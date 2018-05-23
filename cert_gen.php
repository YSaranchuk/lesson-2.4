<?php
error_reporting(0);
session_id($_COOKIE['session_id']);
session_start();
if (empty($_SESSION["name"])||($_SESSION["test"]!=="done")) 
{
    http_response_code(403);
    echo "<br><br> 403! Доступ запрещен! <br> Вы будете перемещены назад через 10 секунд!";
    header("refresh: 10; url=index.php");
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
