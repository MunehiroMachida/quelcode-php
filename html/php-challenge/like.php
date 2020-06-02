<?php
session_start();
require('dbconnect.php');

// var_dump($_REQUEST['id']); 

if (isset($_SESSION['id'])) {//ここはmemberのid。
    $id = $_REQUEST['id'];//これはpostsのid
	
	// 投稿を検査する
	$goods = $db->prepare('SELECT * FROM goods WHERE id=?');
	$goods->execute(array($id));
	$good = $goods->fetch();

        //goodsテーブルに値が入っていて、member_idと操作してる人が一緒で、投稿のidとリクエストで飛んできたidが一緒の時
        // if(!empty($good) && $good['member_id'] == $_SESSION['id'] && $good['post_id'] == $id){
        //     //消す。つまりいいねではない状態にする
        //     $del = $db->prepare('DELETE FROM goods WHERE id=?');
        //     $del->execute(array($id));
        // }
        // else{
        //     ///そうではないときいいね状態にする。
        //     $insert = $db->prepare('INSERT INTO goods SET $_GET');
        //     $insert->execute(array($id));
        // }
        

// とりあえず、いいねテーブルに打ち込みたい

//6/3やること。挙動が少しおかしい。データベースに投稿のidとmenberidを入れることは成功


    if(empty($good)){
            $insert = $db->prepare('INSERT INTO goods SET post_id=?, member_id=?');
            $insert->bindParam(1,$_GET['id']);
            $insert->bindParam(2,$_SESSION['id']);
            $insert->execute();
    }
}

header('Location: index.php'); exit();
?>
