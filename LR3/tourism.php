<?php

require 'header.php';

// Функция для получения списка категорий (используя PDO)
function getOptionGroups($pdo) {
    $sql = "SELECT t.id, t.name FROM types t";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Ошибка выполнения запроса: " . $stmt->errorInfo()[2];
        return false;
    }
}

function getFullList($pdo) {
    $sql = "SELECT p.id, p.img_path, p.name, t.name AS type_name, p.description, p.cost
            FROM products p
            JOIN types t ON p.id_type = t.id"; 
    $arBinds = [];
    if (!key_exists('clearFilter', $_GET)) {
        if (count($_GET) > 0) {
            $whereParts = [];
            if ($_GET['name']) {
                $whereParts[] = "p.name LIKE :name";
                $arBinds['name'] = '%' . $_GET['name'] . '%';
            }
            if ($_GET['type']) {
                $whereParts[] = "p.id_type = :type"; 
                $arBinds['type'] = $_GET['type'];
            }
            if ($_GET['price_min']) {
                $whereParts[] = "p.cost >= :price_min";
                $arBinds['price_min'] = $_GET['price_min'];
            }
            if ($_GET['price_max']) {
                $whereParts[] = "p.cost <= :price_max";
                $arBinds['price_max'] = $_GET['price_max'];
            }
            if (!empty($whereParts)) {
                $sql .= " WHERE " . implode(" AND ", $whereParts);
            }
        }
    }

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($arBinds)) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo '<pre>';
        echo "Ошибка выполнения запроса: " . $stmt->errorInfo()[2];
        echo '</pre>';
        return false;
    }
}

// Подключение к базе данных (используйте PDO)
$dsn = "mysql:host=localhost;dbname=winter_tourism";
$username = "root";
$password = "";
$pdo = new PDO($dsn, $username, $password);

// Получение категорий и списка товаров
$optionGroups = getOptionGroups($pdo); // Используйте PDO для получения категорий
$fullList = getFullList($pdo); // Используйте PDO для получения списка товаров

// Фильтр
$name = isset($_GET['name']) ? $_GET['name'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$price_min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
$price_max = isset($_GET['price_max']) ? $_GET['price_max'] : '';

// SQL-запрос для фильтрации
$sql = "SELECT p.*, t.name AS type_name 
        FROM products p 
        JOIN types t ON p.id_type = t.id
        WHERE 1"; // Начало условия WHERE

if (!empty($name)) {
    $sql .= " AND p.name LIKE '%$name%'";
}
if (!empty($type)) {
    $sql .= " AND p.id_type = '$type'";
}
if (!empty($price_min)) {
    $sql .= " AND p.cost >= '$price_min'";
}
if (!empty($price_max)) {
    $sql .= " AND p.cost <= '$price_max'";
}

// Выполнение запроса с фильтрами
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Фильтр -->
<form method="get">
    <label for="name">Название:</label>
    <input type="text" name="name" id="name" value="<?php echo $name; ?>">

    <label for="type">Категория:</label>
    <select name="type" id="type">
        <option value="">Все</option>
        <?php if (!empty($optionGroups)): ?>
            <?php foreach ($optionGroups as $group): ?>
                <option value="<?php echo $group['id']; ?>" <?php if ($type == $group['id']) echo 'selected'; ?>><?php echo $group['name']; ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

    <label for="price_min">Цена от:</label>
    <input type="number" name="price_min" id="price_min" value="<?php echo $price_min; ?>">

    <label for="price_max">Цена до:</label>
    <input type="number" name="price_max" id="price_max" value="<?php echo $price_max; ?>">

    <button type="submit">Фильтровать</button>
    <a href="tourism.php" class="btn btn-danger">Сбросить фильтр</a>
</form>

<!-- Результаты поиска -->
<div class="container mt-3">
    <?php if (!empty($result)): ?> 
        <div class="row">
            <?php foreach ($result as $row): ?>
                <div class='col-md-4 mb-4'>
                    <div class='card'>
                        <img src='catalog_images/<?php echo basename($row['img_path']); ?>' class='card-img-top' alt='<?php echo $row['name']; ?>'> 
                        <div class='card-body'>
                            <h5 class='card-title'><?php echo $row['name']; ?></h5>
                            <p class='card-text'><?php echo $row['description']; ?></p>
                            <p class='card-text'>Категория: <?php echo $row['type_name']; ?></p>
                            <p class='card-text'>Цена: <?php echo $row['cost']; ?> руб.</p>
                            <a href='product.php?id=<?php echo $row['id']; ?>' class='btn btn-primary'>Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="mt-3">Товаров, соответствующих вашим критериям, не найдено.</p>
    <?php endif; ?>
</div>

<?php
require 'footer.php';
?>
