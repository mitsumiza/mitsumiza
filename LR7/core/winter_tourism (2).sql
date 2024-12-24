-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Окт 22 2024 г., 19:38
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `winter_tourism`
--

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `img_path` varchar(45) NOT NULL DEFAULT 'no_img.png',
  `name` varchar(255) NOT NULL,
  `id_type` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `cost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `img_path`, `name`, `id_type`, `description`, `cost`) VALUES
(1, 'ski1.jpg', 'Лыжи Fischer Transnordic 66 Easy Skin Xtralit', 1, 'Лыжи для бэккантри и скитуринга должны быть надёжными. Многолетняя классика (ранее модель называлась E99 и являлась бестселлером на протяжении 40 лет) создана для экспедиций, позволяет уверенно чувствовать себя в условиях глубокой целины.', 47000),
(2, 'ski2.jpg', 'Туристические лыжи Fischer S-Bound 98 Crown/S', 1, 'Современные универсальные туристические лыжи с насечкой для использования в широком диапазоне условий: от Средней полосы до Заполярья.', 52000),
(3, 'ski3.jpg', 'Туристические лыжи Fischer Spider 62 Crown Xtralite', 1, 'Spider 62 Crown Xtralite – переходный вариант между прогулочными туринговыми лыжами и полноценными туристическими. Целевое использование – лыжные тренировки вне специально подготовленной трассы, лыжное ориентирование, однодневные и походы выходного дня.', 3600),
(4, 'cats1.jpg', 'Кошки TSL Kit Grips Inox 325', 2, 'Дополнительные боковые зубья для моделей TSL 305 и 325. Повышают устойчивость на сложном рельефе, в частности на траверсах и фирновых склонах.', 3270),
(5, 'cats2.jpg', 'Кошки TSL Claws S5-S6 для 225-226', 2, 'Алюминиевые кошки TSL Claws S5-S6 обеспечивают непревзойденное сцепление даже на сложном снежном покрове. Совместимы со снегоступами TSL Expedition Grip 305/325, TSL Tour Grip 305/325, TSL Expedition 305/325, TSL Tour 305/325, TSL Ride 305/325.', 3270),
(6, 'snowshoes1.jpg', 'Снегоступы TSL 325 Elevation Paprika', 2, '325 Elevation – снегоступы с системой крепления стрепы/трещотка для новичков, прокатов и несложных маршрутов. Форма песочных часов, фронтальные зубья и стальные шипы по периметру обеспечивают комфортное передвижение и надёжное сцепление. ', 16260),
(7, 'snowshoes2.jpg', 'Снегоступы детские TSL 302 Freeze Danube', 2, 'TSL 302 Freeze - лёгкие и удобные детские снегоступы с полноценным функционалом для зимних путешествий. Быстрая регулировка платформы под размер обуви и две velcro-липучки для надёжной фиксации ботинка.', 8130),
(8, 'case.jpg', 'Чехол для лыж Atomic Nordic A Sleeve Black', 3, 'Nordic A Sleeve - простой чехол для безопасного хранения и транспортировки ваших лыж и палок. Регулировка Ski Length Adjuster поможет легко отрегулировать длину чехла в зависимости от длины перевозимых лыж.', 1245),
(9, 'camus.jpg', 'Камус Fischer Super Skin Mohair Mix 66', 3, 'Если вы хотите наслаждаться подъемами также, как и спусками, то этот камус — ваш выбор!', 16500),
(10, 'skates.jpg', 'Коньки Lundhags T-Skate Pro Black', 4, 'T-Skate Pro - лёгкие коньки с более коротким носом и радиусом разворота 25 м, а также регулируемой длиной для крепления NIS. Лезвие изготовлено из нержавеющей стали шведского производства. Размер подбирается исходя из длины подошвы ботинка.', 14990),
(11, 'ski-poles.jpg', 'Палки для туристических лыж Fischer OW BC Vario', 5, 'OneWay BC Vario – легкие и прочные алюминиевые палки для туристических лыж.\r\n\r\nМеханизм BC Quick Lock позволяет легко отрегулировать палки на нужную длину одним простым движением. Эксцентриковый зажим надежен и удобен в обслуживании.', 19080),
(12, 'boots1.jpg', 'Ботинки лыжные Alpina Sport Tourer Free Brown/Black', 6, 'Tourer Free – лыжные ботинки для бэккантри.\r\nПодошва средней жёсткости NNN BC и анатомическая стелька обеспечивают идеальный баланс гибкости и поддержки.', 13293),
(13, 'boots2.jpg', 'Ботинки лыжные Alpina Sport Alaska XP Red', 6, 'Ботинки Alaska XP сделают ваши зимние походы ещё приятнее и комфортнее.\r\nБотинки Alaska XP изготовлены из натуральной кожи и дополнены мембраной Alpitex, которая защищает ботинок от попадания в него воды. ', 30723),
(14, 'fasteners1.jpg', 'Крепления Hagan X-Trace 22-23', 7, 'Крепления Hagan X-trace – это современные универсальные крепления для туристических лыж. Основой креплений является гибкая полимерная пластина, выдерживающая низкие температуры и стойкая к механическому воздействию.', 11690),
(15, 'fasteners2.jpg', 'Крепления для туристических лыж Fischer Rottefella Bcx Auto', 7, 'Автоматические крепления BCX Auto для туристических лыж. Усиленная конструкция и широкая направляющая. Крепления подходят только к ботинкам с подошвой BC.', 7950);

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE `types` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `types`
--


(1, 'Туристические лыжи'),
(2, 'Снегоступы'),
(3, 'Аксессуары для туристических лыж'),
(4, 'Туристические коньки'),
(5, 'Палки для туристических лыж'),
(6, 'Ботинки для туристических лыж'),
(7, 'Крепления для туристических лыж');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `blood_group` enum('1','2','3','4') NOT NULL,
  `rhesus_factor` enum('+','-') NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingex_1` (`id_type`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
