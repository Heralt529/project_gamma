<?php
// front.php - модуль для главной страницы

function front_get($request) {
    // Возвращаем HTML-контент главной страницы
    return theme('main_page');
}
?>
