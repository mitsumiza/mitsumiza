<?php
session_start();
session_unset();  // Очистить все сессионные переменные
session_destroy(); // Уничтожить сессию

header("Location: login.php");
exit;