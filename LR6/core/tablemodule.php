<?php

class Product  
{
    const IMAGE_DIR = 'LR5/image/catalog_images/'; // Относительный путь для веб-сервера

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        // Проверяем, существует ли директория для изображений, если нет - создаем
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR, 0777, true);
        }
    }

    public function getFullList()
    {
        $sql = "SELECT p.id, p.img_path, p.name, t.name AS type_name, p.description, p.cost
                FROM products p
                JOIN types t ON p.id_type = t.id";
        $arBinds = [];

        if (!key_exists('clearFilter', $_GET)) {
            if (count($_GET) > 0) {
                $whereParts = [];

                if (!empty($_GET['name'])) {
                    $whereParts[] = "p.name LIKE :name";
                    $arBinds['name'] = '%' . $_GET['name'] . '%';
                }

                if (!empty($_GET['type_name'])) {
                    $whereParts[] = "t.id = :type_id";
                    $stmt = $this->pdo->prepare("SELECT id FROM types WHERE name = :type_name_for_id");
                    $stmt->execute(['type_name_for_id' => $_GET['type_name']]);
                    $type_id_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($type_id_data) {
                        $arBinds['type_id'] = $type_id_data["id"];
                    } else {
                        return []; // Если тип не найден, возвращаем пустой массив
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

                if (!empty($whereParts)) {
                    $sql .= " WHERE " . implode(" AND ", $whereParts);
                }
            }
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($arBinds);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ошибка выполнения запроса: " . $e->getMessage());
            return false;
        }
    }

    public function getProducts()
    {
        try {
            $sql = "SELECT p.id, p.img_path, p.name, p.description, p.cost, t.name AS type_name
                    FROM products p
                    JOIN types t ON p.id_type = t.id";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ошибка при получении списка товаров: " . $e->getMessage());
            return false;
        }
    }

    public function getProductById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ошибка при получении товара по ID: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = $this->getProductById($id);
            if ($product && $product['img_path'] !== 'no_img.png') {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR . $product['img_path'];
                if (file_exists($filePath) && !unlink($filePath)) {
                    error_log("Ошибка при удалении файла: " . $filePath);
                    return false;
                }
            }
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
            if (!$stmt->execute([$id])) {
                error_log("Ошибка при удалении товара из БД: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
            return true; // Удаление успешно
        } catch (PDOException $e) {
            error_log("Ошибка при удалении товара: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $name, $id_type, $description, $cost, $newImgPath = null)
    {
        try {
            $product = $this->getProductById($id);
            if (!$product) {
                return false; // Если товар не найден, возвращаем false
            }

            $query = "UPDATE products SET name = ?, id_type = ?, description = ?, cost = ?";
            $params = [$name, $id_type, $description, $cost];

            if ($newImgPath && $newImgPath !== $product['img_path'] && $product['img_path'] !== 'no_img.png') {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DIR . $product['img_path'];
                if (file_exists($filePath) && !unlink($filePath)) {
                    error_log("Ошибка при удалении файла: " . $filePath);
                }
                $query .= ", img_path = ?";
                $params[] = $newImgPath;
            } elseif ($newImgPath) {
                $query .= ", img_path = ?";
                $params[] = $newImgPath;
            }

            $query .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $this->pdo->prepare($query);

            if (!$stmt->execute($params)) {
                error_log("Ошибка при обновлении товара в БД: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
            return true;

        } catch (PDOException $e) {
            error_log("Ошибка при обновлении товара: " . $e->getMessage());
            return false;
        }
    }

    public function getImagePath($imgPath)
    {
        return self::IMAGE_DIR . $imgPath;
    }

    public function displayProductsInTable()
    {
        $products = $this->getProducts();
        if ($products) {
            echo '<table class="product-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Изображение</th>';
            echo '<th>Название</th>';
            echo '<th>Описание</th>';
            echo '<th>Цена</th>';
            echo '<th>Тип</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($products as $product) {
                $imagePath = $this->getImagePath($product['img_path']);
                $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

                echo '<tr>';
                echo '<td>' . htmlspecialchars($product['id']) . '</td>';
                if (file_exists($fullImagePath)) {
                    echo '<td><img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($product['name']) . '" class="product-image" style="width: 100px; height: auto;"></td>';
                } else {
                    echo '<td><img src="' . htmlspecialchars($this->getImagePath('no_img.png')) . '" alt="Нет изображения" class="product-image" style="width: 100px; height: auto;"></td>';
                }
                echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                echo '<td>' . htmlspecialchars($product['description']) . '</td>';
                echo '<td>' . htmlspecialchars($product['cost']) . ' руб.</td>';
                echo '<td>' . htmlspecialchars($product['type_name']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Товары не найдены.</p>';
        }
    }
}

