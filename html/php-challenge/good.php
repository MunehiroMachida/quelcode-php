<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];
    // 投稿を検査する
    $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
    $messages->execute(array($id));
    $message = $messages->fetch();

    $goods_table = $db->prepare('SELECT * FROM goods');
    $goods_table->execute();
    $goods_tables = $goods_table->fetchAll();

    //レコード数を数えてくれる
    $good_count_table_sql = 'SELECT COUNT(*) FROM goods';
    $stmt = $db->query($good_count_table_sql);
    $good_count_table_amount = (int) $stmt->fetchColumn();

    for ($i = 0; $i < $good_count_table_amount; $i++) {
        if ($goods_tables[$i]['member_id'] === $_SESSION['id'] && $goods_tables[$i]['post_message'] === $message['message']) {
            $is_good = true;
        }
    }

    if ($message['originally_id'] === (string) 0 && $is_good === true) {
        $delete_goods = $db->prepare('DELETE FROM goods WHERE post_message=? AND member_id=?');
        $delete_goods->bindParam(1, $message['message']);
        $delete_goods->bindParam(2, $_SESSION['id']);
        $delete_goods->execute();
    } elseif ($message['originally_id'] > (string) 0 && $is_good === true) {
        $delete_goods = $db->prepare('DELETE FROM goods WHERE post_message=? AND member_id=?');
        $delete_goods->bindParam(1, $message['message']);
        $delete_goods->bindParam(2, $_SESSION['id']);
        $delete_goods->execute();
    } else {
        $plus_goods = $db->prepare('INSERT INTO goods SET post_id=?, post_message=?, member_id=?');
        $plus_goods->bindParam(1, $message['id']);
        $plus_goods->bindParam(2, $message['message']);
        $plus_goods->bindParam(3, $_SESSION['id']);
        $plus_goods->execute();
    }
}
header('Location: index.php');
exit();
