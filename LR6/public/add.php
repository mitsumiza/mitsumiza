<?php
// Включение отображения ошибок для отладки
error_reporting(E_ALL); // Отображаем все ошибки
ini_set('display_errors', 1); // Включаем вывод ошибок на экран

// Подключение к базе данных
try {
    // Создаем объект PDO для подключения к базе данных
    $pdo = new PDO("mysql:host=localhost;dbname=winter_tourism", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch (PDOException $e) {
    // Если не удалось подключиться, выводим сообщение об ошибке и завершаем выполнение
    die("ОШИБКА: Не удалось подключиться: " . $e->getMessage());
}

// Получение типов товара из базы данных
$types = []; // Массив для хранения типов товара
try {
    // Выполняем запрос для получения всех типов товара
    $stmt = $pdo->query("SELECT id, name FROM types");
    // Извлекаем результаты и добавляем их в массив $types
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $types[] = $row;
    }
} catch (PDOException $e) {
    // Обработка ошибок получения типов товара
    die("Ошибка получения типов товара: " . $e->getMessage());
}

// Проверка, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы и их предварительная обработка
    $name = trim($_POST['name']); // Название товара
    $id_type = intval($_POST['id_type']); // ID типа товара
    $description = trim($_POST['description']); // Описание товара
    $cost = floatval($_POST['cost']); // Цена товара
    $imgPath = null; // Путь к изображению товара

    // Проверка обязательных полей
    if (empty($name) || empty($id_type) || empty($cost)) {
        $error = "Все поля обязательны для заполнения. Цена должна быть числом."; // Сообщение об ошибке
    } else {
        // Обработка загрузки изображения
        if (!empty($_FILES['img']['name'])) {
            $uploadDir = 'product_images/'; // Директория для загрузки изображений
            $originalName = basename($_FILES['img']['name']); // Исходное имя файла
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION); // Расширение файла
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Допустимые форматы изображений

            // Проверка расширения файла
            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                $error = "Допустимы только файлы формата JPG, JPEG, PNG или GIF."; // Сообщение об ошибке
            } elseif ($_FILES['img']['size'] > 5 * 1024 * 1024) { // Проверка размера файла
                $error = "Размер файла не должен превышать 5MB."; // Сообщение об ошибке
            } else {
                // Генерация уникального имени файла для предотвращения конфликтов
                $uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $imgPath = $uploadDir . $uniqueName; // Полный путь к загружаемому изображению
                // Перемещение загруженного файла в целевую директорию
                if (!move_uploaded_file($_FILES['img']['tmp_name'], $imgPath)) {
                    $error = "Ошибка загрузки файла."; // Сообщение об ошибке
                } else {
                    $imgPath = basename($imgPath); // Сохраняем только имя файла
                }
            }
        } else {
            $imgPath = 'no_img.png'; // Устанавливаем изображение по умолчанию, если файл не загружен
        }

        // Добавление товара в базу данных
        if (!isset($error)) {
            try {
                // Подготавливаем SQL-запрос для вставки нового товара
                $stmt = $pdo->prepare("INSERT INTO products (name, id_type, description, cost, img_path) VALUES (?, ?, ?, ?, ?)");
                // Выполняем запрос с переданными параметрами
                if ($stmt->execute([$name, $id_type, $description, $cost, $imgPath])) {
                    echo "<p>Товар успешно добавлен.</p>"; // Сообщение об успешном добавлении
                } else {
                    $error = "Ошибка добавления товара. Проверьте введённые данные."; // Сообщение об ошибке
                }
            } catch (PDOException $e) {
                // Обработка ошибок при добавлении товара
                $error = "Ошибка добавления товара: " . $e->getMessage();
            }
        }
    }

    // Вывод ошибки, если она есть
    if (isset($error)) {
        echo "<p>Ошибка: " . htmlspecialchars($error) . "</p>"; // Выводим сообщение об ошибке
    }
}

// Подключение header.php
$headerPath = '../templates/header.php'; // Путь к заголовку
if (file_exists($headerPath)) {
    require $headerPath; // Подключаем файл заголовка
} else {
    die("Ошибка: Не удалось подключить файл header.php. Проверьте путь."); // Сообщение об ошибке
}
?>

<body>
    <div class="container">
        <h1>Добавить товар</h1>
        <form action="add.php" method="post" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="name">Название товара:</label>
                <input type="text" name="name" id="name" required class="form-control"> <!-- Поле для названия товара -->
            </div>
            
            <div class="form-group">
                <label for="id_type">Тип товара:</label>
                <select name="id_type" id="id_type" required class="form-control"> <!-- Выпадающий список для выбора типа товара -->
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option> <!-- Опции для типов товара -->
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea name="description" id="description" class="form-control"></textarea> <!-- Поле для описания товара -->
            </div>
            <div class="form-group">
                <label for="cost">Цена:</label>
                <input type="number" name="cost" id="cost" required step="0.01" class="form-control"> <!-- Поле для цены товара -->
            </div>
            <div class="form-group">
                <label for="img">Изображение:</label>
                <input type="file" name="img" id="img" class="form-control-file"> <!-- Поле для загрузки изображения -->
            </div>
            <button type="submit" class="btn btn-primary">Добавить товар</button> <!-- Кнопка отправки формы -->
        </form>
    </div>
</body>

<?php
// Подключение footer.php
$footerPath = '../templates/footer.php'; // Путь к подвалу
if (file_exists($footerPath)) {
    require $footerPath; // Подключаем файл подвала
} else {
    die("Ошибка: Не удалось подключить файл footer.php. Проверьте путь."); // Сообщение об ошибке
}
?>
