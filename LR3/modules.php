<?php
session_start(); // Стартуем сессии, чтобы отслеживать пользователя

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');

function validateEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Дополнительная проверка на наличие символов, запрещенных в email
    if (strpos($email, ' ') !== false) {
        return false;
    }

    return true;
}

// Проверка, существует ли email в базе
function isEmailTaken($email, $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}
 
function validatePassword($password) {
    // Проверяем минимальную длину пароля
    if (strlen($password) < 7) {
        return false;
    }

    // Проверка: хотя бы одна заглавная латинская буква
    $hasUppercase = preg_match('/[A-Z]/', $password);
    
    // Проверка: хотя бы одна строчная латинская буква
    $hasLowercase = preg_match('/[a-z]/', $password);
    
    // Проверка: хотя бы одна цифра
    $hasDigit = preg_match('/\d/', $password);

    // Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву и одну цифру
    if ($hasUppercase && $hasLowercase && $hasDigit) {
        return true;  // Пароль валиден
    } else {
        return false;
    }
    return true;
}

function avalidatePassword($password) {
    // Проверяем минимальную длину пароля
    if (strlen($password) < 8) {

        return false;
    }
  
    // Проверка: хотя бы одна заглавная латинская буква
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Проверка: хотя бы одна строчная латинская буква
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Проверка: хотя бы одна цифра
    if (!preg_match('/\d/', $password)) {
        return false;
    }
  
    // Проверка: хотя бы один специальный символ
    if (!preg_match('/[!@#$%^&*()_+=[\]{};\':"\\|,.<>\/?]/', $password)) {
        return false;
    }
  
    // Пароль валиден
    return true;
  }

function registerUser($full_name, $birth_date, $blood_group, $rhesus_factor, $email, $password1, $password2, $pdo)
{
    // Проверяем, занят ли email
    if (isEmailTaken($email, $pdo)) {
        return "Email уже занят.";
    }

    // Проверяем, совпадают ли пароли
    if ($password1 !== $password2) {
        return "Пароли не совпадают!";
    }

    // Проверяем пароль по правилам
    if (!validatePassword($password1)) {
        return "Пароль не соответствует требованиям:
                <ul>
                    <li>Минимум 7 символов</li>
                    <li>Минимум 1 заглавная латинская буква</li>
                    <li>Минимум 1 строчная латинская буква</li>
                    <li>Минимум 1 цифра</li>
                    <li>Без русских букв</li>
                </ul>";
    }

    // Хэшируем пароль
    $passwordHash = password_hash($password1, PASSWORD_BCRYPT);

    // Вставляем нового пользователя в базу данных
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, birth_date, blood_group, rhesus_factor, email, password) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if ($stmt->execute([$full_name, $birth_date, $blood_group, $rhesus_factor, $email, $passwordHash])) {
        return "Регистрация успешна.";
    } else {
        return "Ошибка регистрации.";
    }
}

function loginUserWithCookies($email, $password, $pdo)
{
    // Проверяем, есть ли кука с неудачными попытками
    $failed_attempts = isset($_COOKIE['failed_attempts']) ? (int)$_COOKIE['failed_attempts'] : 0;
    $blocked_until = isset($_COOKIE['blocked_until']) ? (int)$_COOKIE['blocked_until'] : 0;
    $current_time = time();

    // Если пользователь заблокирован (если время блокировки не истекло)
    if ($failed_attempts >= 3 && $blocked_until > $current_time) {
        return "Вы заблокированы. Попробуйте снова через " . ceil(($blocked_until - $current_time) / 60) . " минут.";
    }

    // Ищем пользователя по email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Если пользователь найден
    if ($user && password_verify($password, $user['password'])) {
        // Сброс неудачных попыток и куки
        setcookie('failed_attempts', 0, time() - 3600, '/'); // Удаляем куку
        setcookie('blocked_until', 0, time() - 3600, '/'); // Удаляем куку

        // Устанавливаем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];

        return true;
    } else {
        // Если пароль неверен, увеличиваем счётчик неудачных попыток
        $failed_attempts++;
        setcookie('failed_attempts', $failed_attempts, time() + 3600, '/');

        if ($failed_attempts >= 3) {
            // Устанавливаем блокировку на 1 час
            setcookie('blocked_until', time() + 3600, time() + 3600, '/');
            return "Вы заблокированы. Попробуйте снова через 60 минут.";
        } else {
            // Устанавливаем количество попыток и их срок действия (1 час)
            return "Неверный email или пароль. У вас осталось " . (3 - $failed_attempts) . " попытки.";
        }
    }
}

// Функция для проверки авторизации
function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Функция для переадресации, если не авторизован
function requireLogin() {
    // Проверяем, авторизирован ли пользователь.
    if (!isset($_SESSION['user_id'])) {
      // Если нет, перенаправляем на страницу регистрации.
      header('Location: login.php');
      exit;
    }
  }
  
function getOptionGroups($pdo) {
    $sql = "SELECT t.id, t.name FROM types t";
  
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        // Возвращаем массив с результатами
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Обработка ошибки выполнения запроса
        echo '<pre>';
        echo "Ошибка выполнения запроса";
        echo '</pre>';
        return false;
    }
}

function getFullList($pdo)
{
    $sql = "SELECT p.id, p.img_path, p.name, t.name AS type_name, p.description, p.cost
            FROM products p
            JOIN types t ON p.id_type = t.id"; 
    $arBinds = [];
    if (!key_exists('clearFilter', $_GET)) {
        if (count($_GET) > 0) {
            $whereParts = []; // Массив для хранения частей условия WHERE
            if ($_GET['full_name']) {
                $whereParts[] = "c.full_name = :full_name";
                $arBinds['full_name'] = $_GET['full_name'];
            }
            if ($_GET['group_name']) {
                $whereParts[] = "c.group_id = :group_name";
                $arBinds['group_name'] = $_GET['group_name'];
            }
            if ($_GET['biography']) {
                $whereParts[] = "c.biography LIKE :biography";
                $arBinds['biography'] = '%' . $_GET['biography'] . '%';
            }
            if ($_GET['birth_year']) {
                $whereParts[] = "c.birth_year = :birth_year";
                $arBinds['birth_year'] = $_GET['birth_year'];
            }
            if (!empty($whereParts)) { // Проверяем, есть ли части условия
                $sql .= " WHERE" . " " . implode(" AND ", $whereParts) . ';'; // Соединяем части условия с помощью AND
            }
        }
    }
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($arBinds);

    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Обработка ошибки выполнения запроса
        echo '<pre>';
        echo "Ошибка выполнения запроса";
        echo '</pre>';
        return false;
    }
}

