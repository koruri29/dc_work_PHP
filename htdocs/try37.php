<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY37</title>
	</head>
	<body>
		<?php
			$db = new mysqli('mysql34.conoha.ne.jp', 'bcdhm_omiya_pf0006', 'N3p!CxYc', 'bcdhm_omiya_pf0006');
			if($db->connect_error) {
				echo $db->connect_error;
				exit();
			} else {
				print 'データベースへの接続に成功しました。';
			}
			$db->close();
		?>
	</body>
</html>