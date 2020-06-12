<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];
	// 投稿を検査する
	$messages = $db->prepare('SELECT * FROM posts WHERE id=?');
	$messages->execute(array($id));
    $message = $messages->fetch();

    $retweets_count_table = $db->prepare('SELECT * FROM retweets_count');
	$retweets_count_table->execute();
	$retweets_count_tables = $retweets_count_table->fetchAll();

	//レコード数を数えてくれる
    $retweet_count_table_sql = 'SELECT COUNT(*) FROM retweets_count';
    $stmt = $db->query($retweet_count_table_sql);
    $retweet_count_table_amount = (int)$stmt->fetchColumn();

    for($i=0;$i<$retweet_count_table_amount;$i++){
        if($retweets_count_tables[$i]['member_id'] == $_SESSION['id'] && $retweets_count_tables[$i]['post_message'] == $message['message']){
            $is_retweet = 'true';
        }
    }

    if($is_retweet == 'true'){
        $delete_post = $db->prepare('DELETE FROM posts WHERE member_id=? AND is_retweet=1 AND originally_id=?');
        $delete_post->bindParam(1,$_SESSION['id']);
        $delete_post->bindParam(2,$id);
        $delete_post->execute();

        $delete_retweets_count = $db->prepare('DELETE FROM retweets_count WHERE post_id=? AND post_message=? AND member_id=?');
        $delete_retweets_count->bindParam(1,$message['id']);
        $delete_retweets_count->bindParam(2,$message['message']);
        $delete_retweets_count->bindParam(3,$_SESSION['id']);
        $delete_retweets_count->execute();
    }else{
        $plus_post = $db->prepare('INSERT INTO posts SET message=?, member_id=?, created=NOW(), is_retweet=1, originally_id=?');
        $plus_post->bindParam(1,$message['message']);
        $plus_post->bindParam(2,$_SESSION['id']);
        $plus_post->bindParam(3,$id);
        $plus_post->execute();

        $plus_retweets_count = $db->prepare('INSERT INTO retweets_count SET post_id=?, post_message=?, member_id=?');
        $plus_retweets_count->bindParam(1,$message['id']);
        $plus_retweets_count->bindParam(2,$message['message']);
        $plus_retweets_count->bindParam(3,$_SESSION['id']);
        $plus_retweets_count->execute();
    }
}


header('Location: index.php'); exit();
?>
