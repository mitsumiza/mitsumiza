<?php
require '../templates/header.php';
require_once '../.core/modules.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['url']) && !empty($_POST['url'])) {
        $url = $_POST['url'];
        // подключение к базе данных
        $conn = dbConnect();
        if ($conn === false) {
            $message = "Ошибка подключения к базе данных: " . $conn->errorInfo()[2];
        } else {
            try {
                // импортируем данные из ссылки
                $xml = importFromURL($url);
                if ($xml === false) {
                    throw new Exception("Ошибка при загрузке или парсинге XML файла.");
                }
                // проверяем валидность файла
                if (!validateXML($xml)) {
                    throw new Exception("Невалидный XML файл.");
                }

                $tableName = 'products'; 
                // импортируем данные в бд
                $importedRows = importData($xml, $tableName, $conn);

                if ($importedRows === false) {
                    throw new Exception("Ошибка при импорте данных.");
                }

                $message = "Импортировано $importedRows записей.";

            } catch (Exception $e) {
                $message = "Ошибка: " . $e->getMessage();
            } finally {
                unset($conn);
            }
        }
    } else {
        $message = "Ошибка: Не указан URL XML файла.";
    }
}

?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <form method="post">
                <div class="mb-3">
                    <label for="url" class="form-label">URL XML файла:</label>
                    <input type="text" class="form-control" id="url" name="url" required>
                </div>
                <button type="submit" class="btn btn-primary">Импорт</button>
            </form>
            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo strpos(strtolower($message), 'ошибка') !== false ? 'danger' : 'success'; ?> mt-3" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../templates/footer.php'; ?>

