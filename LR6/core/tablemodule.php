<?php

class Product  
{
    // Константа для хранения относительного пути к директории с изображениями
    const IMAGE_DIR = 'LR5/image/catalog_images/'; // Относительный путь для веб-сервера

    // Переменная для хранения объекта PDO для работы с базой данных
    private $pdo;

    // Конструктор класса, принимает объект PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo; // Сохраняем объект PDO в свойство класса
        // Проверяем, существует ли директория для изображений, если нет - создаем
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR, 0777, true); // Создаем директорию с правами доступа 0777
        }
    }

    // Метод для получения полного списка товаров
    public function getFullList()
    {
        // SQL-запрос для получения данных о товарах с объединением таблицы типов
        $sql = "SELECT p.id, p.img_path, p.name, t.name AS type_name, p.description, p.cost
                FROM products p
                JOIN types t ON p.id_type = t.id";
        $arBinds = []; // Массив для связывания параметров запроса

        // Проверка, не очищен ли фильтр
        if (!key_exists('clearFilter', $_GET)) {
            if (count($_GET) > 0) {
                $whereParts = []; // Массив для условий WHERE

                // Проверка наличия фильтра по имени товара
                if (!empty($_GET['name'])) {
                    $whereParts[] = "p.name LIKE :name"; // Добавляем условие для фильтрации по имени
                    $arBinds['name'] = '%' . $_GET['name'] . '%'; // Связываем параметр
                }

                // Проверка наличия фильтра по типу товара
                if (!empty($_GET['type_name'])) {
                    $whereParts[] = "t.id = :type_id"; // Добавляем условие для фильтрации по типу
                    // Получаем ID типа по его имени
                    $stmt = $this->pdo->prepare("SELECT id FROM types WHERE name = :type_name_for_id");
                    $stmt->execute(['type_name_for_id' => $_GET['type_name']]);
                    $type_id_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($type_id_data) {
                        $arBinds['type_id'] = $type_id_data["id"]; // Связываем ID типа
                    } else {
                        return []; // Если тип не найден, возвращаем пустой массив
                    }
                }

                // Проверка наличия фильтра по описанию товара
                if (!empty($_GET['description'])) {
                    $whereParts[] = "p.description LIKE :description"; // Добавляем условие для фильтрации по описанию
                    $arBinds['description'] = '%' . $_GET['description'] . '%'; // Связываем параметр
                }

                // Проверка наличия фильтра по цене товара
                if (!empty($_GET['cost'])) {
                    $whereParts[] = "p.cost = :cost"; // Добавляем условие для фильтрации по цене
                    $arBinds['cost'] = $_GET['cost']; // Связываем параметр
                }

                // Если есть условия фильтрации, добавляем их в SQL-запрос
                if (!empty($whereParts)) {
                    $sql .= " WHERE " . implode(" AND ", $whereParts);
                }
            }
        }

        try {
            // Подготавливаем и выполняем SQL-запрос
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($arBinds); // Передаем связанные параметры
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Возвращаем все найденные записи
        } catch (PDOException $e) {
            // В случае ошибки записи в лог
            error_log("Ошибка выполнения запроса: " . $e->getMessage());
            return false; // Возвращаем false в случае ошибки
        }
    }

    // Метод для получения всех товаров
    public function getProducts()
    {
        try {
            // SQL-запрос для получения всех товаров с их типами
            $sql = "SELECT p.id, p.img_path, p.name, p.description, p.cost, t.name AS type_name
                    FROM products p
                    JOIN types t ON p.id_type = t.id";
            $stmt = $this->pdo->query($sql); // Выполняем запрос
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Возвращаем все найденные записи
        } catch (PDOException $e) {
            // В случае ошибки записи в лог
            error_log("Ошибка при получении списка товаров: " . $e->getMessage());
            return false; // Возвращаем false в случае ошибки
        }
    }

    // Метод для получения товара по ID
    public function getProductById($id)
    {
        try {
            // Подготавливаем SQL-запрос для получения товара по его ID
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]); // Выполняем запрос с передачей ID
            return $stmt->fetch(PDO::FETCH_ASSOC); // Возвращаем найденный товар
        } catch (PDOException $e) {
            // В случае ошибки записи в лог
            error_log("Ошибка при получении товара по ID: " . $e->getMessage());
            return false; // Возвращаем false в случае ошибки
        }
    }

    // Метод для удаления товара по ID
    public function deleteProduct($id)
    {
        try {
            $product = $this->getProductById($id); // Получаем товар по ID
            // Проверяем, существует ли товар и не является ли изображение стандартным
            if ($product && $product['img_path'] !== 'no_img.png') {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR . $product['img_path']; // Полный путь к изображению
                // Если файл существует, пытаемся его удалить
                if (file_exists($filePath) && !unlink($filePath)) {
                    error_log("Ошибка при удалении файла: " . $filePath); // Записываем ошибку в лог
                    return false; // Возвращаем false в случае ошибки удаления файла
                }
            }
            // Подготавливаем SQL-запрос для удаления товара из базы данных
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
            if (!$stmt->execute([$id])) {
                error_log("Ошибка при удалении товара из БД: " . implode(", ", $stmt->errorInfo())); // Записываем ошибку в лог
                return false; // Возвращаем false в случае ошибки удаления из БД
            }
            return true; // Удаление успешно
        } catch (PDOException $e) {
            // В случае ошибки записи в лог
            error_log("Ошибка при удалении товара: " . $e->getMessage());
            return false; // Возвращаем false в случае ошибки
        }
    }

    // Метод для обновления товара
    public function updateProduct($id, $name, $id_type, $description, $cost, $newImgPath = null)
    {
        try {
            $product = $this->getProductById($id); // Получаем товар по ID
            if (!$product) {
                return false; // Если товар не найден, возвращаем false
            }

            // Начинаем формировать SQL-запрос для обновления товара
            $query = "UPDATE products SET name = ?, id_type = ?, description = ?, cost = ?";
            $params = [$name, $id_type, $description, $cost]; // Параметры для обновления

            // Проверяем, есть ли новое изображение и отличается ли оно от текущего
            if ($newImgPath && $newImgPath !== $product['img_path'] && $product['img_path'] !== 'no_img.png') {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR . $product['img_path']; // Полный путь к текущему изображению
                // Если файл существует, пытаемся его удалить
                if (file_exists($filePath) && !unlink($filePath)) {
                    error_log("Ошибка при удалении файла: " . $filePath); // Записываем ошибку в лог
                }
                $query .= ", img_path = ?"; // Добавляем условие для обновления изображения
                $params[] = $newImgPath; // Добавляем новое изображение в параметры
            } elseif ($newImgPath) {
                $query .= ", img_path = ?"; // Добавляем условие для обновления изображения
                $params[] = $newImgPath; // Добавляем новое изображение в параметры
            }

            $query .= " WHERE id = ?"; // Условие для обновления по ID
            $params[] = $id; // Добавляем ID в параметры

            $stmt = $this->pdo->prepare($query); // Подготавливаем запрос

            if (!$stmt->execute($params)) {
                error_log("Ошибка при обновлении товара в БД: " . implode(", ", $stmt->errorInfo())); // Записываем ошибку в лог
                return false; // Возвращаем false в случае ошибки обновления
            }
            return true; // Обновление успешно

        } catch (PDOException $e) {
            // В случае ошибки записи в лог
            error_log("Ошибка при обновлении товара: " . $e->getMessage());
            return false; // Возвращаем false в случае ошибки
        }
    }

    // Метод для получения пути к изображению
    public function getImagePath($imgPath)
    {
        return self::IMAGE_DIR . $imgPath; // Возвращаем полный путь к изображению
    }

    // Метод для отображения товаров в виде таблицы
    public function displayProductsInTable()
    {
        $products = $this->getProducts(); // Получаем список всех товаров
        if ($products) {
            echo '<table class="product-table">'; // Начинаем таблицу
            echo '<thead>'; // Заголовок таблицы
            echo '<tr>';
            echo '<th>ID</th>'; // Заголовок для ID
            echo '<th>Изображение</th>'; // Заголовок для изображения
            echo '<th>Название</th>'; // Заголовок для названия
            echo '<th>Описание</th>'; // Заголовок для описания
            echo '<th>Цена</th>'; // Заголовок для цены
            echo '<th>Тип</th>'; // Заголовок для типа
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>'; // Начинаем тело таблицы

            foreach ($products as $product) {
                $imagePath = $this->getImagePath($product['img_path']); // Получаем путь к изображению
                $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath; // Полный путь к изображению

                echo '<tr>'; // Начинаем строку таблицы
                echo '<td>' . htmlspecialchars($product['id']) . '</td>'; // Отображаем ID товара
                // Проверяем, существует ли изображение
                if (file_exists($fullImagePath)) {
                    echo '<td><img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($product['name']) . '" class="product-image" style="width: 100px; height: auto;"></td>'; // Отображаем изображение
                } else {
                    echo '<td><img src="' . htmlspecialchars($this->getImagePath('no_img.png')) . '" alt="Нет изображения" class="product-image" style="width: 100px; height: auto;"></td>'; // Отображаем стандартное изображение
                }
                echo '<td>' . htmlspecialchars($product['name']) . '</td>'; // Отображаем название товара
                echo '<td>' . htmlspecialchars($product['description']) . '</td>'; // Отображаем описание товара
                echo '<td>' . htmlspecialchars($product['cost']) . ' руб.</td>'; // Отображаем цену товара
                echo '<td>' . htmlspecialchars($product['type_name']) . '</td>'; // Отображаем тип товара
                echo '</tr>'; // Заканчиваем строку таблицы
            }

            echo '</tbody>'; // Заканчиваем тело таблицы
            echo '</table>'; // Заканчиваем таблицу
        } else {
            echo '<p>Товары не найдены.</p>'; // Если товаров нет, выводим сообщение
        }
    }
}

Найти еще
