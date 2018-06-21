<?php

if (empty($_COOKIE['session_id']))
{
    session_start();
    setcookie('session_id', session_id(), time() + 7200); 
}
else
{
    session_id($_COOKIE['session_id']); 
    session_start();
}
$current_time=time();
if (!empty($_SESSION["blocked_time"]))
{
    if ($_SESSION["blocked_time"]>$current_time)
    {
        exit ("Вы были заблокированы на один час.");
    }
    if($_SESSION["blocked_time"]<=$current_time)
    {
        unset($_SESSION["blocked_time"]);
    }
}
if(!empty($_SESSION["mistakes"])) 
    if ($_SESSION["mistakes"]>6): ?>
        <form method="GET" enctype="multipart/form-data">
            <img src='captcha.php' id='capcha-image'>
            <a href="javascript:void(0);" onclick="document.getElementById('capcha-image').src='captcha.php?rid=' + Math.random();"><br>Обновить изображение</a>
            <p><br>Вы ошиблись слишком много раз. <br> Введите текст, указанный на изображении:</p>
            <input type="text" name="code" required>
            <input type="submit" name="cap_test" value="Продолжить">
        </form>
    
<?php endif;
if (isset($_GET["cap_test"])&&$_GET["code"]===$_SESSION["cod"])
{
    echo "<br>Вы прошли проверку на капчу!";
    $_SESSION["mistakes"]-=10;
}
if (isset($_GET["cap_test"])&&$_GET["code"]!==$_SESSION["cod"])
{
    echo "<br>Вы не прошли проверку на капчу!";
    $_SESSION["mistakes"]++;
}
//Механизм блокировки пользователя на час после 11 ошибок
if(!empty($_SESSION[‘mistakes’])) {
    if($_SESSION["mistakes"]>11)
    {
        $_SESSION["blocked_time"] = $current_time + 3600;
        exit();
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
</head>
<body>
<form method="POST">
    <fieldset>
    <p>Добрый день!</p>
    <p>Пожалуйста, введите ваше имя и пароль и нажмите "Вход".</p>
    <p>Если вы первый раз на этом сайте, нажмите на клавишу "Зарегистрироваться".</p>
    <p><input type="text" name="login" placeholder="Имя"></p>
    <p><input type="password" name="password" placeholder="Пароль"></p>
<?php
if ($_SESSION["mistakes"]<=6): ?>
    <p><input type="submit" name="sign_in" value="Вход"></p>
<?php
endif;
?>
    <p><input type="submit" name="sign_up" value="Регистрация"></p>
    </fieldset>
    <fieldset>
    <p>Вы также можете продолжить просмотр сайта в качестве гостя, но при этом у вас будут ограничены полномочия на редактирование контента.</p>
    <p>Вам все равно необходимо указать ваше имя.</p>
    <p><input type="submit" name="guest" value="Войти как гость"></p>
    </fieldset>
</form>
</body>
</html>
<?php
if (isset($_POST["sign_in"])||
    isset($_POST["sign_up"])||
    isset($_POST["guest"]))
{
    if(empty($_POST["login"]))
    {
        exit ("<b>Необходимо ввести ваше имя!</b>");
    }
}
$_SESSION["name"]=$_POST["login"];
if (isset($_POST["guest"]))
{
    $_SESSION["guest"]='yes';
    unset($_SESSION["auth"]);
    header("Location: admin.php");
    exit;
}
if (isset($_POST["sign_up"]))
{
    $users_base = json_decode(file_get_contents("users.json"), true);
    if ((empty($_POST["login"]))||(empty($_POST["password"])))
    {
        exit("Введите, пожалуйста, желаемые имя и пароль");
    }
    foreach ($users_base as $k=>$users)
    {
        if ($users["login"]==$_POST["login"])
        {
            exit ("Пользователь с таким именем уже зарегистрирован!");
        }
    }
    $user["login"]=$_POST["login"];
    $user["password"]=$_POST["password"];
    $users_base[]=$user;
    file_put_contents("users.json", json_encode($users_base));
    echo "Поздравляю с регистрацией,".$_POST["login"];
    $_SESSION['auth']='yes';
    unset($_SESSION["guest"]);
    header("refresh: 10; url=admin.php");
    exit;
}
if (isset($_POST["sign_in"]))
{
    $users_base = json_decode(file_get_contents("users.json"), true);
    if ((empty($_POST["login"]))||(empty($_POST["password"])))
    {
        exit("Введите, пожалуйста, ваше имя (логин) и пароль");
    }
    foreach ($users_base as $k=>$users)
    {
        if ($users["login"]==$_POST["login"]&&$users["password"]==$_POST["password"])
        {
                $_SESSION['auth']='yes'; 
                if(!empty($_SESSION["guest"]))
                header("refresh: 10; url=admin.php");
                unset($_SESSION["guest"]);
                
                echo "Добро пожаловать, " . $_SESSION["name"];
                echo "<br>Через 10 секунд вы будете перенаправлены на главную страницу";
                exit;
        }
    }
    $_SESSION["mistakes"]++; 
    exit ("Ваш логин или пароль введен неверно! Попробуйте еще раз!");
}
?>
