<?php

function processText($text) {
    $output = '';

    // Создаем экземпляр DOMDocument
    $doc = new DOMDocument();
    @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $text);

    // Сохраняем исходный текст для задач, которые зависят от оригинала
    $html_text = $text;

    // Задача 5: Замена тире
    //$formattedText = formatHyphens($html_text);
    //$output .= '<div class="task"><p>Задание 5: Текст с отформатированными тире:</p><p>' . $formattedText . '</p></div><br>';

    // Задача 6: Расставляем запятые и заменяем многоточия на исходном тексте
    //$formattedText = formatCommasAndEllipses($html_text);
    //$output .= '<div class="task"><p>Задание 6: Текст после проставления запятых перед "а" и "но":</p><p>' . $formattedText . '</p></div><br>';

    //Задача 13: Создаем указатель изображений
    //$output .= '<div class="task"><p>Задание 13: Указатель изображений:</p>' . createImageIndex($doc) . '</div><br>';

    //Задача 17: Выделение повторов
    $formattedText = highlightTechnicalRepeats($html_text); 
    $output .= '<div class="task"><p>Задание 17: Текст с подсвеченными повторами:</p><p>' . $formattedText . '</p></div><br>';

    // Введённый текст после всех преобразований (итоговый вывод)
    //$output .= '<div class="text"><h3>Введённый текст после всех преобразований:</h3><p>' . formatCommasAndEllipses($html_text) . '</p></div><br>'; 

    return $output;
}

// Сохранение исходного текста
function extractAndLinkImages($doc) {
    $images = $doc->getElementsByTagName('img');
    $result = '';
    $index = 1;

    foreach ($images as $img) {
        // Добавляем id для каждой картинки
        $img->setAttribute('id', 'img' . $index);
        // Выводим изображение как HTML (в виде строки с тегом <br> в конце)
        $result .= $doc->saveHTML($img) . "<br>";
        $index++;
    }

    return $result ?: '<p>Картинок не найдено</p>';
}

// Задание 5: Тире
function formatHyphens($text) {
    // Замена тире в пробелах на среднее тире
    $text = preg_replace('/\s-\s/', ' &ndash; ', $text);

    // Замена двойного минуса на длинное тире и привязка к предыдущему слову
    $text = preg_replace('/\s--\s/', ' &mdash;', $text);

    return $text;
}

// Задание 6: Запятые и Многоточия
function formatCommasAndEllipses($text) {
    // Заменяем пробел перед союзами "а" и "но" на запятую с пробелом
    $text = preg_replace('/\s+(а|но)\b/iu', ', $1', $text);

    // Заменяем многоточие на символ …
    $text = str_replace('...', '…', $text);

    return $text;
}

// Задание 13: Указатель украшений
function createImageIndex($doc) {
    $images = $doc->getElementsByTagName('img');
    $index = '';
    $i = 1;

    foreach ($images as $img) {
        $alt = $img->getAttribute('alt');
        $index .= "<p><a href=\"#img$i\">Картинка $i: $alt</a></p>";
        $i++;
    }

    return $index ?: '<p>Картинок для указателя не найдено</p>';
}

// Задание 17: Выделение повторов
function highlightTechnicalRepeats($text) {
  $words = explode(' ', $text); // Разбиваем текст по пробелам
  $highlightedWords = [];

  foreach ($words as $word) {
    $parts = explode('-', $word); // Разбиваем слово по дефисам
    $highlightedParts = [];

    $previousPart = null;
    foreach ($parts as $part) {
      if ($previousPart !== null && $part === $previousPart) {
        $highlightedParts[] = "<span style='background-color: yellow;'>$part</span>";
      } else {
        $highlightedParts[] = $part;
      }
      $previousPart = $part;
    }

    $highlightedWords[] = implode('-', $highlightedParts);
  }

  return implode(' ', $highlightedWords);
}

?>
