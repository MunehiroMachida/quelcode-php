<?php
$limit = $_GET['target'];


if($limit <= 0
    or $limit != (int)$limit
    or $limit != is_numeric($limit)
    or strpos($limit,'.0')){

    http_response_code(400);
    exit();

}else{
    $dsn = 'mysql:dbname=test;host=mysql';
    $dbuser = 'test';
    $dbpassword = 'test';
    try{
        $db = new PDO($dsn,$dbuser,$dbpassword);
    }catch (PDOException $e){
        echo 'DB接続エラー:'.$e->getMessage();
        http_response_code(404);
        exit();
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
        }elseif($digit > 1){
            $j = 0;
            for($i = 0; $i < $array_number - $digit + 1; $i++){
                $ts = set_array(array_slice($total,$i + 1),$digit - 1); //array_sliceのパラメは(変数,前から何文字目,桁数)、ここ再帰か。
                foreach($ts as $t){
                    array_unshift($t,$total[$i]);
                    $arrs[$j] = $t;
                    $j++;
                }
            } 
        }
        return $arrs;
    }

    //全パターンの組み合わせを$array_boxに入れる処理
    $array_box = [];
    for($i = 1; $i <= $array_amount; $i++){
        $temps = set_array($record_array,$i);
        $array_box = array_merge($array_box,$temps);
    }

    //$limitの組み合わせを新しい配列に入れる処理
    $limit = (int)$limit;
    for($i = 0; $i < count($array_box); $i++){
            if($limit === array_sum($array_box[$i])){
                $sum_limit[] = $array_box[$i];
        }
    }

    //$limitをjsonに変換して出力
    if(is_null($sum_limit)){
        $sum_limit_json = '[]';
    }else{
        $sum_limit_json = json_encode($sum_limit);
    }

    echo($sum_limit_json);
}
