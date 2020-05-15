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
    print($record[0]."\n");

echo "<pre>";
print_r($record);
echo "</pre>";