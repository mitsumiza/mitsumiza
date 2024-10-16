<?php

// Подключение файла с логикой
require_once 'logic.php';
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
  <link rel="stylesheet" href="bootstrap.min.css">  
    <!-- Подключение Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">

    <!-- Подключение стилей -->
    <link rel="stylesheet" href="style.css">

    <!-- Подключение jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Подключение Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q" crossorigin="anonymous"></script>

    <!-- Скрипт для автозаполнения -->
    <script>
    $(document).ready(function() {
        // Автозаполнение для поля "Название товара"
        $("#search_name").autocomplete({
            source: <?php echo json_encode($productNames); ?> // Предполагается, что у вас есть массив $productNames в logic.php
        });
    });
    </script>
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

<!-- Фильтр -->

<form method="get">
        <label for="name">Название:</label>
        <input type="text" name="name" id="name" value="<?php echo $_GET['name'] ?? ''; ?>">

        <label for="type">Категория:</label>
        <select name="type" id="type">
            <option value="">Все</option>
            <?php foreach ($types as $type): ?>
                <option value="<?php echo $type; ?>" <?php if ($_GET['type'] == $type) echo 'selected'; ?>><?php echo $type; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="price_min">Цена от:</label>
        <input type="number" name="price_min" id="price_min" value="<?php echo $_GET['price_min'] ?? ''; ?>">

        <label for="price_max">Цена до:</label>
        <input type="number" name="price_max" id="price_max" value="<?php echo $_GET['price_max'] ?? ''; ?>">

        <button type="submit">Фильтровать</button>
        <button type="submit" name="clearFilter">Очистить фильтр</button>
    </form>




<!-- Результаты поиска -->
<div class="container mt-3">
    <?php if ($result->num_rows > 0): ?> 
        <div class="row">
            <?php
            while($row = $result->fetch_assoc()) {
                $image_name = basename($row['img_path']);
                echo "
                <div class='col-md-4 mb-4'>
    <div class='card'>
        <img src='catalog_images/$row[img_path]' class='card-img-top' alt='$row[name]'> 
        <div class='card-body'>
            <h5 class='card-title'>$row[name]</h5>
            <p class='card-text'>$row[description]</p>
            <p class='card-text'>Категория: $row[type_name]</p>
            <p class='card-text'>Цена: $row[cost] руб.</p>
            <a href='product.php?id=$row[id]' class='btn btn-primary'>Подробнее</a>
        </div>
    </div>
</div>


                ";
            }
            ?>
        </div>
    <?php else: ?>
        <p class="mt-3">Товаров, соответствующих вашим критериям, не найдено.</p>
    <?php endif; ?>
</div>


    <!-- О команде -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="image/icon1.jpg" alt="Иконка 1" class="card-img-top">
                    <div class="card-body">
                        <p class="card-text">Вся команда нашего магазина увлекается активными видами спорта: туризмом, альпинизмом, горными лыжами и другими видами outdoor-активности.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="image/icon2.jpg" alt="Иконка 2" class="card-img-top">
                    <div class="card-body">
                        <p class="card-text">Оплата наличными курьеру или банковской картой без комиссии.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="image/icon3.jpg" alt="Иконка 3" class="card-img-top">
                    <div class="card-body">
                        <p class="card-text">Коротко о самом важном: новых коллекциях и брендах, о снаряжении и как его выбрать, ближайших акциях и распродажах.
                            <br> 
                            <form class="d-flex" role="search">
                                <input class="form-control me-2" type="search" placeholder="Ваш e-mail" aria-label="Ваш e-mail">
                                <button class="btn btn-outline-success" type="submit" disabled style="opacity: 0.5; pointer-events: none; cursor: default; background-color: #ccc; border-color: #ccc; color: #333;">Подписаться</button> 
                            </form>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ошибка -->
    <div class="container mt-5">    
        <p>Заметили ошибку? Выделите текст ошибки, нажмите Ctrl+Enter, отправьте форму. Мы постараемся исправить ее.</p>
    </div> 

    <!-- Подвал -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-1">
                <!-- Здесь будут колонки, но текст будет в таблице -->
            </div>
            <div class="col-md-1">
                <!-- Здесь будут колонки, но текст будет в таблице -->
            </div>
            <div class="col-md-1">
                <!-- Здесь будут колонки, но текст будет в таблице -->
            </div>
            <div class="col-md-1">
                <!-- Здесь будут колонки, но текст будет в таблице -->
            </div>
            <div class="col-md-1">
                <!-- Здесь будут колонки, но текст будет в таблице -->
            </div>
            <div class="col-md-1">
                <!-- Здесь будут колонки, но текст будет в таблице -->
            </div>
            <div class="col-md-3"></div>
        </div>

        <!-- Таблица -->
        <table>
            <thead>
                <tr>
                    <th style="width: 200px;">КАТАЛОГ</th>
                    <th style="width: 200px;">МАГАЗИН</th>
                    <th style="width: 200px;">СЕРВИС</th>
                    <th style="width: 200px;">СООБЩЕСТВО</th>
                    <th style="width: 200px;">ИНФОРМАЦИЯ</th>
                    <th style="width: 200px;">КОНТАКТЫ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Акции</td>
                    <td>Контакты</td>
                    <td>Персональная консультация</td>
                    <td>Блог</td>
                    <td>Дисконтная программа</td>
                    <td>Москва, ул. Сайкина 4</td>
                </tr>
                <tr>
                    <td>Новинки</td>
                    <td>О нас</td>
                    <td>Ски-сервис</td>
                    <td>Клуб</td>
                    <td>Доставка и оплата</td>
                    <td>ежедневно с 10:00 до 24:00</td>
                </tr>
                <tr>
                    <td>Активности</td>
                    <td>Команда</td>
                    <td>Бутфитинг</td>
                    <td>YouTube</td>
                    <td>Обмен и возврат</td>
                    <td>8 (800) 333-14-41</td>
                </tr>
                <tr>
                    <td>Бренды</td>
                    <td>Вакансии</td>
                    <td>Мастерская бега</td>
                    <td>Подкасты</td>
                    <td>Осторожно, мошенники</td>
                    <td>бесплатный звонок по России</td>
                </tr>
                <tr>
                    <td>Лукбук</td>
                    <td></td>
                    <td></td>
                    <td>Outdoor Fest в Никола-Ленивце</td>
                    <td>Оферта</td>
                    <td>Мы в социальных сетях</td>
                </tr>
                <tr>
                    <td>Идеи подарков</td>
                    <td></td>
                    <td></td>
                    <td>Проекты в Красной Поляне</td>
                    <td></td>
                    <td>Наши каналы</td>
                </tr>
                <tr>
                    <td>Подарки для ваших сотрудников</td>
                    <td></td>
                    <td></td>
                    <td>Парк</td>
                    <td></td>
                    <td>
                        <img src="image/vk.jpg" alt="" class="social-media-icon">
                        <img src="image/youtube.jpg" alt="YouTube" class="social-media-icon">
                        <img src="image/music.jpg" alt="Музыка" class="social-media-icon">
                        <img src="image/tg.jpg" alt="Telegram" class="social-media-icon">
                    </td>
                </tr>
                <tr>
                    <td>Библиотека Спорт-Марафон</td>
                    <td></td>
                    <td></td>
                    <td>Школа туризма</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <!--Права и картинки-->
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3"></div>
                <div class="col-md-2">
                    <p class="mb-0">Все права защищены. 2012-2024 © Спорт-Марафон</p>
                </div>
                <div class="col-md-2 text-center">
                    <img src="image/rating.jpg" alt="Рейтинг" class="img-fluid">
                </div>
                <div class="col-md-2 text-center">
                    <img src="image/lebedev-logo.jpg.jpg" alt="Логотип Lebedev Studio" class="img-fluid">
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap JavaScript -->
    <script src="bootstrap.bundle.min.js"></script>
</body>
</html>