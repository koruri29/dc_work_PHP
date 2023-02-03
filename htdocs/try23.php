<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY23</title>
	</head>
	<body>
			<?php
				$fp = fopen('file_write.txt', 'w');

				fwrite($fp, 'ファイルへ書き込む');

				fclose($fp);
			?>
	</body>
</html>