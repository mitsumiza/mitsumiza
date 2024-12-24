<?php
require_once '../.core/tablemodule.php'; // Убедитесь, что путь правильный
require_once '../.core/modules.php';

// Убедитесь, что $pdo инициализирован
if (!isset($pdo)) {
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php.");
}

// Получаем ID товара из URL
$id = intval($_GET['id'] ?? 0);

// Создаем экземпляр TableModule
$tableModule = new Product($pdo);

// Получаем продукт по ID
$product = $tableModule->getProductById($id);
if (!$product) {
    die("Товар не найден.");
}

// Получение списка типов товаров
$types = getOptionGroups($pdo);

// Обработка формы
$error = null; // Инициализация переменной ошибки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = trim($_POST['name']);
    $id_type = intval($_POST['id_type']);
    $description = trim($_POST['description']);
    $cost = floatval($_POST['cost']);
    $imgPath = $product['img_path'] ?? 'no_img.png';

    // Проверка обязательных полей
    if (empty($name) || empty($id_type) || empty($cost) || !is_numeric($cost)) {
        $error = "Все поля обязательны для заполнения. Цена должна быть числом.";
    } else {
        // Проверка на существование типа товара
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM types WHERE id = ?");
        $stmt->execute([$id_type]);
        $typeCount = $stmt->fetchColumn();
        if ($typeCount == 0) {
            $error = "Выбранного типа товара не существует.";
        } else {
            // Обработка загрузки нового изображения
            if (!empty($_FILES['img']['name'])) {
                $uploadDir = '../imsge/catalog_images/';
                $originalName = basename($_FILES['img']['name']);
                $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = "Допустимы только файлы формата JPG, JPEG, PNG или GIF.";
                } elseif ($_FILES['img']['size'] > 5 * 1024 * 1024) {
                    $error = "Размер файла не должен превышать 5MB.";
                } else {
                    $uniqueName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                    $newImgPath = $uploadDir . $uniqueName;
                    if (move_uploaded_file($_FILES['img']['tmp_name'], $newImgPath)) {
                        // Удаляем старое изображение, если оно не является дефолтным
                        if ($product['img_path'] !== 'no_img.png' && file_exists($uploadDir . $product['img_path'])) {
                            unlink($uploadDir . $product['img_path']);
                        }
                        $imgPath = basename($newImgPath);
                    } else {
                        $error = "Ошибка загрузки файла.";
                    }
                }
            }

            // Обновляем запись в базе данных, если нет ошибок
            if (!isset($error)) {
                $updateResult = $tableModule->updateProduct($id, $name, $description, $cost, $id_type, $imgPath);
                if ($updateResult) {
                    setFlashMessage('success', 'Товар ID ' . $id . ' успешно обновлен.');
                    header('Location: list.php');
                    exit;
                } else {
                    $error = "Ошибка обновления товара.";
                }
            }
        }
    }
}

require '../templates/header.php';
?>

<div class="container">
    <h1 class="mt-4">Редактирование товара</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
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

<?php require '../templates/footer.php'; ?>
