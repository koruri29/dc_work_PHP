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