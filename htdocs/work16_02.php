<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK16</title>
	</head>
	<body>
		<?php
			foreach($_GET['option'] as $key => $val) {
				$animals[] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
			}
		?>
		<p>あなたの好きな動物は
		<?php
			foreach ($animals as $key => $val) {
				print $val;
			}
		?>
		です。</p>
	</body>
</html>