<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
	$id = $_REQUEST['id'];
	
	// 投稿を検査する
	$messages = $db->prepare('SELECT * FROM posts WHERE id=?');
	$messages->execute(array($id));
    $message = $messages->fetch();
    
    // echo'<pre>';
    // var_dump($message);
    // echo'<pre>';

	if ($message['member_id'] == $_SESSION['id']) {
        if($message['is_retweet'] == 0){
            $judgment_retweet = 'NO';
        }elseif($message['is_retweet'] == 1){
            $judgment_retweet = 'YES';
        }
    }
    
    if($judgment_retweet == 'NO'){
        $plus_post = $db->prepare('INSERT INTO posts SET message=?, member_id=?, created=NOW(), is_retweet=1, originally_id=?');
        $plus_post->bindParam(1,$message['message']);
        $plus_post->bindParam(2,$message['member_id']);
        $plus_post->bindParam(3,$message['id']);
        $plus_post->execute();
        // is_retweetを1にしてリツイートしたことにする
        $did_retweet = $db->prepare('UPDATE posts SET is_retweet=1 WHERE id=?');
        $did_retweet->execute(array($id));
    }elseif($judgment_retweet == 'YES'){
        $delete_post = $db->prepare('DELETE FROM posts WHERE originally_id=?');
        $delete_post->bindParam(1,$message['id']);
        $delete_post->execute();
        //リツイートされてたら戻す
        $not_retweet = $db->prepare('UPDATE posts SET is_retweet=0 WHERE id=?');
        $not_retweet->execute(array($id));
    }

    }


header('Location: index.php'); exit();
?>
