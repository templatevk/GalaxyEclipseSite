<?php
//Запускаем сессию
session_start();
$text=implode(" ", str_split($_SESSION['uid']));
//Создаем изображение из 3-х возможных подложек
$im=ImageCreateFromJpeg(round(mt_rand(1,3)).".jpg");
//Берём шрифт из 3-х возможных подложек
$fo="addict".round(mt_rand(1,3)).".ttf";
//Генерируем цвет надписи
$color=ImageColorAllocate($im,mt_rand(1,100),mt_rand(0,100),mt_rand(0,100));
//Формируем надпись, используя шрифт
ImageTtfText($im, 23, mt_rand(-3,3), 20, 22, $color, $fo,  $text);
//Указываем тип содержимого
Header("Content-type: image/jpeg");
//Создаем и выводим изображение
ImageJpeg($im);
//Разрушаем изображение
ImageDestroy($im);
?>