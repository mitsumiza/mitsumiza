<?php

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "winter_tourism";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение категорий товаров
$sql_categories = "SELECT DISTINCT category FROM products"; 
$result_categories = $conn->query($sql_categories);
$categories = array();
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Фильтрация результатов поиска
$where_clauses = [];

// Защита от SQL-инъекций
if (isset($_GET['name'])) {
    $name = htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
    $where_clauses[] = "name LIKE ?";
    $name_param = $conn->real_escape_string($name); // Безопасное экранирование
}

if (isset($_GET['category'])) {
    $category = htmlspecialchars($_GET['category'], ENT_QUOTES, 'UTF-8');
    $where_clauses[] = "category = ?";
    $category_param = $conn->real_escape_string($category); // Безопасное экранирование
}

if (isset($_GET['price_min'])) {
    $price_min = intval($_GET['price_min']);
    $where_clauses[] = "cost >= ?";
    $price_min_param = $price_min;
}

if (isset($_GET['price_max'])) {
    $price_max = intval($_GET['price_max']);
    $where_clauses[] = "cost <= ?";
    $price_max_param = $price_max;
}

$where_clause = implode(" AND ", $where_clauses);

// Подготовка запроса
$sql = "SELECT * FROM products " . ($where_clause ? "WHERE $where_clause" : ""); 
$stmt = $conn->prepare($sql);

// Привязка параметров
if (isset($name_param)) {
    $stmt->bind_param("s", $name_param);
}
if (isset($category_param)) {
    $stmt->bind_param("s", $category_param);
}
if (isset($price_min_param)) {
    $stmt->bind_param("i", $price_min_param);
}
if (isset($price_max_param)) {
    $stmt->bind_param("i", $price_max_param);
}

// Выполнение запроса
$stmt->execute();
$result = $stmt->get_result();

// Вывод результатов поиска
if ($result->num_rows > 0) {
    echo "<h2>Результаты поиска:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<b>Имя:</b> " . $row["name"] . "<br>";
        echo "<b>Категория:</b> " . $row["category"] . "<br>";
        echo "<b>Цена:</b> " . $row["cost"] . "<br>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Товаров не найдено.";
}

// Закрытие соединения с базой данных
$conn->close();

?>
