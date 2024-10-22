
<?php
session_start(); // Инициализация сессии 

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=winter_tourism', 'root', '');

// Функция проверки валидности пароля
function validatePassword($password) {
  // Проверяем минимальную длину пароля
  if (strlen($password) < 7) {
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


function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}

//  Проверяем авторизацию пользователя
if (isUserLoggedIn()) {
  //  Пользователь авторизован, перенаправляем его на tourism.php 
  header("Location: tourism.php");
  exit;
}

// Обрабатываем POST-запрос  для  входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = loginUserWithCookies($email, $password, $pdo);

  if ($result === true) {
      //  Пользователь авторизован, перенаправляем его на tourism.php
      header("Location: tourism.php");
      exit;
  } else {
      $error = $result;
  }
}

// Функция проверки, занят ли email
function isEmailTaken($email, $pdo) {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetchColumn() > 0;
}

// Функция регистрации нового пользователя
function registerUser($full_name, $birth_date, $blood_group, $rhesus_factor, $email, $password1, $password2, $pdo) {
    // Проверяем, занят ли email
    if (empty($full_name) || empty($birth_date) || empty($blood_group) || empty($rhesus_factor) || empty($email) || empty($password1) || empty($password2)) {
        return "Заполните все поля!";
    }

    $full_name = trim($full_name);
    $birth_date = trim($birth_date);
    $blood_group = trim($blood_group);
    $rhesus_factor = trim($rhesus_factor);
    $email = trim($email);
    $password1 = trim($password1);
    $password2 = trim($password2);

    if (isEmailTaken($email, $pdo)) {
        return "Email уже занят.";
    }

    // Проверяем, совпадают ли пароли
    if ($password1 !== $password2) {
        return "Пароли не совпадают!";
    }

    // Проверяем пароль по правилам
    if (!validatePassword($password1)) {
        return "Пароль не соответствует требованиям:";
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

// Функция входа пользователя с использованием куки
function loginUserWithCookies($email, $password, $pdo) {
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

  // Блокировка пользователя на 10 минут после 3 неудачных попыток
  if ($failed_attempts >= 3) {
   $blocked_until = time() + 600; // 10 минут
   setcookie('blocked_until', $blocked_until, time() + 3600, '/');
   return "Неверный пароль. Вы заблокированы на 10 минут.";
  } else {
   return "Неверный пароль.";
  }
 }
}

// Обработка данных формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $birth_date = trim($_POST['birth_date']);
    $blood_group = trim($_POST['blood_group']);
    $rhesus_factor = trim($_POST['rhesus_factor']);
    $email = trim($_POST['email']);
    $password1 = trim($_POST['password1']);
    $password2 = trim($_POST['password2']);

    // Проверка ввода
    $errors = validateRegistrationForm($full_name, $birth_date, $blood_group, $rhesus_factor, $email, $password1, $password2);

    if (empty($errors)) {
        // Хэшируем пароль
        $password_hash = password_hash($password1, PASSWORD_DEFAULT);

        $registration_result = registerUser($full_name, $birth_date, $blood_group, $rhesus_factor, $email, $password_hash, $pdo);

        if ($registration_result === true) {
            // Успешная регистрация
            echo 'Регистрация прошла успешно!';
        } else {
            // Ошибка регистрации
            echo $registration_result;
        }
    } else {
        // Ошибки в форме
        echo 'Пожалуйста, исправьте ошибки в форме: <br>';
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
    }
}

// Обработка данных формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $login_result = loginUserWithCookies($email, $password, $pdo);
    if ($login_result === true) {
        // Успешный вход
        header('Location: index.php');
        exit();
    } else {
        // Ошибка входа
        echo $login_result;
    }
}

// В случае, если пользователь уже авторизован, перенаправляем на главную страницу
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

?>