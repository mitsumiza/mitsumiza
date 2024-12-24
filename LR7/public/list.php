<?php
require_once '../.core/modules.php';

// Проверка наличия файла tablemodule.php
$product_file = '../.core/tablemodule.php';
if (!file_exists($product_file)) {
    die("Ошибка: файл tablemodule не найден.");
}
require_once $product_file;

// Проверка, что объект $pdo определен
if (!isset($pdo)) {
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php.");
}

// Убедитесь, что класс Product существует
if (!class_exists('Product')) {
    die("Ошибка: класс Product не найден. Проверьте файл tablemodule.php.");
}

// Создание экземпляра класса Product
$product = new Product($pdo);

// Убедитесь, что метод getFullList существует
if (!method_exists($product, 'getFullList')) {
    die("Ошибка: метод getFullList не найден в классе Product.");
}

// Получение данных из БД
$products = $product->getFullList(); 

if ($products === false) {
    echo "Ошибка при получении списка товаров.";
    exit;
}

require_once '../templates/header.php'; 
?>

<main class="container">
    <h1>Список товаров</h1>
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
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                    <td>
                        <?php 
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/image/catalog_images/' . $item['img_path'];
                        if (!empty($item['img_path']) && $item['img_path'] !== 'no_img.png' && file_exists($imagePath)): ?>
                            <img src="<?php echo htmlspecialchars('/image/catalog_images/' . $item['img_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars('/image/catalog_images/no_img.png'); ?>" alt="Нет изображения" class="product-image">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['types_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td><?php echo htmlspecialchars($item['cost']); ?> руб.</td>
                    <td>
                        <a href="edit.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="button edit" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Редактировать</a>
                        <form action="delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                            <button type="submit" class="button delete" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Удалить</button>
                        </form>
                    </td>
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
