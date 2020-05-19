<?php
$limit = $_GET['target'];

$dsn = 'mysql:dbname=test;host=mysql';
$dbuser = 'test';
$dbpassword = 'test';
try{
    $db = new PDO($dsn,$dbuser,$dbpassword);
}catch (PDOException $e){
    echo 'DB接続エラー:'.$e->getMessage();
}
$records = $db->query('SELECT value FROM prechallenge3');
$record = $records->fetchAll(PDO::FETCH_ASSOC);



$array = [1,2];


$matome_str = '';
for($i = 0; $i <= count($array); $i++){
    if($i < count($array)){
        $str = (string)$array[$i];
        echo "<pre>";
        var_dump($str);
        echo "</pre>";
        $matome_str .= $array[$i] . ',';


    }elseif($i == count($array)){
        $matome_str = substr($matome_str, 0, -1);
        echo "<pre>";
        var_dump($matome_str);
        echo "</pre>";
    }
}

