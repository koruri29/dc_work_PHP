<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY24</title>
	</head>
	<body>
		<?php
			if (! isset($_FILES['upload_image'])) {
				echo 'ファイルが送信されていません。';
				exit;
			}

			$save = 'img/' . basename($_FILES['upload_image']['name']);

			if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $save)) {
				echo 'アップロード成功しました。';
			} else {
				echo 'アップロード失敗しました。';
			}
		?>
	</body>
</html>