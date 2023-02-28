<?php
define('DSN', 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;');
define('LOGIN_USER', 'bcdhm_omiya_pf0006');
define('PASSWORD', 'N3p!CxYc');


$db = new PDO(DSN, LOGIN_USER, PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//画像一覧を表示
function render_img_view($db) {
	$select = 'SELECT * FROM photo_submission WHERE public_flag = 1';
	$stmt = $db->query($select);

	while($row = $stmt->fetch_assoc()){
		print '<div class="img">';
		print '<p>' . substr($row['image_name'], 14) . '</p>';
		print '<img src="./images/' . $row['image_name'] . '">';
		print '</div>';
	}
$stmt->close();
}
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
			<?php
				render_img_view($db);
			?>
		</div>
	</body>
</html>