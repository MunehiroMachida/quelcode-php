<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
	// ログインしている
	$_SESSION['time'] = time();

	$members = $db->prepare('SELECT * FROM members WHERE id=?');
	$members->execute(array($_SESSION['id']));
	$member = $members->fetch();
} else {
	// ログインしていない
	header('Location: login.php');
	exit();
}

// 投稿を記録する
if (!empty($_POST)) {
	if ($_POST['message'] != '') {
		$message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
		$message->execute(array(
			$member['id'],
			$_POST['message'],
			$_POST['reply_post_id']
		));
		header('Location: index.php');
		exit();
	}
}

// 投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
	$page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_REQUEST['res'])) {
	$response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
	$response->execute(array($_REQUEST['res']));

	$table = $response->fetch();
	$message = '@' . $table['name'] . ' ' . $table['message'];
}

// htmlspecialcharsのショートカット
function h($value)
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 本文内のURLにリンクを設定します
function makeLink($value)
{
	return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>
	<link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>ひとこと掲示板</h1>
		</div>
		<div id="content">
			<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
			<form action="" method="post">
				<dl>
					<dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
					<dd>
						<textarea name="message" cols="50" rows="5"><?php echo h($message); ?></textarea>
						<input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>" />
					</dd>
				</dl>
				<div>
					<p>
						<input type="submit" value="投稿する" />
					</p>
				</div>
			</form>

			<?php foreach ($posts as $post) : ?>
				<div class="msg">
					<!-- 誰がリツイートしたかリツイート、表示 start ====================================-->
					<?php if ($post['originally_id'] > 0 && $post['member_id'] == $_SESSION['id']) : ?>
						<span style='font-size: 12px; color: #c0c0c0;'>リツイート済み</span>
					<?php elseif ($post['originally_id'] > 0 && $post['member_id'] != $_SESSION['id']) : ?>
						<span style='font-size: 12px; color: #c0c0c0;'><?php echo h($post['name']); ?>さんがリツイート</span>
					<?php endif; ?>
					<!-- 誰がリツイートしたかリツイート、表示　end ====================================-->

					<img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" style="border-radius:50%;" alt="<?php echo h($post['name']); ?>" />
					<p><?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
					<p class="day">
						<a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>

						<!-- リツイート start  -->
						<!-- リツイートの色判定 start ====================================-->
						<?php
						// retweets_countテーブルの中からmessageが一緒のやつをとる
						$retweets_count_table = $db->prepare('SELECT * FROM retweets_count WHERE post_message=?');
						$retweets_count_table->bindParam(1, $post['message']);
						$retweets_count_table->execute();
						$retweets_count_tables = $retweets_count_table->fetchAll(PDO::FETCH_ASSOC);

						//retweets_countテーブルのレコード数を数えてくれる
						$retweet_count_table_sql = 'SELECT COUNT(*) FROM retweets_count';
						$stmt = $db->query($retweet_count_table_sql);
						$retweet_count_table_amount = (int) $stmt->fetchColumn();

						for ($i = 0; $i < $retweet_count_table_amount; $i++) {
							$is_retweet = '';
							if ($retweets_count_tables[$i]['post_message'] == $post['message'] && !empty($retweets_count_tables[$i]['member_id'] == $_SESSION['id'])) {
								$is_retweet = true;
								break;
							}
						}
						?>
						<?php if ($is_retweet) : ?>
							<a href="retweet.php?id=<?php echo h($post['id']); ?>" style="color: #66cdaa;"><i class="fas fa-retweet"></i></a>
						<?php else : ?>
							<a href="retweet.php?id=<?php echo h($post['id']); ?>" style=""><i class="fas fa-retweet"></i></a>
						<?php endif; ?>
						<!-- リツイートの色判定 end ====================================-->
						<!-- リツイートの数を表示 start ====================================-->
						<span>
							<?php
							$retweets_amount = $db->prepare('SELECT COUNT(post_id) FROM retweets_count WHERE post_message=?');
							$retweets_amount->bindParam(1, $post['message']);
							$retweets_amount->execute();
							$retweets_amounts = $retweets_amount->fetchAll(PDO::FETCH_ASSOC);
							echo ($retweets_amounts[0]['COUNT(post_id)']);
							?>
						</span>
						<!-- リツイートの数を表示　end ====================================-->
						<!-- リツイート　end  -->

						<!-- いいね start  -->
						<!-- いいね値が入っていたら色変える start ==================================== -->
						<?php
						$is_good = $db->prepare('SELECT * FROM goods WHERE post_message=?');
						$is_good->bindParam(1, $post['message']);
						$is_good->execute();
						$is_goods = $is_good->fetchAll(PDO::FETCH_ASSOC);

						$count_sql = 'SELECT member_id FROM goods';
						$stmt = $db->query($count_sql);
						$count = (int) $stmt->fetchColumn();

						$judgment = '';
						for ($i = 0; $i < $count; $i++) {
							if (!empty($is_goods[$i]['member_id'] == $_SESSION['id'])) {
								$judgment = 'like';
								break;
							}
						}
						?>
						<?php if ($judgment == 'like') : ?>
							<a href="good.php?id=<?php echo ($post['id']); ?>" style="color: #ff69b4;"><i class="far fa-thumbs-up"></i></i></a>
						<?php else : ?>
							<a href="good.php?id=<?php echo ($post['id']); ?>"><i class="far fa-thumbs-up"></i></a>
						<?php endif; ?>
						<!-- いいね値が入っていたら色変える end ==================================== -->

						<!-- いいねの数を表示 start-->
						<span>
							<?php
							$goods_count = $db->prepare('SELECT COUNT(post_id) FROM goods WHERE post_message=?');
							$goods_count->bindParam(1, $post['message']);
							$goods_count->execute();
							$goods_id_array = $goods_count->fetchAll(PDO::FETCH_ASSOC);
							echo ($goods_id_array[0]['COUNT(post_id)']);
							?>
						</span>
						<!-- いいねの数を表示　end -->
						<!-- いいね　end  -->
						<?php if ($post['reply_post_id'] > 0) : ?>
							<a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
						<?php endif; ?>
						<?php if ($_SESSION['id'] == $post['member_id']) : ?>
							[<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #F33;">削除</a>]
						<?php endif; ?>
					</p>
				</div>
			<?php endforeach; ?>

			<ul class="paging">
				<?php
				if ($page > 1) {
				?>
					<li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
				<?php
				} else {
				?>
					<li>前のページへ</li>
				<?php
				}
				?>
				<?php
				if ($page < $maxPage) {
				?>
					<li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
				<?php
				} else {
				?>
					<li>次のページへ</li>
				<?php
				}
				?>
			</ul>
		</div>
	</div>
</body>

</html>