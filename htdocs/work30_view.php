<?php
$host = 'mysql34.conoha.ne.jp';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
$database = 'bcdhm_omiya_pf0006';

$db = new mysqli($host, $login_user, $password, $database);
if ($db->connect_error) {
	print $db->connect_error;
	exit();
} else {
	$db->set_charset('utf8');
}

$select = 'SELECT * FROM photo_submission WHERE public_flag = 1';
$stmt = $db->query($select);
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<style>
			img {width: 150px;}
			.img { display: inline-block; text-align: center;}
		</style>
		<title>画像一覧</title>
	</head>
	<body>
		<a href="work30_post.php">投稿ページへ</a>
		<div class="images">
			<?php while ($row = $stmt->fetch_assoc()):?>
				<div class="img">
					<p><?php print substr($row['image_name'], 14); ?></p>
					<img src="./images/<?php print $row['image_name']; ?>">
				</div>
			<?php endwhile; ?>
			<?php $stmt->close(); ?>
			<?php $db->close(); ?>
		</div>
	</body>
</html>