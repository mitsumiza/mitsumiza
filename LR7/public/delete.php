<?php
require_once '../.core/modules.php'; // Подключаем файл с основными модулями
require_once '../.core/tablemodule.php'; // Подключаем файл с классом Product

// Проверяем, что объект $pdo определен
if (!isset($pdo)) {
    // Если объект $pdo не определен, выводим сообщение об ошибке и завершаем выполнение
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php.");
}

// Проверяем, что запрос выполнен методом POST и ID товара передан
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $productId = $_POST['id']; // Получаем ID товара из POST-запроса

    // Создаем экземпляр класса Product
    $product = new Product($pdo); // Передаем объект PDO в конструктор класса Product

    try {
        // Удаляем товар с указанным ID
        $product->deleteProduct($productId);
        // Перенаправляем на страницу списка товаров после успешного удаления
        header("Location: list.php"); // Замените на нужный вам файл
        exit; // Завершаем выполнение скрипта
    } catch (Exception $e) {
        // Обработка ошибки при удалении товара
        echo "Ошибка: " . htmlspecialchars($e->getMessage()); // Выводим сообщение об ошибке
    }
} else {
    // Если ID не передан, выводим сообщение об ошибке
    echo "Ошибка: ID товара не передан."; // Сообщение о том, что ID товара не был передан
}
?>
