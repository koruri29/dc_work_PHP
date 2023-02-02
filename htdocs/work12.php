<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK12</title>
	</head>
	<body>
		<?php
				$i = 1;
			while ($i <= 100) {
				if ($i % 3 === 0 && $i % 5 === 0) {
					print 'FizzBuzz<br>';
					$i++;
					continue;
				} else if ($i % 3 === 0 && $i % 5 !== 0) {
					print 'Fizz<br>';
					$i++;
					continue;
				} else if ($i % 3 !== 0 && $i % 5 === 0) {
					print 'Buzz<br>';
					$i++;
					continue;
				}
				print $i;
				print '<br>';
				$i++;
			}


			$i = 1;
			while ($i < 10) {
				$j = 1;
				while($j < 10) {
					print $i . '*' . $j . '=' . $i * $j;
					$j++;
				}
				print '<br>';
				$i++;
			}


			$i = 1;
			while ($i < 30) {
				print str_repeat('*', $i);
				print '<br>';
				print '!';
				print '<br>';
				$i++;
			}
		?>
	</body>
</html>