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

// 要件
// ・functionの中の動きは気にしなくていいので下の$tempsの中身１５パターン取り出したい。
// 　  やること
// 　　 ┗ $tempsの要素の個数を出す
// 　　　$i < $tempsの数繰り返すfor文の作成
// 　　　配列の中から、１つ取り出す処理
// 　　　配列の中から、2つ取り出す処理
// 　　　配列の中から、3つ取り出す処理
// 　　　配列の中から、4つ取り出す処理
//      を出力


$i = 1;
$temps=kumiawase(array(1,2,3,4),$i); //$iは取り出す桁数。下のforで、1桁→2桁→3桁→4桁の全パターンを取り出す。
$array_amount = count($temps); //配列の中身の数を数える　　数えるために↑が必要


// while($i <= $array_amount){ //$iは1。$array_amountは4。$iが$array_amount以下の場合、この場合は4まで繰り返す。
//     $temps=kumiawase(array(1,2,3,4),$i);  //←ここなぜ再度入れる必要があるか考え中
//     print "<ul>";
//     foreach($temps as $temp){
//         print "<li>".implode($temp)."</li>";
//     }
//     print "</ul>";
//     $i++;
// }


for($i = 1; $i <= $array_amount; $i++){
    $temps=kumiawase(array(1,2,3,4),$i);  //←ここなぜ再度入れる必要があるか考え中
    print "<ul>";
    foreach($temps as $temp){
        print "<li>".implode($temp)."</li>";
    }
    print "</ul>";
}







// print "<ul>";
// foreach($temps as $temp){
//     print "<li>".implode($temp)."</li>";
// }
// print "</ul>";

// var_dump($i.'aa'.'<br>');