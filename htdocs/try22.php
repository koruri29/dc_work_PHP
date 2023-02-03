<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY22</title>
	</head>
	<body>
		<form method="post">
			<?php
				$fp = fopen('file_read.txt', 'r');
				while ($line = fgets($fp)) {
					echo $line . '<br>';
				}
				fclose($fp);
			?>
		</form>
	</body>
</html>