<?php
session_start(); // Стартуем сессии, чтобы отслеживать пользователя

// Подключение к базе данных
try {
    $pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage());
    exit;
}

/**
 * Получает полный список продуктов с возможностью фильтрации.
 *
 * @param PDO $pdo Подключение к базе данных.
 * @return array|false Массив продуктов или false в случае ошибки.
 */
function getFullList($pdo) {
    $sql = "SELECT p.id,
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
            $arBinds['name'] = '%' . htmlspecialchars($_GET['name']) . '%'; // Экранируем входные данные
        }
    
        if (!empty($_GET['type_name'])) {
            $whereParts[] = "t.id = :type_id";
            // Подзапрос для получения id типа по имени
            $stmt = $pdo->prepare("SELECT id FROM types WHERE name = :type_name_for_id");
            $stmt->execute(['type_name_for_id' => htmlspecialchars($_GET['type_name'])]);
            $type_id_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($type_id_data) {
                $arBinds['type_id'] = $type_id_data['id'];
            } else {
                return []; // Если такого типа нет, возвращаем пустой массив
            }
        }
    
        if (!empty($_GET['description'])) {
            $whereParts[] = "p.description LIKE :description";
            $arBinds['description'] = '%' . htmlspecialchars($_GET['description']) . '%'; // Экранируем входные данные
        }
    
        if (!empty($_GET['cost'])) {
            $whereParts[] = "p.cost = :cost";
            $arBinds['cost'] = htmlspecialchars($_GET['cost']); // Экранируем входные данные
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

/**
 * Получает группы продуктов.
 *
 * @param PDO $pdo Подключение к базе данных.
 * @return array|false Массив групп или false в случае ошибки.
 */
function getOptionGroups($pdo)
{
    try {
      $sql = "SELECT t.id, t.name FROM types t"; // ИСПРАВЛЕНО: Используется таблица types
      $stmt = $pdo->prepare($sql);
      if ($stmt->execute()) {
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($groups) {
          return $groups;
        } else {
          return [];
        }
      } else {
        echo "Ошибка выполнения запроса: ";
        print_r($stmt->errorInfo());
        return false;
      }
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}


/**
 * Устанавливает флэш-сообщение для отображения пользователю.
 *
 * @param string $type Тип сообщения (например, 'success', 'error').
 * @param string $message Сообщение для отображения.
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Получает флэш-сообщение и удаляет его из сессии.
 *
 * @return array|null Массив с типом и сообщением или null, если сообщения нет.
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
