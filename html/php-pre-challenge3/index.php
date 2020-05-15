<?php
$limit = $_GET['target'];

$dsn = 'mysql:dbname=test;host=mysql';
$dbuser = 'test';
$dbpassword = 'test';

try{
    $db = new PDO($dsn,$dbuser,$dbpassword);
    echo "<p>DB接続に成功しました。</p>";
    // SQL実行	
    $sql = "SELECT value FROM prechallenge3";
    $stmt = $db->prepare($sql);	//prepareでSQL文を実行
    $stmt->execute();	//executeは実行するっていう意味
    // 結果の取得	
    $record = $stmt->fetchAll(PDO::FETCH_ASSOC);

}catch (PDOException $e){
    echo 'DB接続エラー:'.$e->getMessage();
    
}

echo "<pre>";
print_r($record);
echo "</pre>";




