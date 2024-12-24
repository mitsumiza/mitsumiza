<?php
session_start(); // Стартуем сессии, чтобы отслеживать пользователя

// Подключаем файл с классом Product
require 'tablemodule.php';

// Подключение к базе данных
try {
    // Создаем объект PDO для подключения к базе данных
    $pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch (PDOException $e) {
    // В случае ошибки подключения выводим сообщение и завершаем выполнение скрипта
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    exit;
}

// Создаем экземпляр класса Product
$tableModule = new Product($pdo); // Убедитесь, что название класса правильное

/**
 * Функция для получения полного списка товаров с фильтрацией
 *
 * @param PDO $pdo Объект подключения к базе данных
 * @return array|false Массив с товарами или false в случае ошибки
 */
function getFullList($pdo)
{
    // SQL-запрос для получения данных о товарах и их типах
    $sql = "SELECT  p.id,
                    p.img_path,
                    p.name,
                    t.name AS type_name,
                    p.description,
                    p.cost
            FROM products p
            JOIN types t ON p.id_type = t.id";
    $arBinds = []; // Массив для связывания параметров запроса
    
    // Проверяем, не очищен ли фильтр и есть ли параметры в запросе
    if (!key_exists('clearFilter', $_GET) && count($_GET) > 0) {
        $whereParts = []; // Массив для хранения частей условия WHERE

        // Фильтр по имени товара
        if (!empty($_GET['name'])) {
            $whereParts[] = "p.name LIKE :name"; // Добавляем условие фильтрации по имени
            $arBinds['name'] = '%' . $_GET['name'] . '%'; // Связываем параметр
        }

        // Фильтр по типу товара
        if (!empty($_GET['type_name'])) {
            $whereParts[] = "t.id = :type_id"; // Добавляем условие фильтрации по типу
            // Подзапрос для получения id типа по имени
            $stmt = $pdo->prepare("SELECT id FROM types WHERE name = :type_name_for_id");
            $stmt->execute(['type_name_for_id' => $_GET['type_name']]); // Выполняем запрос
            $type_id_data = $stmt->fetch(PDO::FETCH_ASSOC); // Получаем результат
            if ($type_id_data) {
                $arBinds['type_id'] = $type_id_data['id']; // Связываем ID типа
            } else {
                return []; // Если такого типа нет, возвращаем пустой массив
            }
        }

        // Фильтр по описанию товара
        if (!empty($_GET['description'])) {
            $whereParts[] = "p.description LIKE :description"; // Добавляем условие фильтрации по описанию
            $arBinds['description'] = '%' . $_GET['description'] . '%'; // Связываем параметр
        }

        // Фильтр по цене товара
        if (!empty($_GET['cost'])) {
            $whereParts[] = "p.cost = :cost"; // Добавляем условие фильтрации по цене
            $arBinds['cost'] = $_GET['cost']; // Связываем параметр
        }

        // Если есть условия фильтрации, добавляем их в SQL-запрос
        if (!empty($whereParts)) {
            $sql .= " WHERE " . implode(" AND ", $whereParts); // Соединяем части условия с помощью AND
        }
    }

    // Подготавливаем и выполняем SQL-запрос
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($arBinds); // Передаем связанные параметры

    // Проверяем результат выполнения запроса
    if ($result) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Возвращаем все найденные записи
    } else {
        // Обработка ошибки выполнения запроса
        echo "Ошибка выполнения запроса: ";
        print_r($stmt->errorInfo()); // Выводим информацию об ошибке
        return false; // Возвращаем false в случае ошибки
    }
}

/**
 * Функция для установки сообщения в сессию
 *
 * @param string $type Тип сообщения (например, 'success' или 'error')
 * @param string $message Текст сообщения
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // Сохраняем тип сообщения
        'message' => $message // Сохраняем текст сообщения
    ];
}

/**
 * Функция для получения сообщения из сессии
 *
 * @return array|null Массив с сообщением или null, если сообщение отсутствует
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message']; // Получаем сообщение
        unset($_SESSION['flash_message']); // Удаляем сообщение из сессии
        return $flash; // Возвращаем сообщение
    }
    return null; // Если сообщения нет, возвращаем null
}
