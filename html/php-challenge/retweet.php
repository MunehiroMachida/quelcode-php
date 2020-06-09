<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];
    // リツイートの数を数えるのと色を変えるテーブルの処理 ==============================================================================================================================
	// 投稿を検査する
	$retweets_count = $db->prepare('SELECT * FROM retweets_count');
	$retweets_count->execute(array($id));
    $retweets_counts = $retweets_count->fetchAll(PDO::FETCH_ASSOC);

    //レコード数を数えてくれる
    $retweets_count_table_sql = 'SELECT COUNT(*) FROM retweets_count';
    $stmt = $db->query($retweets_count_table_sql);
    $retweets_count_table_count = (int)$stmt->fetchColumn();


    $is_retweet['session'] = $_SESSION['id'];
    $is_retweet['get'] = $_GET['id'];

    $is_ture = '';
    if(empty($retweets_counts)){
        $insert = $db->prepare('INSERT INTO retweets_count SET post_id=?, member_id=?');
        $insert->bindParam(1,$_GET['id']);
        $insert->bindParam(2,$_SESSION['id']);
        $insert->execute();
    }elseif(!empty($retweets_counts)){
        for($i=0;$i<$retweets_count_table_count;$i++){
            if($is_retweet['get'] == $retweets_counts[$i]['post_id'] && $is_retweet['session'] == $retweets_counts[$i]['member_id']){
                $is_ture = 'true';
                $retweets_id = $retweets_counts[$i]['id'];
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

    // リツイートされているかの検査 ==============================================================================================================================
    //自分の投稿を取り出す
    $my_post = $db->prepare('SELECT * FROM posts WHERE id=?');
    $my_post->execute(array($id));
    $my_posts = $my_post->fetch();


    // リツイートされているかを検査する
    $posts_retweets = $db->prepare('SELECT * FROM posts');
    $posts_retweets->execute(array($id));
    $posts_retweet = $posts_retweets->fetchAll(PDO::FETCH_ASSOC);


    //レコード数を数えてくれる
    $posts_table_count_sql = 'SELECT COUNT(*) FROM posts';
    $stmt = $db->query($posts_table_count_sql);
    $posts_table_count = (int)$stmt->fetchColumn();

    $a = '';
    for($i=0;$i<$posts_table_count;$i++){
        if(!empty($posts_retweet[$i]['motomoto_id'] == $my_posts['id'] && $posts_retweet[$i]['member_id'] == $_SESSION['id'])){
            $a = 'aru';
            break;
        }else{
            $a = 'nai';
        }
    }

    // var_dump($my_posts['member_id']);
    // var_dump($_SESSION['id']);

    if($a == 'nai'){
        $insert = $db->prepare('INSERT INTO posts SET message=?, member_id=?, created=NOW(), motomoto_id=?');
        $insert->bindParam(1,$my_posts['message']);
        $insert->bindParam(2,$_SESSION['id']);
        $insert->bindParam(3,$id);
        $insert->execute();
    }elseif($a == 'aru'){
        // 削除する
        $del = $db->prepare('DELETE FROM posts WHERE member_id=? AND motomoto_id=?');
        $del->bindParam(1,$_SESSION['id']);
        $del->bindParam(2,$_GET['id']);
        $del->execute();
    }
// =========================================================================================================================
}

header('Location: index.php'); exit();
?>
