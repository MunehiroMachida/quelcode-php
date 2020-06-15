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

	$retweets_count_table = $db->prepare('SELECT * FROM retweets_count');
	$retweets_count_table->execute();
	$retweets_count_tables = $retweets_count_table->fetchAll();

	if ($message['member_id'] == $_SESSION['id']) {
		// 削除する
		$del = $db->prepare('DELETE FROM posts WHERE id=?');
		$del->execute(array($id));
	}

	$delete_goods = $db->prepare('DELETE FROM goods WHERE post_message=? AND member_id=?');
	$delete_goods->bindParam(1,$message['message']);
	$delete_goods->bindParam(2,$_SESSION['id']);
	$delete_goods->execute();

	$delete_retweets_count = $db->prepare('DELETE FROM retweets_count WHERE  post_message=? AND member_id=?');
	$delete_retweets_count->bindParam(1,$message['message']);
	$delete_retweets_count->bindParam(2,$_SESSION['id']);
	$delete_retweets_count->execute();
	
	if($message['originally_id'] == 0){
		$del_post = $db->prepare('DELETE FROM posts WHERE message=? AND member_id=?');
		$del_post->bindParam(1,$message['message']);
		$del_post->bindParam(2,$message['member_id']);
		$del_post->execute();
	}
}

header('Location: index.php'); exit();
?>
