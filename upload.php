<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<br><a href="index.php">Вернуться к странице авторизации</a>
<br><a href="list.php">Вернуться к выбору тестов</a>
<br><a href="admin.php">Вернуться к главной странице</a>
<?php
error_reporting(0);
session_id($_COOKIE['session_id']);
session_start();
if ($_SESSION["auth"]!=="yes")
{
    http_response_code(403);
    echo "<br><br> 403! Доступ запрещен! <br> Вы будете перемещены назад через 10 секунд!";
    header("refresh: 10; url=index.php");
    exit();
}
?>
<form method="POST" enctype=multipart/form-data>
    <fieldset>
    <input type=file name=testfile>
    <p>Пожалуйста, выберите файл с тестом в формате JSON для загрузки в базу</p>
        <p>ВАЖНО! <br>
            <br> В загружаемом файле должно быть строго:
            <br> 1.Вопрос, с ключом "question".
            <br> 2.Четыре варианта ответов, ключи которых названы "answer_1"-"answer_4".
            <br> 3.Подмассив "true" с 4 элементами без ключей, где позиции правильных ответов помечены фразой "on".
            <br> В случае несоответствия данным требованиям файл загружен не будет.
        </p>
    <input type=submit name=upload value=Загрузить>
    </fieldset>
</form>
<?php
$path_info = pathinfo("base/".($_FILES["testfile"]["name"]));
//Задаем путь для сохраняемого теста
if (isset($_POST["upload"]))
{
    if(!empty($_FILES["testfile"])) { //внесла правки
    if (is_file("base/".$_FILES["testfile"]["name"])) //Есть ли уже файл с таким именем
    {
        echo "Извините, тест с таким именем уже существует";
    }
}
    elseif ($path_info["extension"] === "json") //Проверка расширения файла
    {
        $test_test = json_decode(file_get_contents($_FILES["testfile"]["tmp_name"]), true); 
        foreach ($test_test as $k=>$i)
        {
            if
            (
                !array_key_exists("question", $i)||
                !array_key_exists("answer_1",$i)||
                !array_key_exists("answer_2",$i)||
                !array_key_exists("answer_3",$i)||
                !array_key_exists("answer_4",$i)||
                !array_key_exists("true",$i)||
                !in_array("on",$i["true"])
            )
            {
                exit ("Извините, ваш тест не соответствует заданным требованиям");
            }
        }
        if (move_uploaded_file(($_FILES["testfile"]["tmp_name"]), "uploads/".($_FILES["testfile"]["name"])))
        {
            header("refresh: 10; url=list.php");
            echo "Спасибо, Ваш тест загружен! Вы будете перенаправлены на страницу выбора тестов через 10 секунд.";
        }
        else
        {
            echo "Ошибка при сохранении теста";
        }
    }
    else
    {
        echo "Извините, не обнаружен файл с расширением JSON";
    }
}
?>
</body>
</html>

