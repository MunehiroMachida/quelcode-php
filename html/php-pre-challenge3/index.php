<?php
$limit = $_GET['target'];
if($limit <= 0 or $limit != (integer)$limit or $limit != is_numeric($limit)){
    http_response_code(400);
    exit();
}else{
    $dsn = 'mysql:dbname=test;host=mysql';
    $dbuser = 'test';
    $dbpassword = 'test';
    try{
        $db = new PDO($dsn,$dbuser,$dbpassword);
    }catch (PDOException $e){
        $DB_error ='DB接続エラー:'.$e->getMessage();
        exit($DB_error);
    }
    $records = $db->query('SELECT value FROM prechallenge3');
    $record = $records->fetchAll(PDO::FETCH_ASSOC);

    //一旦valuカラムから全部の数字を取り出して単次元配列にする処理
    $array_amount = count($record);
        for($i = 0; $i < $array_amount; $i++){
            $record_array[] = (int)$record[$i]['value'];
        }
    //組み合わせを出す関数
    //参考https://stabucky.com/wp/archives/2188
    function set_array($total,$digit){  //$totalが$record_array。$record_arrayは上で単次元に直したやつ。$digitは桁数。
        $array_number = count($total); 
        if($digit === 1){
            for($i = 0; $i < $array_number; $i++){
                $arrs[$i] = array($total[$i]); 
            }
        }elseif($digit>1){
            $j=0;
            for($i=0;$i<$array_number-$digit+1;$i++){
                $ts=set_array(array_slice($total,$i+1),$digit-1); //array_sliceのパラメは(変数,前から何文字目,桁数)、ここ再帰か。
                foreach($ts as $t){
                    array_unshift($t,$total[$i]);
                    $arrs[$j]=$t;
                    $j++;
                }
            } 
        }
        return $arrs;
    }

    //全パターンの組み合わせを$array_boxに入れる処理
    $array_box = [];
    for($i = 1; $i <= $array_amount; $i++){
        if(empty($array_box)){
            $temps=set_array($record_array,$i);
            $array_box = $temps;
        }else{
            $temps=set_array($record_array,$i);
            $array_box = array_merge($array_box,$temps);
        }
    }

    //$limitの組み合わせを新しい配列に入れる処理
    $limit = (int)$limit;
    for($i = 0; $i < count($array_box); $i++){
        for($j = 0; $j < count($array_box[$i]); $j++){
            if($limit === array_sum($array_box[$i])){
                $sum_limit[] = $array_box[$i];
                break;
            }
        }
    }

    //$limitを出力
    $sum_limit_json = json_encode($sum_limit);
    echo'<pre>';
    print_r($sum_limit_json);
    echo'</pre>';
    }
