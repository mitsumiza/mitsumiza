<?php

// Подключение к базе данных
$servername = "localhost";
$username = "root"; 
$password = " "; 
$dbname = "winter_tourism";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных о товарах
$sql = "SELECT * FROM products"; // Замените "products" на имя вашей таблицы товаров
$result = $conn->query($sql);

// Получение названий товаров
$sql_names = "SELECT name FROM products"; // Замените "products" на имя вашей таблицы товаров
$result_names = $conn->query($sql_names);
$productNames = array();
while ($row = $result_names->fetch_assoc()) {
    $productNames[] = $row['name'];
}

// Получение категорий товаров
$sql_categories = "SELECT DISTINCT category FROM products"; // Замените "products" на имя вашей таблицы товаров
$result_categories = $conn->query($sql_categories);
$categories = array();
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Фильтрация результатов поиска
if (isset($_GET['name']) || isset($_GET['category']) || isset($_GET['price_min']) || isset($_GET['price_max'])) {
    $where_clauses = array();

    if (isset($_GET['name'])) {
        $where_clauses[] = "name LIKE '%" . $_GET['name'] . "%'";
    }

    if (isset($_GET['category'])) {
        $where_clauses[] = "category = '" . $_GET['category'] . "'";
    }

    if (isset($_GET['price_min'])) {
        $where_clauses[] = "cost >= " . $_GET['price_min'];
    }

    if (isset($_GET['price_max'])) {
        $where_clauses[] = "cost <= " . $_GET['price_max'];
    }

    $where_clause = implode(" AND ", $where_clauses);

    $sql = "SELECT * FROM products WHERE " . $where_clause; // Замените "products" на имя вашей таблицы товаров
    $result = $conn->query($sql);
}

// Закрытие соединения с базой данных
$conn->close();

?>

