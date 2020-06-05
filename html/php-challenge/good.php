<?php
session_start();
require('dbconnect.php');

// var_dump($_REQUEST['id']); 

if (isset($_SESSION['id'])) {//ここはmemberのid。
    $id = $_REQUEST['id'];//これはpostsのid
	
	// 投稿を検査する
	$goods = $db->prepare('SELECT * FROM goods');
	$goods->execute(array($id));
    $good = $goods->fetchAll(PDO::FETCH_ASSOC);

    //レコード数を数えてくれる
    $count_sql = 'SELECT COUNT(*) FROM goods';
    $stmt = $db->query($count_sql);
    $count = (int)$stmt->fetchColumn();


    $is_good['session'] = $_SESSION['id'];
    $is_good['get'] = $_GET['id'];

    $is_ture = '';
    if(empty($good)){ //何も値が入っていない時。
        $insert = $db->prepare('INSERT INTO goods SET post_id=?, member_id=?');
        $insert->bindParam(1,$_GET['id']);
        $insert->bindParam(2,$_SESSION['id']);
        $insert->execute();
    }elseif(!empty($good)){//値が入っている
        for($i=0;$i<$count;$i++){ //同じ値があるかけんさ
            if($is_good['get'] == $good[$i]['post_id'] && $is_good['session'] == $good[$i]['member_id']){
                $is_ture = 'true';
                $goods_id = $good[$i]['id'];
            }
        }
        if($is_ture == 'true'){
            $del = $db->prepare('DELETE FROM goods WHERE id=?');
            $del->execute(array($goods_id));
        }elseif($is_ture != 'true'){
            $insert = $db->prepare('INSERT INTO goods SET post_id=?, member_id=?');
            $insert->bindParam(1,$_GET['id']);
            $insert->bindParam(2,$_SESSION['id']);
            $insert->execute();
        }
    }

}

header('Location: index.php'); exit();
?>
