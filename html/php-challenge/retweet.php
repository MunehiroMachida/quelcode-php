<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {//ここはmemberのid。
    $id = $_REQUEST['id'];//これはpostsのid
	
	// 投稿を検査する
	$retweets = $db->prepare('SELECT * FROM retweets_count');
	$retweets->execute(array($id));
    $retweet = $retweets->fetchAll(PDO::FETCH_ASSOC);

    //レコード数を数えてくれる
    $count_sql = 'SELECT COUNT(*) FROM retweets_count';
    $stmt = $db->query($count_sql);
    $count = (int)$stmt->fetchColumn();


    $is_retweet['session'] = $_SESSION['id'];
    $is_retweet['get'] = $_GET['id'];

    $is_ture = '';
    if(empty($retweet)){ //何も値が入っていない時。
        $insert = $db->prepare('INSERT INTO retweets_count SET post_id=?, member_id=?');
        $insert->bindParam(1,$_GET['id']);
        $insert->bindParam(2,$_SESSION['id']);
        $insert->execute();
    }elseif(!empty($retweet)){//値が入っている
        for($i=0;$i<$count;$i++){ //同じ値があるかけんさ
            if($is_retweet['get'] == $retweet[$i]['post_id'] && $is_retweet['session'] == $retweet[$i]['member_id']){
                $is_ture = 'true';
                $retweets_id = $retweet[$i]['id'];
            }
        }
        if($is_ture == 'true'){
            $del = $db->prepare('DELETE FROM retweets_count WHERE id=?');
            $del->execute(array($retweets_id));
        }elseif($is_ture != 'true'){
            $insert = $db->prepare('INSERT INTO retweets_count SET post_id=?, member_id=?');
            $insert->bindParam(1,$_GET['id']);
            $insert->bindParam(2,$_SESSION['id']);
            $insert->execute();
        }
    }


    // var_dump($_GET['id']);

    //postsテーブルのカラムを1にして、postsテーブルを複製
    if (isset($_SESSION['id'])) {
        $id = $_REQUEST['id'];
        
        // 投稿を検査する
        $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
        $messages->execute(array($id));
        $message = $messages->fetch();

        // echo'<pre>';
        // print_r($message);
        // echo'</pre>';

        // echo'<pre>';
        // print_r($id);
        // echo'</pre>';

        // 色を変える
        if($message['is_retweet'] == 0){
            $update = $db->prepare('UPDATE posts SET is_retweet=1 WHERE id=?');
            $update->bindParam(1,$message['id']);
            $update->execute();
            //リツイートされたやつをリツイートする時
            if($message['the_retweeted_side'] == 1){
                $insert = $db->prepare('INSERT INTO posts SET message=?, member_id=?,created=NOW(),is_retweet=0,the_retweeted_side=1,originally_id=?,whose_retweet=?');
                $insert->bindParam(1,$message['message']);
                $insert->bindParam(2,$_SESSION['id']);
                $insert->bindParam(3,$message['id']);
                $insert->bindParam(4,$message['whose_retweet']);
                $insert->execute();
            }else{
                //リツイートされていないやつをリツイートする時
                $insert = $db->prepare('INSERT INTO posts SET message=?, member_id=?,created=NOW(),is_retweet=0,the_retweeted_side=1,originally_id=?,whose_retweet=?');
                $insert->bindParam(1,$message['message']);
                $insert->bindParam(2,$_SESSION['id']);
                $insert->bindParam(3,$message['id']);
                $insert->bindParam(4,$message['member_id']);
                $insert->execute();
            // originally_idは元のI'dの番号を入れる
            }
        }elseif($message['is_retweet'] == 1){
            $update = $db->prepare('UPDATE posts SET is_retweet=0 WHERE id=?');
            $update->bindParam(1,$message['id']);
            $update->execute();
            // 削除する
            $del = $db->prepare('DELETE FROM posts WHERE the_retweeted_side=1 AND originally_id=?');
            $del->execute(array($message['id']));


        }
    }

}

header('Location: index.php'); exit();
?>
