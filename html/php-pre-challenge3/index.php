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


for($i = 0; $i <= count($array); $i++){
    if($i < count($array)){
        print_r($array[$i].'<br>');
    }else{
        echo "<pre>";
        print_r($array[$i-$i].','.$array[$i-1]);
        echo "</pre>";
    }
}
