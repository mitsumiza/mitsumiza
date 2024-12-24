<?php

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Получить все продукты с именем типа
    public function getFullList() {
         $sql = "SELECT p.*, t.name AS types_name FROM products p LEFT JOIN types t ON p.id_type = t.id";
        $stmt = $this->pdo->query($sql);
        if ($stmt) {
             return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false; // Или выбрасывать исключение, если нужно
    }

    // Получить продукт по ID
    public function getProductById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     // Получить продукт по ID с именем типа
    public function getProductByIdWithTypeName($id) {
        $stmt = $this->pdo->prepare("SELECT p.*, t.name AS types_name FROM products p LEFT JOIN types t ON p.id_type = t.id WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Добавить новый продукт
    public function addProduct($name, $description, $cost, $type_id, $img_path = 'no_img.png') {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, cost, id_type, img_path) VALUES (:name, :description, :cost, :type_id, :img_path)");
          $result = $stmt->execute([
             'name' => $name,
             'description' => $description,
             'cost' => $cost,
             'type_id' => $type_id,
             'img_path' => $img_path
         ]);

         return $result;
    }

    // Обновить существующий продукт
  public function updateProduct($id, $name, $description, $cost, $type_id, $img_path = 'no_img.png')
{
    $stmt = $this->pdo->prepare("
        UPDATE products 
        SET name = :name, 
            description = :description, 
            cost = :cost, 
            id_type = :type_id,
            img_path = :img_path
        WHERE id = :id
    ");
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
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>

