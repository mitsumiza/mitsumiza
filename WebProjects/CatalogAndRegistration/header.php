
<?php
require_once 'modules.php'; // Подключаем  modules.php
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Спорт-Марафон - Снаряжение для активного отдыха</title>

    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <!-- Подключение Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">

    <!-- Подключение jQuery и Bootstrap JavaScript -->
    <script src="scripts.js"></script>

    <!-- Подключение стилей -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Верхняя навигационная панель -->
    <div class="container">
        <div class="row">
          <div class="col-md-3"></div> 
          <div class="col-md-9 d-flex justify-content-end">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link" href="#">Доставка и оплата</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Контакты</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Сервис</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Блог</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Клуб</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Youtube</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Fest</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Подкасты</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Парк</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">О магазине</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Логотип -->
      <div class="container"> 
        <ul class="nav justify-content-start"> 
          <li class="nav-item d-flex align-items-center"> 
            <img src="image/logo.jpg" alt="Логотип" class="me-2"> 
            <strong><h1>Спорт-Марафон</h1></strong>
          </li>
        </ul>
    </div>




    <!-- Основная навигационная панель -->
    <div class="container">
      <div class="row">
          <nav class="navbar navbar-expand-lg bg-light">
              <div class="container-fluid">
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключатель навигации">
                      <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarNav">
                      <ul class="navbar-nav me-auto"> 
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Новинки</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2 sale" href="#">Sale</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Каталог</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Одежда</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Туризм</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Альпинизм</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Бег</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Горные лыжи</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Сноуборд</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link-2" href="#">Бренды</a>
                          </li>
                      </ul>
                      <ul class="navbar-nav"> 
                          <li class="nav-item">
                            <a class="nav-link-3" href="#">Вход</a>
                        </li>
                        <li class="nav-item">
                          <img src="image/bag.jpg" alt="Корзина" class="icons"> 
                      </li>
                      <li class="nav-item">
                        <img src="image/heart.jpg" alt="Корзина" class="icons"> 
                    </li>
                      <li class="nav-item">
                        <img src="image/zoom.jpg" alt="Корзина" class="icons"> 
                    </li>
                      </ul> 
                  </div>
              </div>
          </nav>
      </div>

      <div class="gradient-line"></div> 

      <div class="text-center">
    <div class="text-center">
      <?php if (isUserLoggedIn()): ?>
        <span>
          Вы вошли как <?= htmlspecialchars($_SESSION['user_name']); ?>!<a href="logout.php">Выйти</a>
        </span>
      <?php else: ?>
        <span>
          Вы не авторизованы. <a href="login.php">Ввести логин и пароль</a> или <a href="signup.php">зарегистрироваться</a>
        </span>
      <?php endif; ?>
    </div>
  </div>

</div>
</nav>
</div>
</div>
</div>
<main>
