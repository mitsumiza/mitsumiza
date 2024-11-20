<?php
require '../templates/header.php';

$tableName = 'products'; // Имя таблицы изменено на products
$exportedTableName = $tableName . '_exported';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = dbConnect();
    if ($conn === false) {
        $message = "Ошибка подключения к базе данных: " . $conn->errorInfo()[2];
    } else {
        try {
            $stmt = $conn->query("SELECT * FROM $tableName");
            if (!$stmt) {
                throw new Exception("Ошибка выполнения запроса: " . $conn->errorInfo()[2]);
            }
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $xml = generateXML($data, $tableName);

            $filename = $exportedTableName . '.xml';
            $filePath = '../files/' . $filename;

            if (file_put_contents($filePath, $xml) !== false) {
                $message = "Файл с данными сохранен на диск по адресу: files/" . $filename;
            } else {
                throw new Exception("Ошибка при сохранении файла на сервер.");
            }

        } catch (PDOException $e) {
            $message = "Ошибка PDO: " . $e->getMessage();
        } catch (Exception $e) {
            $message = "Ошибка: " . $e->getMessage();
        } finally {
            unset($conn);
        }
    }
}

?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <form method="post">
                <button type="submit" class="btn btn-light btn-block" style="border: 1px solid #ccc;">Экспорт в XML на сервер</button>
            </form>
            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo isset($message) && strpos($message, 'Ошибка') !== false ? 'danger' : 'success'; ?> mt-3" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../templates/footer.php'; ?>

