<?php

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1); // Включаем отображение ошибок
ini_set('display_startup_errors', 1); // Включаем отображение ошибок при старте
error_reporting(E_ALL); // Устанавливаем уровень отчетности об ошибках на все

// Подключаем файл с классом Product
require 'tablemodule.php'; // Убедитесь, что путь к файлу правильный

// Подключение к базе данных
try {
    // Создаем объект PDO для подключения к базе данных
    $pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch (PDOException $e) {
    // Обработка ошибки подключения к базе данных
    echo "Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage());
    exit; // Завершаем выполнение скрипта при ошибке
}

// Создаем экземпляр класса Product
$tableModule = new Product($pdo); // Передаем объект PDO в конструктор класса Product

// Проверяем, передан ли ID товара
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Приводим переданный ID к целому числу

    // Удаляем товар
    if ($tableModule->deleteProduct($id)) {
        // Если товар успешно удален
        $_SESSION['flash_message'] = [
            'type' => 'success', // Тип сообщения - успех
            'message' => 'Товар успешно удален.' // Сообщение об успешном удалении
        ];
    } else {
        // Если произошла ошибка при удалении
        $_SESSION['flash_message'] = [
            'type' => 'error', // Тип сообщения - ошибка
            'message' => 'Ошибка при удалении товара.' // Сообщение об ошибке
        ];
    }
} else {
    // Если ID товара не указан
    $_SESSION['flash_message'] = [
        'type' => 'error', // Тип сообщения - ошибка
        'message' => 'ID товара не указан.' // Сообщение о том, что ID не был передан
    ];
}

// Перенаправляем обратно на страницу с товарами
header('Location: list.php'); // Перенаправление на страницу со списком товаров
exit; // Завершаем выполнение скрипта
?>
