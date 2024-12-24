<?php

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаем файл с классом Product
require 'tablemodule.php'; // Убедитесь, что путь к файлу правильный

// Подключение к базе данных
try {
    $pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage());
    exit;
}

// Создаем экземпляр класса Product
$tableModule = new Product($pdo);

// Проверяем, передан ли ID товара
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Приводим к целому числу

    // Удаляем товар
    if ($tableModule->deleteProduct($id)) {
        // Успешное удаление
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Товар успешно удален.'
        ];
    } else {
        // Ошибка при удалении
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Ошибка при удалении товара.'
        ];
    }
} else {
    // Если ID товара не указан
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'ID товара не указан.'
    ];
}

// Перенаправляем обратно на страницу с товарами
header('Location: list.php'); // Замените на нужный вам файл
exit;
?>
