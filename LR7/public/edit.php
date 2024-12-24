<?php
require_once '../.core/tablemodule.php'; // Подключаем файл с классом Product
require_once '../.core/modules.php'; // Подключаем файл с основными модулями

// Убедитесь, что $pdo инициализирован
if (!isset($pdo)) {
    // Если объект $pdo не определен, выводим сообщение об ошибке и завершаем выполнение
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php.");
}

// Получаем ID товара из URL, если он передан, иначе устанавливаем 0
$id = intval($_GET['id'] ?? 0);

// Создаем экземпляр TableModule
$tableModule = new Product($pdo); // Передаем объект PDO в конструктор класса Product

// Получаем продукт по ID
$product = $tableModule->getProductById($id);
if (!$product) {
    // Если товар не найден, выводим сообщение об ошибке и завершаем выполнение
    die("Товар не найден.");
}

// Получение списка типов товаров
$types = getOptionGroups($pdo); // Получаем доступные типы товаров

// Обработка формы
$error = null; // Инициализация переменной ошибки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = trim($_POST['name']); // Название товара
    $id_type = intval($_POST['id_type']); // ID типа товара
    $description = trim($_POST['description']); // Описание товара
    $cost = floatval($_POST['cost']); // Цена товара
    $imgPath = $product['img_path'] ?? 'no_img.png'; // Путь к изображению товара

    // Проверка обязательных полей
    if (empty($name) || empty($id_type) || empty($cost) || !is_numeric($cost)) {
        $error = "Все поля обязательны для заполнения. Цена должна быть числом."; // Сообщение об ошибке
    } else {
        // Проверка на существование типа товара
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM types WHERE id = ?");
        $stmt->execute([$id_type]);
        $typeCount = $stmt->fetchColumn();
        if ($typeCount == 0) {
            $error = "Выбранного типа товара не существует."; // Сообщение об ошибке
        } else {
            // Обработка загрузки нового изображения
            if (!empty($_FILES['img']['name'])) {
                $uploadDir = '../imsge/catalog_images/'; // Директория для загрузки изображений
                $originalName = basename($_FILES['img']['name']); // Оригинальное имя файла
                $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION)); // Расширение файла
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Допустимые расширения

                // Проверка на допустимые расширения и размер файла
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = "Допустимы только файлы формата JPG, JPEG, PNG или GIF."; // Сообщение об ошибке
                } elseif ($_FILES['img']['size'] > 5 * 1024 * 1024) {
                    $error = "Размер файла не должен превышать 5MB."; // Сообщение об ошибке
                } else {
                    // Генерация уникального имени для файла
                    $uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                    $newImgPath = $uploadDir . $uniqueName; // Новый путь к изображению
                    if (move_uploaded_file($_FILES['img']['tmp_name'], $newImgPath)) {
                        // Удаляем старое изображение, если оно не является дефолтным
                        if ($product['img_path'] !== 'no_img.png' && file_exists($uploadDir . $product['img_path'])) {
                            unlink($uploadDir . $product['img_path']); // Удаляем старое изображение
                        }
                        $imgPath = basename($newImgPath); // Обновляем путь к изображению
                    } else {
                        $error = "Ошибка загрузки файла."; // Сообщение об ошибке
                    }
                }
            }

            // Обновляем запись в базе данных, если нет ошибок
            if (!isset($error)) {
                $updateResult = $tableModule->updateProduct($id, $name, $description, $cost, $id_type, $imgPath);
                if ($updateResult) {
                    // Устанавливаем сообщение об успешном обновлении и перенаправляем на список товаров
                    setFlashMessage('success', 'Товар ID ' . $id . ' успешно обновлен.');
                    header('Location: list.php'); // Перенаправление на страницу списка товаров
                    exit; // Завершаем выполнение скрипта
                } else {
                    $error = "Ошибка обновления товара."; // Сообщение об ошибке
                }
            }
        }
    }
}

// Подключаем заголовок страницы
require '../templates/header.php';
?>

<div class="container">
    <h1 class="mt-4">Редактирование товара</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <!-- Выводим сообщение об ошибке -->
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data"> <!-- Форма для редактирования товара -->
        <div class="form-group">
            <label for="name">Название товара*</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="id_type">Тип товара*</label>
            <select id="id_type" name="id_type" class="form-control" required>
                <option value="">Выберите тип товара</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?php echo htmlspecialchars($type['id']); ?>" <?php echo ($type['id'] == $product['id_type']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="cost">Цена*</label>
            <input type="number" id="cost" name="cost" class="form-control" value="<?php echo htmlspecialchars($product['cost']); ?>" required>
        </div>

        <div class="form-group">
            <label for="img">Фото товара</label>
            <input type="file" id="img" name="img" class="form-control-file">
            <small class="form-text text-muted">Файл не выбран. Пожалуйста, выберите допустимый файл изображения.</small>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
</div>

<?php require '../templates/footer.php'; // Подключаем файл с подвалом ?>
