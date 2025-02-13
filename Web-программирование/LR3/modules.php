<?php

function getFullProductList($pdo) {
    $sql = "SELECT p.id, p.img_path, p.name, p.description, p.cost, t.name AS type_name
            FROM products p
            JOIN types t ON p.id_type = t.id";
    $arBinds = [];

    if (!isset($_GET['clearFilter'])) { // Используем isset для проверки существования ключа
        $whereParts = [];
        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $whereParts[] = "p.name LIKE :name";
            $arBinds['name'] = '%' . $_GET['name'] . '%';
        }
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $whereParts[] = "p.id_type = :type";
            $arBinds['type'] = $_GET['type'];
        }
        if (isset($_GET['cost_min']) && is_numeric($_GET['cost_min'])) {
            $whereParts[] = "p.cost >= :cost_min";
            $arBinds['cost_min'] = $_GET['cost_min'];
        }
        if (isset($_GET['cost_max']) && is_numeric($_GET['cost_max'])) {
            $whereParts[] = "p.cost <= :cost_max";
            $arBinds['cost_max'] = $_GET['cost_max'];
        }

        if (!empty($whereParts)) {
            $sql .= " WHERE " . implode(" AND ", $whereParts);
        }
    }

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute($arBinds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка выполнения запроса: " . $e->getMessage());
        return false;
    }
}



function addProduct($pdo, $name, $id_type, $description, $cost, $img_path = null): array {
    $errors = []; // Array to store error messages

    // Input validation
    if (empty($name)) {
        $errors[] = "Поле 'Название' не может быть пустым.";
    }
    if (empty($description)) {
        $errors[] = "Поле 'Описание' не может быть пустым.";
    }
    if (empty($cost) || !is_numeric($cost) || $cost <= 0) {
        $errors[] = "Поле 'Цена' должно быть числом больше нуля.";
    }
    if (empty($id_type) || !is_numeric($id_type)) {
        $errors[] = "Поле 'ID типа' должно быть числом.";
    }


    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors]; // Return errors
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, id_type, description, cost, img_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $id_type, $description, $cost, $img_path]);
        return ['success' => true, 'id' => $pdo->lastInsertId()]; // Return success and ID
    } catch (PDOException $e) {
        error_log("Ошибка добавления товара: " . $e->getMessage());
        return ['success' => false, 'errors' => ["Произошла ошибка при добавлении товара в базу данных."]];
    }
}
function dbConnect() {
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "winter_tourism";    

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        error_log("Ошибка подключения к сети: " . $e->getMessage());
        return false; 
    }
}

function setFlashMessage($type, $message) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function getFlashMessage() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

function getOptionGroups($pdo)
{
    $sql = "SELECT id, name FROM types"; 

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Более надежная обработка ошибок с использованием try-catch
        error_log("Ошибка выполнения запроса: " . $e->getMessage()); // Запись ошибки в лог
        return false; // Возвращаем false в случае ошибки
    }
}



?>




