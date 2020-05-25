<?php
$limit = $_GET['target'];

if($limit <= 0 or $limit != (integer)$limit or $limit != is_numeric($limit)){
    http_response_code(400);
}else{
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
    
    //一旦valuカラムから全部の数字を取り出して単次元配列にする処理
    $array_amount = count($record);
    for($i = 0; $i < $array_amount; $i++){
        $record_array[] = (int)$record[$i]['value'];
    }
    
    //組み合わせを出す関数
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
    
    
    //全パターンの組み合わせを$array_boxに入れる処理
    for($i = 1; $i <= $array_amount; $i++){
        if(empty($array_box)){
            $temps=kumiawase($record_array,$i);
            $array_box = $temps;
        }elseif(!empty($array_box)){
            $temps=kumiawase($record_array,$i);
            $array_box = array_merge($array_box,$temps);
        }
    }
    
    
    //$limitの組み合わせを新しい配列に入れる処理
    for($i = 0; $i < count($array_box); $i++){
        for($j = 0; $j < count($array_box[$i]); $j++){
            if($limit == array_sum($array_box[$i])){
                $sum_limit[] = $array_box[$i];
                break;
            }
        }
    }
    
    //$limitを出力
    echo'<pre>';
    print_r($sum_limit);
    echo'</pre>';
    
}

