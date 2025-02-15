<?php
require '../templates/header.php';

// Функции обработки из `modules.php`
require '../.core/modules.php';

// Загружаем данные для пресета, если он задан
$presetContent = '';
if (isset($_GET['preset']) && in_array($_GET['preset'], [1, 2, 3])) {
    $presetFile = "../templates/preset_" . (int)$_GET['preset'] . ".php";
    if (file_exists($presetFile)) {
        ob_start();
        include $presetFile;
        $presetContent = ob_get_clean();
    }
}

// Обработка отправленного текста
$processedText = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['text'])) {
    $text = $_POST['text'];
    $processedText = processText($text);  // Обрабатываем текст через функцию
}
?>

<div class="container">
    <form class="m-5" action="text.php<?= isset($_GET['preset']) ? '?preset=' . (int)$_GET['preset'] : '' ?>" method="post">
        <label class="form-label">Введите текст</label>
        <textarea class="form-control" name="text"><?= htmlspecialchars($presetContent) ?></textarea>
        <button class="btn btn-primary mt-2">Отправить</button>
    </form>
</div>

<?php if ($processedText): ?>
    <div class="container">
        <div class="examples">
            <h3>Решение Задач:</h3>
            <?= $processedText ?>
        </div>
    </div>
<?php endif; ?>

<?php require '../templates/footer.php'; ?>