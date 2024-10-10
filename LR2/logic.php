<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root"; 
$password = "1"; 
$dbname = "winter_tourism";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных о товарах
$sql = "SELECT p.*, t.name as type_name FROM products p JOIN types t ON p.id_type = t.id";
$result = $conn->query($sql);

// Получение названий товаров
$sql_names = "SELECT DISTINCT name FROM products";
$result_names = $conn->query($sql_names);
$productNames = array();
while ($row = $result_names->fetch_assoc()) {
    $productNames[] = $row['name'];
}

// Получение категорий товаров
$sql_categories = "SELECT DISTINCT name FROM types";
$result_categories = $conn->query($sql_categories);
$categories = array();
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row['name'];
}

// Фильтрация результатов поиска
if (isset($_GET['name']) || isset($_GET['type']) || isset($_GET['price_min']) || isset($_GET['price_max'])) {
    $where_clauses = array();

    if (isset($_GET['name'])) {
        $where_clauses[] = "p.name LIKE '%" . $_GET['name'] . "%'";
    }

    if (isset($_GET['type'])) {
        $where_clauses[] = "t.name = '" . $_GET['type'] . "'";
    }

    if (isset($_GET['price_min'])) {
        $where_clauses[] = "p.cost >= " . $_GET['price_min'];
    }

    if (isset($_GET['price_max'])) {
        $where_clauses[] = "p.cost <= " . $_GET['price_max'];
    }

    $where_clause = implode(" AND ", $where_clauses);

    $sql = "SELECT p.*, t.name as type_name FROM products p JOIN types t ON p.id_type = t.id WHERE " . $where_clause;
    $result = $conn->query($sql);
}

// Преобразование массива в JSON
$productNamesJSON = json_encode($productNames);

// Закрытие соединения с базой данных
$conn->close();

?>

