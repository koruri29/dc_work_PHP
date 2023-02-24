<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK14</title>
	</head>
	<body>
		<?php
			$arr = array();
			for ($i = 0; $i < 5; $i++) {
				$random = rand(1, 100);
				array_push($arr, $random);
			}
			foreach ($arr as $key => $val) {
				if ($val % 2 === 1) {
					print $val . '(奇数)';
				} else {
					print $val . '(偶数)';
				}
			}
	?>
	</body>
</html>