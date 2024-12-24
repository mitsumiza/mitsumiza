<?php

class Product {
    private $pdo; // Объект PDO для работы с базой данных

    // Конструктор класса, принимает объект PDO
    public function __construct($pdo) {
        $this->pdo = $pdo; // Инициализируем свойство $pdo
    }

    // Получить все продукты с именем типа
    public function getFullList() {
        // SQL-запрос для получения всех продуктов и их типов
        $sql = "SELECT p.*, t.name AS types_name FROM products p LEFT JOIN types t ON p.id_type = t.id";
        $stmt = $this->pdo->query($sql); // Выполняем запрос
        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Возвращаем все результаты в виде ассоциативного массива
        }
        return false; // Возвращаем false, если запрос не удался
    }

    // Получить продукт по ID
    public function getProductById($id) {
        // Подготавливаем SQL-запрос для получения продукта по ID
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]); // Выполняем запрос с передачей параметра
        return $stmt->fetch(PDO::FETCH_ASSOC); // Возвращаем результат в виде ассоциативного массива
    }

    // Получить продукт по ID с именем типа
    public function getProductByIdWithTypeName($id) {
        // Подготавливаем SQL-запрос для получения продукта по ID с его типом
        $stmt = $this->pdo->prepare("SELECT p.*, t.name AS types_name FROM products p LEFT JOIN types t ON p.id_type = t.id WHERE p.id = :id");
        $stmt->execute(['id' => $id]); // Выполняем запрос с передачей параметра
        return $stmt->fetch(PDO::FETCH_ASSOC); // Возвращаем результат в виде ассоциативного массива
    }

    // Добавить новый продукт
    public function addProduct($name, $description, $cost, $type_id, $img_path = 'no_img.png') {
        // Подготавливаем SQL-запрос для добавления нового продукта
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, cost, id_type, img_path) VALUES (:name, :description, :cost, :type_id, :img_path)");
        // Выполняем запрос с передачей параметров
        $result = $stmt->execute([
            'name' => $name,
            'description' => $description,
            'cost' => $cost,
            'type_id' => $type_id,
            'img_path' => $img_path
        ]);

        return $result; // Возвращаем результат выполнения запроса
    }

    // Обновить существующий продукт
    public function updateProduct($id, $name, $description, $cost, $type_id, $img_path = 'no_img.png') {
        // Подготавливаем SQL-запрос для обновления существующего продукта
        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET name = :name, 
                description = :description, 
                cost = :cost, 
                id_type = :type_id,
                img_path = :img_path
            WHERE id = :id
        ");
        // Выполняем запрос с передачей параметров
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'cost' => $cost,
            'type_id' => $type_id,
            'img_path' => $img_path
        ]);
    }

    // Удалить продукт
    public function deleteProduct($id) {
        // Подготавливаем SQL-запрос для удаления продукта по ID
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]); // Выполняем запрос с передачей параметра
    }
}
?>
