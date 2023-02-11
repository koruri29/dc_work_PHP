<?php
if (! empty($_POST)) {
	$post = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');

	$error = array();
	if (empty($post['title'])) {
		$error['title'] = '画像名が入力されていません。';
	}
	if (empty($_FILES)) {
		$error['img'] = '画像が選択されていません。';
	} else {
		if (mime_content_type($_FILES['img']['tmp_name']) !== 'image/jpeg' || mime_content_type($_FILES['img']['tmp_name']) !== 'image/png') {
			$error['filetype'] = 'jpgまたはpngファイル以外が選択されています。';
		} else {
			$tmp_file_data = bin2hex(file_get_contents($_FILES['img']['tmp_name']));
		}
	}
}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>投稿ページ</title>
	</head>
	<body>
		<form method="post" enctype="multipart/form-data">
			画像名：<input type="text" name="title">
			画像ファイル：<input type="file" name="img">
			<input type="submit" value="画像投稿">
		</form>
		<a href="work30_view.php">画像一覧ページへ</a>
	</body>
</html>