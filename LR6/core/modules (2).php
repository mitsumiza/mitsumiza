<?php
session_start(); // Стартуем сессии, чтобы отслеживать пользователя

// Подключаем файл с классом Product
require 'tablemodule.php';

// Подключение к базе данных
try {
    $pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    exit;
}

// Создаем экземпляр класса Product
$tableModule = new Product($pdo); // Убедитесь, что название класса правильное

function getFullList($pdo)
{
    $sql = "SELECT  p.id,
                    p.img_path,
                    p.name,
                    t.name AS type_name,
                    p.description,
                    p.cost
            FROM products p
            JOIN types t ON p.id_type = t.id";
    $arBinds = [];
    
    if (!key_exists('clearFilter', $_GET) && count($_GET) > 0) {
        $whereParts = []; // Массив для хранения частей условия WHERE

        if (!empty($_GET['name'])) {
            $whereParts[] = "p.name LIKE :name";
            $arBinds['name'] = '%' . $_GET['name'] . '%';
        }

        if (!empty($_GET['type_name'])) {
            $whereParts[] = "t.id = :type_id";
            // Подзапрос для получения id типа по имени
            $stmt = $pdo->prepare("SELECT id FROM types WHERE name = :type_name_for_id");
            $stmt->execute(['type_name_for_id' => $_GET['type_name']]);
            $type_id_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($type_id_data) {
                $arBinds['type_id'] = $type_id_data['id'];
            } else {
                return []; // Если такого типа нет, возвращаем пустой массив
            }
        }

        if (!empty($_GET['description'])) {
            $whereParts[] = "p.description LIKE :description";
            $arBinds['description'] = '%' . $_GET['description'] . '%';
        }

        if (!empty($_GET['cost'])) {
            $whereParts[] = "p.cost = :cost";
            $arBinds['cost'] = $_GET['cost'];
        }

        if (!empty($whereParts)) { // Проверяем, есть ли части условия
            $sql .= " WHERE " . implode(" AND ", $whereParts); // Соединяем части условия с помощью AND
        }
    }

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($arBinds);

    if ($result) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Обработка ошибки выполнения запроса
        echo "Ошибка выполнения запроса: ";
        print_r($stmt->errorInfo()); // Выводим информацию об ошибке
        return false;
    }
}

function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}