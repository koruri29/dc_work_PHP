<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK35</title>
	</head>
	<body>
		<?php
			function rand_calc() {
				$num = mt_rand(1, 10);
				if ($num % 2 === 0) {
					return $num * 10;
				} else {
					return $num * 100;
				}
			}

			$random = rand_calc();
			print $random;
		?>
	</body>
</html>