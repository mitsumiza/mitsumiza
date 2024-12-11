<?php

// подключение к бд
function dbConnect() {
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "winter_tourism";    

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        error_log("Ошибка подключения к сети: " . $e->getMessage());
        return false; 
    }
}


// извлечение данных из бд
function fetchData($tableName, $conn) {
  try {
      // запрос для извлечения данных
      $stmt = $conn->query("SELECT * FROM $tableName");
      // извлечение данных в виде массива
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $data;
  } catch(PDOException $e) {
    error_log("Ошибка извлечения данных: " . $e->getMessage());
    return false;
  }
}

// преобразует данные в XML
function generateXML($data, $tableName) {
    // создает новые данные и добавляет дочерние элементы, потом возвращает их в XML строке
    $xml = new SimpleXMLElement("<$tableName/>");
    foreach ($data as $row) {
        $item = $xml->addChild('item');
        foreach ($row as $key => $value) {
            $item->addChild($key, htmlspecialchars($value));
        }
    }
    return $xml->asXML();
}

// сохраняет содержимое в файл
function saveFile($filePath, $content){
    if (file_put_contents($filePath, $content) !== false) {
        return true;
    } else {
        error_log("Error saving file: " . $filePath);
        return false;
    }
}

// загружает XML по ссылке
function importFromURL($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $xmlData = curl_exec($ch);
  curl_close($ch);

  if ($xmlData === false) {
      error_log("Ошибка извлечения XML из URL: $url");
      return false;
  }

  libxml_use_internal_errors(true); // включает внутренние ошибки libxml
  $xml = simplexml_load_string($xmlData);
  if ($xml === false) {
      $errors = libxml_get_errors();
      foreach ($errors as $error) {
          error_log("XML ошибка: " . $error->message);
      }
      libxml_clear_errors();
      return false;
  }
  return $xml;
}

// проверяет валидность
function validateXML($xml) {
  SimpleXMLElement.
  return $xml instanceof SimpleXMLElement; 

}

// импортирует данные из XML-файла в бд
function importData($xml, $tableName, $conn) {
  // удаляем таблицу, если она уже существует  
  $conn->query("DROP TABLE IF EXISTS $tableName"); 
  // создаем новую таблицу, идентичную products
  $conn->query("CREATE TABLE $tableName LIKE products"); 

  $count = 0;
  // запрос для вставки данных
  $stmt = $conn->prepare("INSERT INTO $tableName (img_path, name, id_type, description, cost) VALUES (?, ?, ?, ?, ?)");
    
  foreach ($xml->item as $item) {
    // извлекаем данные из XML и преобразуем в строки
    $imgPath = (string)($item->img_path ?? 'no_img.png');  
    $name = (string)($item->name ?? '');                
    $idType = (int)($item->id_type ?? 0);              
    $description = (string)($item->description ?? '');    
    $cost = (int)($item->cost ?? 0);      

    // выполняем запрос для вставки данных
    $stmt->execute([$drugName, $price, $quantity]); 

    //пПроверяем кол-во добавленных строк
    if ($stmt->rowCount() > 0) {
      $count++;
    } else {
      error_log("Error inserting row: " . $stmt->errorInfo()[2]);
    }
  }
  return $count;
}

?>
