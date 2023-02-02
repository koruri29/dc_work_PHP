<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK05</title>
	</head>
	<body>
		<?php
		//work05－1
			$num = rand(1, 100);

			if ($num % 3 === 0 && $num % 6 === 0):
				print '3と6の倍数です。';
			elseif ($num % 3 === 0 && $num % 6 !== 0):
				print '3の倍数で、6の倍数ではありません';
			else:
				print '3の倍数で、6の倍数ではありません';
			endif;


			print '<br>';


			//work05－2
			$random01 = rand(1, 10);
			$random02 = rand(1, 10);

			print 'random01 = ' . $random01 . ',random02 = ' . $random02 . 'です。';

			if ($random01 == $random02):
				print '2つは同じ数です。';
			elseif ($random01 > $random02):
				print 'random01の方が大きいです。';
			else:
				print 'random02の方が大きいです。';
			endif;

			if ($random01 % 3 ===0 && $random02 % 3 === 0):
				print '2つの数字の中には3の倍数が2つ含まれています。';
			elseif ($random01 % 3 !== 0 && $random02 % 3 !== 0):
				print '2つの数字の中に3の倍数が含まれていません。';
			else:
				print '2つの数字の中には3の倍数が1つ含まれています。';
			endif;
			?>
	</body>
</html>