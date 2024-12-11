<?php
require '../templates/header.php';
// таблица бд
$tableName = 'products'; 
// файл для экспорта
$exportedTableName = $tableName . '_exported';
$message = '';

// пост-запрос
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // подключение к бд 
    $conn = dbConnect();
    if ($conn === false) {
        $message = "Ошибка подключения к базе данных: " . $conn->errorInfo()[2];
    } else {
        try {
            // селект запрос для извлечения данных
            $stmt = $conn->query("SELECT * FROM $tableName");
            if (!$stmt) {
                throw new Exception("Ошибка выполнения запроса: " . $conn->errorInfo()[2]);
            }
            // данные выводятся в массив
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // генерируем XML-данные 
            $xml = generateXML($data, $tableName);

            $filename = $exportedTableName . '.xml';
            $filePath = '../files/' . $filename;
            // сохранение данных в файл 
            if (file_put_contents($filePath, $xml) !== false) {
                $message = "Файл с данными сохранен на диск по адресу: files/" . $filename;
            } else {
                // обработка ошибок
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

