<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: text/html; charset=utf-8");
session_start();
if(!empty(session_id($_COOKIE['session_id']))); 
if (empty($_SESSION["name"])) {
    http_response_code(403);
    header("refresh: 5; url=list.php");
    echo "<br><br> 403! Доступ запрещен! <br> Вы будете перемещены назад через 5 секунд!";
    exit();
} 
?>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<br><a href="index.php">Вернуться к странице авторизации</a>
<form method="GET">
    <fieldset>
    <p>Добрый день, <?=$_SESSION["name"];?>!</p>
    <p>Что будем делать сегодня? Пройдем тест, загрузим тест или попробуем создать новый?</p>
    <p>Если вы - гость, то вы можете пройти только тест. Либо вы можете зарегистрироваться и опробывать весь наш ресурс.</p>
    <p><input type="submit" name="testing" value="Выбрать тест для прохождения"></p>
<?php if(empty($_SESSION["guest"])): ?>
    <p><input type="submit" name="upload_test" value="Загрузить тест в базу"></p>
    <p><input type="submit" name="create" value="Создать тест"></p>
<?php endif; ?>
    </fieldset>
</form>
<?php
//Если нажато Выбрать тест для прохождения
if(isset($_GET["testing"]))
{
    header("Location: list.php");
    exit();
}
//Если нажато Загрузить тест
if(isset($_GET["upload_test"]))
{
    header("Location: upload.php");
    exit();
}
//Если нажато создать тест - генератор тестов
if(isset($_GET["create"])): ?>
    <form method="POST">
        <fieldset>
        <p>СОЗДАНИЕ ТЕСТА:</p>
        <p>Тест может состоять из неограниченного числа вопросов.
        <p>У каждого вопроса может быть 4 варианта ответа, в котором от 1 до 4 вариантов ответа могут быть правильными.</p>
        <p>Вначале введите название создаваемого теста или уже существующего теста,
            к которому хотите добавить вопрос.</p>
        <p>Затем введите задаваемый вопрос и варианты ответов в соответствующие поля. Правильные варианты ответов отметьте галочкой.
            Обязательно должны быть внесены все четыре варианта ответов.</p>
        <br><input type="text" name="quiz_name" placeholder="Название теста"><br>
        <br><input type="text" name="question" placeholder="Вопрос"><br>
        <br><input type="checkbox" name="true_1" id="check1"><label for="check1"><input type="text" name="answer_1" placeholder="Ответ 1"></label><br>
        <br><input type="checkbox" name="true_2" id="check2"><label for="check2"><input type="text" name="answer_2" placeholder="Ответ 2"></label><br>
        <br><input type="checkbox" name="true_3" id="check3"><label for="check3"><input type="text" name="answer_3" placeholder="Ответ 3"></label><br>
        <br><input type="checkbox" name="true_4" id="check4"><label for="check4"><input type="text" name="answer_4" placeholder="Ответ 4"></label><br>
        <p><input type="submit" name="save" value="Сохранить тест в базу"></p>
        </fieldset>
    </form>
<?php
//Задаем имя для сохраняемого теста
    if(!empty($_POST["quiz_name"])) {
    $name = $_POST["quiz_name"];
    $file = "uploads/$name.json";
    }
    if (isset($_POST['save']))
    {
        if
        (
            empty($_POST["quiz_name"])||
            empty($_POST["question"])||
            empty($_POST["answer_1"])||
            empty($_POST["answer_2"])||
            empty($_POST["answer_3"])||
            empty($_POST["answer_4"])
        )
        {
            exit("Пожалуйста, заполните все поля формы для добавления теста");
        }
        if
        (
            empty($_POST["true_1"])&&
            empty($_POST["true_2"])&&
            empty($_POST["true_3"])&&
            empty($_POST["true_4"])
        )
        {
            exit ("Пожалуйста, укажите хотя бы один правильный ответ");
        }
        if (file_exists($file))  //Если файл с тестом уже существует
        {
            $testbase=json_decode(file_get_contents($file), true);
            echo "Вопрос <b>".$_POST["question"]."</b> добавлен в тест <b>$name</b>!";
        }
        else
        {
            echo "Тест <b>$name</b> создан и туда добавлен вопрос ".$_POST["question"]."!";
        }
        $quest_data["question"]=$_POST["question"];
        $quest_data["answer_1"]=$_POST["answer_1"];
        $quest_data["true"]=[$_POST["true_1"],$_POST["true_2"],$_POST["true_3"],$_POST["true_4"]]; 
        $quest_data["answer_2"]=$_POST["answer_2"];
        $quest_data["answer_3"]=$_POST["answer_3"];
        $quest_data["answer_4"]=$_POST["answer_4"];
        $testbase[]=$quest_data; 
        file_put_contents($file, json_encode($testbase));
    }
endif; ?>
</body>
</html>
