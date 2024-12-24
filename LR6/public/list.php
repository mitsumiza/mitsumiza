<?php
require_once '../.core/modules.php';

// Проверяем наличие tablemodule.php
$product_file = '../.core/tablemodule.php';
if (!file_exists($product_file)) {
    die("Ошибка: файл tablemodule не найден.");
}
require_once $product_file;

// Проверяем, что объект $pdo определен
if (!isset($pdo)) {
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php.");
}

// Создаем экземпляр класса Product
$product = new Product($pdo);

// Получаем данные из БД без фильтрации
$products = $product->getFullList(); 

if ($products === false) {
    echo "Ошибка при получении списка товаров.";
    exit;
}

?>

    <?php require_once '../templates/header.php'; ?>
    
    <main class="container">
        <h1>Список товаров</h1>

        <!-- Кнопка для добавления товара -->
        <a href="add.php" class="button add" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Добавить товар</a>
        <?php if (!empty($products)): ?>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Изображение</th>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Описание</th>
                        <th>Цена</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td>
                            <?php 
                            // Формируем полный путь к изображению
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/image/catalog_images/' . $item['img_path'];
                            if (!empty($item['img_path']) && $item['img_path'] !== 'no_img.png' && file_exists($imagePath)): ?>
                                <img src="<?php echo htmlspecialchars('/image/catalog_images/' . $item['img_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars('/image/catalog_images/no_img.png'); ?>" alt="Нет изображения" class="product-image">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td><?php echo htmlspecialchars($item['cost']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Нет товаров для отображения.</p>
        <?php endif; ?>
    </main>

    <?php require_once '../templates/footer.php'; ?>
</body>
</html>
