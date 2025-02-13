<?php
require 'modules.php';

if (isUserLoggedIn()) {
    header("tourism.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = loginUserWithCookies($email, $password, $pdo);

    if ($result === true) {
        header("Location: tourism.php");
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
            <p class="col py-2">
                <a href="/un_site_main/LR1/index.html" class="text-decoration-none">Домашняя страница</a>
                > <b>Вход в аккаунт</b>
            </p>
        </div>
    </div>
    <div class="flex-grow-1 d-flex align-items-center mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <h1 class="mb-4">Вход</h1>
                    <form action="login.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@example.com" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password">Пароль</label>
                            <input type="password" name="password" class="form-control" placeholder="**********" required>
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary w-100">Войти</button>
                        </div>
                        <div class="form-group">
                            <p class="text-center">Ещё нет аккаунта? <a href="signup.php" class="text-decoration-none">Зарегистрируйтесь</a></p>
                        </div>
                    </form>
                    <?php if (isset($error)): ?>
                        <p class="text-center" style="color:red;"><?= $error ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require 'footer.php'; ?>