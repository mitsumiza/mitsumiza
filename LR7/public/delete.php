<?php
require_once '../.core/modules.php';
require_once '../.core/tablemodule.php';

// Проверяем, что объект $pdo определен
if (!isset($pdo)) {
    die("Ошибка: PDO не определен. Проверьте подключение к базе данных в .core/modules.php.");
}

// Проверяем, что запрос выполнен методом POST и ID товара передан
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Создаем экземпляр класса Product
    $product = new Product ($pdo);

    try {
        // Удаляем товар
        $product->deleteProduct($productId);
        // Перенаправляем на страницу списка товаров после успешного удаления
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        // Обработка ошибки
        echo "Ошибка: " . htmlspecialchars($e->getMessage());
    }
} else {
    // Если ID не передан, выводим сообщение об ошибке
    echo "Ошибка: ID товара не передан.";
}
