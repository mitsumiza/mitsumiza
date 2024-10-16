<?php

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$database = "winter_tourism";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

// Получение списка типов товаров
$sql_types = "SELECT DISTINCT name FROM types";
$result_types = $connection->query($sql_types);
$types = [];
if ($result_types) {
    while ($row = $result_types->fetch_assoc()) {
        $types[] = $row['name'];
    }
}

// Базовый SQL-запрос для выборки товаров
$sql = "SELECT products.img_path, products.name, products.cost, types.name AS type_name, products.description, products.id
   FROM products 
   INNER JOIN types ON products.id_type = types.id";

// Обработка фильтра
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Очистка фильтра
    if (isset($_GET['clearFilter'])) {
        $name = '';
        $type = '';
        $price_min = '';
        $price_max = '';
    } else {
        $name = $_GET["name"] ?? "";
        $type = $_GET["type"] ?? "";
        $price_min = $_GET["price_min"] ?? "";
        $price_max = $_GET["price_max"] ?? "";
    }

    // Проверка на наличие фильтра
    if (!empty($name) || !empty($type) || !empty($price_min) || !empty($price_max)) {
        $whereConditions = [];

        // Добавление условий WHERE для фильтрации
        if (!empty($name)) {
            $whereConditions[] = "products.name LIKE '%" . mysqli_real_escape_string($connection, $name) . "%'"; 
        }
        if (!empty($type)) {
            $whereConditions[] = "types.name = '" . mysqli_real_escape_string($connection, $type) . "'";
        }
        if (!empty($price_min) && is_numeric($price_min)) {
            $whereConditions[] = "products.cost >= " . $price_min; 
        }
        if (!empty($price_max) && is_numeric($price_max)) {
            $whereConditions[] = "products.cost <= " . $price_max;
        }

        // Добавление условий WHERE к SQL-запросу
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }
    }

    // Выполнение запроса с учетом фильтра
    $result = $connection->query($sql);
    if (!$result) {
        die("Invalid query: " . $connection->error);
    }
} else {
    // Выполнение базового запроса без фильтра
    $result = $connection->query($sql);
    if (!$result) {
        die("Invalid query: " . $connection->error);
    }
}

// Передача данных в глобальную область видимости
global $types, $result;

// Дальнейшая обработка данных (например, вывод на страницу)
// ... 

// Закрытие соединения с базой данных
$connection->close();

?>
