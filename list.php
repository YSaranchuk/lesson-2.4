<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<br><a href="index.php">Вернуться к странице авторизации</a>
<br><a href="admin.php">Вернуться к главной странице</a>

<?php
error_reporting(0);
session_id($_COOKIE['session_id']);
session_start();
//Находим все имеющиеся тесты в заданной папке, определяя их в массив all_files
$all_files = glob('uploads/*.json');
if (!empty($all_files)): //Проходим массив, выводя для каждого теста его номер, через который он будет загружаться в обработчик
    foreach ($all_files as $file): ?>
    <form action="test.php" method="GET">
        <fieldset>
        <legend><?=str_replace(array("uploads/",".json"), '', $file);?></legend>
        <p><input type="hidden" name="test_number" value="<?=array_search($file, $all_files);?>"></p>
        <p><input type="submit" name="get_testing" value="Пройти тестирование"></p>
        </fieldset>
    </form>
<?php if($_SESSION["auth"]==="yes"): ?>
    <form method="GET">
        <p>Вы можете удалить этот тест, нажав на кнопку ниже</p>
        <p><input type="submit" name="delete" value="Удалить тест <?=str_replace(array("uploads/",".json"), '', $file);?>"></p>
    </form>
<?php
endif;
    if (isset($_GET['delete']))
    {
        unlink($file);
        exit("Тест удален!"."<meta http-equiv='refresh' content='0; url= $_SERVER[PHP_SELF]'>");
    }
    endforeach;
    endif;
if (empty($all_files))
{
    echo "Пока не загружено ни одного теста";
}
?>
</body>
</html>
