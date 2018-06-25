<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header("Content-Type: text/html; charset=utf-8");
session_start();

if(!empty($_COOKIE['session_id'])) {
    session_id($_COOKIE['session_id']);
}


if (empty($_SESSION["name"])) {
    http_response_code(403);
    header("refresh: 5; url=list.php");
    echo "<br><br> 403! Доступ запрещен! <br> Вы будете перемещены назад через 5 секунд!";
    exit();
} 
$font='FONT.ttf'; 
header("Content-type: image/png"); 
$gb=$_POST["gb"];
$text = $_SESSION["name"];
$im = imagecreatefrompng("certificate.png");
$color = imagecolorallocate($im, 0, 0, 0);
imagettftext ($im, 20, 0, 100, 500, $color, $font, $text." Прошел тест и ответил правильно на следующее количество вопросов: $gb");
imagepng($im);//Выводим изображение
imagedestroy($im);
?>
