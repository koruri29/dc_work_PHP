<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<style>
			img {width: 150px;}
			.img { display: inline-block; text-align: center;}
			.non-display {background-color: #ccc;}
		</style>
		<title>投稿ページ</title>
	</head>
	<body>
		<?php if (! empty($error['sql'])) print $error['sql']; ?>
		<?php if (! empty($msg)) print $msg; ?>
		<form method="post" enctype="multipart/form-data">
			<?php if (! empty($error['title'])) print $error['title']; ?>
			<?php if (! empty($error['character'])) print $error['character']; ?>
			画像名：<input type="text" name="title">
			<br>
			<?php if (! empty($error['img'])) print $error['img']; ?>
			<?php if (! empty($error['ext'])) print $error['ext']; ?>
			画像ファイル：<input type="file" name="img">
			<br>
			<input type="submit" value="画像投稿">
		</form>
		<a href="work30_view.php">画像一覧ページへ</a>
		<div class="images">
			<?php
				render_img_post($stmt);
			?>
		</div>
		<script>
			const displayBtn = document.getElementsByClassName('display');
			const img = document.getElementsByClassName('img');
			for (let i = 0; i < img.length; i++) {
				if (displayBtn[i].value == '表示する') {
					img[i].classList.add('non-display');
				} else {
					img[i].classList.remove('non-display');
				}
			}
		</script>
	</body>
</html>