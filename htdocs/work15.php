<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK15</title>
	</head>
	<body>
		<?php
			$class01 = [
				'tokugawa' => rand(1, 100),
				'oda' => rand(1, 100),
				'toyotomi' => rand(1, 100),
				'takeda' => rand(1, 100),
			];
			$class02 = [
				'minamoto' => rand(1, 100),
				'taira' => rand(1, 100),
				'sugawara' => rand(1, 100),
				'fujiwara' => rand(1, 100),
			];
			$school = array($class01, $class02);


			if ($school[0]['oda'] > $school[1]['sugawara']) {
				print '<p>odaさんの方が得点が高いです。</p><br>';
			} else if ($school[0]['oda'] < $school[1]['sugawara']) {
				print '<p>sugawaraさんの方が得点が高いです。</p><br>';
			} else {
				print '<p>odaさんとsugawaraさんの得点は同じです。</p><br>';
			}

			$class01_total = 0;
			foreach ($school[0] as $key => $val) {
				$class01_total += $val;
			}
			$class02_total = 0;
			foreach ($school[1] as $key => $val) {
				$class02_total += $val;
			}
			$class01_average = $class01_total / count($school[0]);
			$class02_average = $class02_total / count($school[1]);

			print '<p>$class01の平均点は' . $class01_average . 'です。</p>';
			print '<p>$class02の平均点は' . $class02_average . 'です。</p>';
		?>
	</body>
</html>