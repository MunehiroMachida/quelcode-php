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


function kumiawase($zentai,$nukitorisu){
    $zentaisu=count($zentai);
    if($zentaisu<$nukitorisu){
        return;
    }elseif($nukitorisu==1){
        for($i=0;$i<$zentaisu;$i++){
            $arrs[$i]=array($zentai[$i]);
        }
        }elseif($nukitorisu>1){
        $j=0;
        for($i=0;$i<$zentaisu-$nukitorisu+1;$i++){
            $ts=kumiawase(array_slice($zentai,$i+1),$nukitorisu-1);
            foreach($ts as $t){
            array_unshift($t,$zentai[$i]);
            $arrs[$j]=$t;
            $j++;
            }
        }
        }
        return $arrs;
    }


$items = [1,2,3,4];
$array_amount = count($items);
$array_box = [];


for($i = 1; $i <= $array_amount; $i++){
    if(empty($array_box)){
        $temps=kumiawase($items,$i);
        $array_box = $temps;
    }elseif(!empty($array_box)){
        $temps=kumiawase($items,$i);
        $array_box = array_merge($array_box,$temps);
    }
    

}

echo'<pre>';
print_r($array_box);
echo'</pre>';


// $temp = [];
// $temp[] = [1];
// $temp[] = [2];
// $temp[] = [3];
// $temp[] = [1,2];




// echo'<pre>';
// print_r($temp);
// echo'</pre>';