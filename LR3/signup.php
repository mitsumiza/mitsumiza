<?php
require 'modules.php';

if (isUserLoggedIn()) {
    header("Location: tourism.php");
    exit;
}

// Инициализируем переменные значениями по умолчанию
$full_name = '';
$birth_date = '';
$blood_group = '';
$rhesus_factor = '';
$email = '';
$password1 = '';
$password2 = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $full_name = $_POST['full_name'];
    $birth_date = $_POST['birth_date'];
    $blood_group = $_POST['blood_group'];
    $rhesus_factor = $_POST['rhesus_factor'];
    $email = $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    // Проверка данных перед регистрацией
    $result = registerUser($full_name, $birth_date, $blood_group, $rhesus_factor, $email, $password1, $password2, $pdo);

    if ($result === "Регистрация успешна.") {
        header("Location: login.php");
        exit;
    } else {
        $error = $result;
    }
}

require 'header.php';
?>

<div class="d-flex flex-column h-100" style="min-height: 35em;">
    <div class="container">
        <div class="row">
            <p class="py-2">
                <a href="/un_site_main/LR1/index.html" class="text-decoration-none">Домашняя страница</a>
                > <b>Регистрация</b>
            </p>
        </div>
    </div>
    <div class="flex-grow-1 d-flex align-items-center mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <h1 class="mb-4">Регистрация</h1>
                    <!-- Форма регистрации -->
                    <form action="signup.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="full_name">ФИО</label>
                            <input type="text" name="full_name" class="form-control" placeholder="ФИО" value="<?= htmlspecialchars($full_name) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="birth_date">Дата рождения</label>
                            <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($birth_date) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="blood_group">Группа крови</label>
                            <select name="blood_group" class="form-control" required>
                                <option value="">Выберите группу крови</option>
                                <option value="O" <?= $blood_group == 'O' ? 'selected' : '' ?>>1</option>
                                <option value="A" <?= $blood_group == 'A' ? 'selected' : '' ?>>2</option>
                                <option value="B" <?= $blood_group == 'B' ? 'selected' : '' ?>>3</option>
                                <option value="AB" <?= $blood_group == 'AB' ? 'selected' : '' ?>>4</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rhesus_factor">Резус-фактор</label>
                            <select name="rhesus_factor" class="form-control" required>
                                <option value="">Выберите резус-фактор</option>
                                <option value="+" <?= $rhesus_factor == '+' ? 'selected' : '' ?>>Положительный (+)</option>
                                <option value="-" <?= $rhesus_factor == '-' ? 'selected' : '' ?>>Отрицательный (-)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@example.com" value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password1">Пароль</label>
                            <input type="password" name="password1" class="form-control" placeholder="**********" value="<?= htmlspecialchars($password1) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password2">Повторите пароль</label>
                            <input type="password" name="password2" class="form-control" placeholder="**********" value="<?= htmlspecialchars($password2) ?>" required>
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                        </div>
                        <div class="form-group">
                            <p class="text-center">Уже есть аккаунт? <a href="login.php" class="text-decoration-none">Войти</a></p>
                        </div>
                    </form>

                    <?php if ($error): ?>
                        <p class="text-center text-danger"><?= $error ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>