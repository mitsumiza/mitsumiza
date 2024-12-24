<?php
require_once '../.core/modules.php'; // Подключаем файл с основными модулями

// Проверка наличия файла tablemodule.php
$product_file = '../.core/tablemodule.php';
if (!file_exists($product_file)) {
    die("Ошибка: файл tablemodule не найден."); // Завершаем выполнение, если файл не найден
}
require_once $product_file; // Подключаем файл с классом Product

// Проверка, что объект $pdo определен
if (!isset($pdo)) {
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php."); // Завершаем выполнение, если PDO не инициализирован
}

// Убедитесь, что класс Product существует
if (!class_exists('Product')) {
    die("Ошибка: класс Product не найден. Проверьте файл tablemodule.php."); // Завершаем выполнение, если класс не найден
}

// Создание экземпляра класса Product
$product = new Product($pdo); // Передаем объект PDO в конструктор класса Product

// Убедитесь, что метод getFullList существует
if (!method_exists($product, 'getFullList')) {
    die("Ошибка: метод getFullList не найден в классе Product."); // Завершаем выполнение, если метод не найден
}

// Получение данных из БД
$products = $product->getFullList(); // Получаем полный список товаров

if ($products === false) {
    echo "Ошибка при получении списка товаров."; // Сообщение об ошибке при получении данных
    exit; // Завершаем выполнение скрипта
}

require_once '../templates/header.php'; // Подключаем заголовок страницы
?>

<main class="container">
    <h1>Список товаров</h1>
    <a href="add.php" class="button add" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Добавить товар</a>
    
    <?php if (!empty($products)): ?> <!-- Проверка на наличие товаров -->
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
            <?php foreach ($products as $item): ?> <!-- Перебор списка товаров -->
                <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td> <!-- Вывод ID товара -->
                    <td>
                        <?php 
                        // Формируем путь к изображению
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/image/catalog_images/' . $item['img_path'];
                        if (!empty($item['img_path']) && $item['img_path'] !== 'no_img.png' && file_exists($imagePath)): ?>
                            <img src="<?php echo htmlspecialchars('/image/catalog_images/' . $item['img_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image"> <!-- Вывод изображения товара -->
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars('/image/catalog_images/no_img.png'); ?>" alt="Нет изображения" class="product-image"> <!-- Вывод изображения по умолчанию -->
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td> <!-- Вывод названия товара -->
                    <td><?php echo htmlspecialchars($item['types_name'] ?? ''); ?></td> <!-- Вывод типа товара -->
                    <td><?php echo htmlspecialchars($item['description']); ?></td> <!-- Вывод описания товара -->
                    <td><?php echo htmlspecialchars($item['cost']); ?> руб.</td> <!-- Вывод цены товара -->
                    <td>
                        <a href="edit.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="button edit" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Редактировать</a> <!-- Ссылка на редактирование товара -->
                        <form action="delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>"> <!-- Скрытое поле с ID товара для удаления -->
                            <button type="submit" class="button delete" style="display: inline-block; padding: 10px 20px; background-color: white; color: black; border: 2px solid black; text-decoration: none;">Удалить</button> <!-- Кнопка для удаления товара -->
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет товаров для отображения.</p> <!-- Сообщение, если товаров нет -->
    <?php endif; ?>
</main>

<?php require_once '../templates/footer.php'; // Подключаем файл с подвалом ?>
</body>
</html>

