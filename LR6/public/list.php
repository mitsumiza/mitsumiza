<?php
// Подключаем необходимые модули
require_once '../.core/modules.php';

// Проверяем наличие файла tablemodule.php
$product_file = '../.core/tablemodule.php';
if (!file_exists($product_file)) {
    die("Ошибка: файл tablemodule не найден."); // Выводим сообщение об ошибке, если файл не найден
}
require_once $product_file; // Подключаем файл tablemodule.php

// Проверяем, что объект $pdo определен
if (!isset($pdo)) {
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php."); // Сообщение об ошибке, если PDO не инициализирован
}

// Создаем экземпляр класса Product, передавая объект PDO
$product = new Product($pdo);

// Получаем данные из базы данных без фильтрации
$products = $product->getFullList(); 

// Проверяем, успешно ли получен список товаров
if ($products === false) {
    echo "Ошибка при получении списка товаров."; // Сообщение об ошибке, если получение списка не удалось
    exit; // Завершаем выполнение скрипта
}
?>

<?php require_once '../templates/header.php'; // Подключаем файл заголовка ?>
    
<main class="container">
    <h1>Список товаров</h1>

    <!-- Кнопка для добавления товара -->
    <a href="add.php" class="button add" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Добавить товар</a>
    
    <?php if (!empty($products)): // Проверяем, есть ли товары для отображения ?>
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
            <?php foreach ($products as $item): // Перебираем список товаров ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td> <!-- Выводим ID товара -->
                    <td>
                        <?php 
                        // Формируем полный путь к изображению
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/image/catalog_images/' . $item['img_path'];
                        // Проверяем, существует ли изображение
                        if (!empty($item['img_path']) && $item['img_path'] !== 'no_img.png' && file_exists($imagePath)): ?>
                            <img src="<?php echo htmlspecialchars('/image/catalog_images/' . $item['img_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image"> <!-- Выводим изображение товара -->
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars('/image/catalog_images/no_img.png'); ?>" alt="Нет изображения" class="product-image"> <!-- Изображение по умолчанию -->
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td> <!-- Выводим название товара -->
                    <td><?php echo htmlspecialchars($item['type_name']); ?></td> <!-- Выводим тип товара -->
                    <td><?php echo htmlspecialchars($item['description']); ?></td> <!-- Выводим описание товара -->
                    <td><?php echo htmlspecialchars($item['cost']); ?></td> <!-- Выводим цену товара -->
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет товаров для отображения.</p> <!-- Сообщение, если нет товаров -->
    <?php endif; ?>
</main>

<?php require_once '../templates/footer.php'; // Подключаем файл подвала ?>
</body>
</html>
