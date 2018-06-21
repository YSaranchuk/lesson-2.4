<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<br><a href="index.php">Вернуться к странице авторизации</a>
<br><a href="list.php">Вернуться к выбору тестов</a>
<br><a href="admin.php">Вернуться к главной странице</a>
<?php

if(!empty(session_id($_COOKIE['session_id']))) {
session_start();
}
if (!empty($_SESSION["name"])) {
    if (empty($_SESSION["name"])) 
{ 
    header("refresh: 10; url=list.php");
    http_response_code(403);
    echo "<br><br> 403! Доступ запрещен! <br> Вы будете перемещены назад через 10 секунд!"; 
    exit();
}
}
$all_tests = glob("uploads/*.json");
$number = $_GET["test_number"];
if(!empty($test))
$test = file_get_contents($all_tests[$number]) or exit('Не удалось получить данные');
$decodedTest = json_decode($test, true) or exit('Ошибка декодирования json');
$test = json_decode(file_get_contents($all_tests[$number]), true);
if (is_null($_GET["test_number"])||empty($all_tests[$number]))
{
    http_response_code(404);
    echo "<br><br> 404! Страница не найдена! <br> Вы будете перемещены назад через 10 секунд!";
    header("refresh: 10; url=list.php");
}
if (isset($number)): ?>
    <p>Ответьте, пожалуйста, на вопросы.</p>
    <p>Как будете готовы, нажмите кнопку "Проверить".</p>
    <p>Вы всегда можете вернуться на страницу выбора тестов или их загрузки, нажав внизу по соответствующим ссылкам.</p>
    <p>Помните - на каждый вопрос может быть несколько правильных ответов,  вы должы выбрать их все, невыбранный правильный ответ засчитывается, как ошибка.
Если вы выбрали ошибочный вариант при выбранных правильных, ответ на вопрос вам также не будет засчитан.</p>
    <form method="POST">
        <?php foreach($test as $key => $answers): ?>
            <fieldset>
                <legend><?=$answers["question"];?></legend>
                <br><input type="checkbox" name="answ_1_<?=$key;?>" id="check1"><label for="check1"><?=$answers["answer_1"];?></label><br>
                <br><input type="checkbox" name="answ_2_<?=$key;?>" id="check2"><label for="check2"><?=$answers["answer_2"];?></label><br>
                <br><input type="checkbox" name="answ_3_<?=$key;?>" id="check3"><label for="check3"><?=$answers["answer_3"];?></label><br>
                <br><input type="checkbox" name="answ_4_<?=$key;?>" id="check4"><label for="check4"><?=$answers["answer_4"];?></label><br>
            </fieldset>
        <?php endforeach; ?>
        <input type="submit" name="test_check" value="Проверить">
    </form>
<?php
endif;
$us_answer=[];
foreach ($test as $key => $answers)
{
    $us_answer[$key] = [$_POST["answ_1_$key"],
                        $_POST["answ_2_$key"],
                        $_POST["answ_3_$key"],
                        $_POST["answ_4_$key"]]; 
    if (empty($_POST["answ_1_$key"]) &&
        empty($_POST["answ_2_$key"]) &&
        empty($_POST["answ_3_$key"]) &&
        empty($_POST["answ_4_$key"]))    
    {
        exit ("Необходимо выбрать хотя бы 1 вариант ответа для каждого вопроса");
    }
}
$result=[];
if (isset($_POST["test_check"]))
{
    foreach ($us_answer as $key1=>$value) 
    {
        foreach ($value as $key2=>$otvet) 
        {
            if (!empty($test[$key1]["true"][$key2]) && ($test[$key1]["true"][$key2] !== $us_answer[$key1][$key2]))
            {
                $result[$key1] = "-1"; 
                break;
            }
            else
            {
                $result[$key1] = 100;
            }
            if ((!empty($us_answer[$key1][$key2])) && ($test[$key1]["true"][$key2] !== $us_answer[$key1][$key2]))
            {
                $result[$key1] = "-1";     
                break;
            }
            else
            {
                $result[$key1] = 100;
            }
        }
    }
}
#print_r($result); Если существуют сомнения в правильности итогов - ваш комментарий
$gb=0; 
foreach($result as $key=>$item)
{
    if ($item<0)
    {
        echo "<br>Печалька! Вы <b>неправильно</b> ответили на вопрос<b> ".$test[$key]["question"].".</b><br>";
    }
    else
    {
        echo "<br>УРА! Вы <b>правильно</b> ответили на вопрос<b> ".$test[$key]["question"].".</b><br>";
        $gb++;
        $_SESSION["test"]="done";
    }
}
if (isset($_POST["test_check"])&&$gb>0):?>
    <form action="cert_gen.php" method="POST">
        <fieldset><p>Для получения сертификата нажмите Получить сертификат</p>
            <input type="hidden" name="gb" value="<?=$gb;?>">
        <p><input type="submit" name="get_cert" value="Получить сертификат"></p>
        </fieldset>
    </form>
<?php
endif;
?>
</body>
</html>
