<?php
// Включение отображения ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к базе данных
try {
    $pdo = new PDO("mysql:host=localhost;dbname=winter_tourism", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ОШИБКА: Не удалось подключиться: " . $e->getMessage());
}

// Получение типов товара из базы данных
$types = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM types");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $types[] = $row;
    }
} catch (PDOException $e) {
    die("Ошибка получения типов товара: " . $e->getMessage());
}

// Проверка, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $id_type = intval($_POST['id_type']);
    $description = trim($_POST['description']);
    $cost = floatval($_POST['cost']);
    $imgPath = null;

    // Проверка обязательных полей
    if (empty($name) || empty($id_type) || empty($cost)) {
        $error = "Все поля обязательны для заполнения. Цена должна быть числом.";
    } else {
        // Обработка загрузки изображения
        if (!empty($_FILES['img']['name'])) {
            $uploadDir = 'product_images/';
            $originalName = basename($_FILES['img']['name']);
            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Проверка расширения файла
            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                $error = "Допустимы только файлы формата JPG, JPEG, PNG или GIF.";
            } elseif ($_FILES['img']['size'] > 5 * 1024 * 1024) {
                $error = "Размер файла не должен превышать 5MB.";
            } else {
                $uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $imgPath = $uploadDir . $uniqueName;
                if (!move_uploaded_file($_FILES['img']['tmp_name'], $imgPath)) {
                    $error = "Ошибка загрузки файла.";
                } else {
                    $imgPath = basename($imgPath);
                }
            }
        } else {
            $imgPath = 'no_img.png'; // Путь к изображению по умолчанию
        }

        // Добавление товара в базу данных
        if (!isset($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, id_type, description, cost, img_path) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $id_type, $description, $cost, $imgPath])) {
                    echo "<p>Товар успешно добавлен.</p>";
                } else {
                    $error = "Ошибка добавления товара. Проверьте введённые данные.";
                }
            } catch (PDOException $e) {
                $error = "Ошибка добавления товара: " . $e->getMessage();
            }
        }
    }

    // Вывод ошибки, если она есть
    if (isset($error)) {
        echo "<p>Ошибка: " . htmlspecialchars($error) . "</p>";
    }
}

// Подключение header.php
$headerPath = '../templates/header.php';
if (file_exists($headerPath)) {
    require $headerPath; 
} else {
    die("Ошибка: Не удалось подключить файл header.php. Проверьте путь.");
}
?>

<body>
    <div class="container">
        <h1>Добавить товар</h1>
        <form action="add.php" method="post" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="name">Название товара:</label>
                <input type="text" name="name" id="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="id_type">Тип товара:</label>
                <select name="id_type" id="id_type" required class="form-control">
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="cost">Цена:</label>
                <input type="number" name="cost" id="cost" required step="0.01" class="form-control">
            </div>
            <div class="form-group">
                <label for="img">Изображение:</label>
                <input type="file" name="img" id="img" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Добавить товар</button>
        </form>
    </div>
</body>

<?php
// Подключение footer.php
$footerPath = '../templates/footer.php';
if (file_exists($footerPath)) {
    require $footerPath; 
} else {
    die("Ошибка: Не удалось подключить файл footer.php. Проверьте путь.");
}
?>
