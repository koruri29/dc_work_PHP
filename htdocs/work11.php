<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK10</title>
	</head>
	<body>
		<?php
			for ($i = 1; $i <= 100; $i++):
				if ($i % 3 === 0 && $i % 5 === 0) {
					print 'FizzBuzz<br>';
					continue;
				} else if ($i % 3 === 0 && $i % 5 !== 0) {
					print 'Fizz<br>';
					continue;
				} else if ($i % 3 !== 0 && $i % 5 === 0) {
					print 'Buzz<br>';
					continue;
				}
				print $i;
				print '<br>';
			endfor;


			for ($i = 1; $i < 10; $i++):
				for ($j = 1; $j < 10; $j++):
					print $i . '*' . $j . '=' . $i * $j;
				endfor;
				print '<br>';
			endfor;


			for ($i = 1; $i < 30; $i++):
				print str_repeat('*', $i);
				print '<br>';
				print '!';
				print '<br>';
			endfor;
		?>
	</body>
</html>