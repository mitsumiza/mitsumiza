<?php
require 'templates/header.php';

?>
<div style="text-align: center; margin-top: 20px;">
    <form method="post">
        <button type="submit" formaction="public/export.php" style="background-color: white; color: black; border: 1px solid #ccc; padding: 10px 20px; border-radius: 5px;">Экспорт данных</button>
        <button type="submit" formaction="public/import.php" style="background-color: white; color: black; border: 1px solid #ccc; padding: 10px 20px; border-radius: 5px;">Импорт данных</button>
    </form>
</div>



<?php require 'templates/footer.php'; ?>