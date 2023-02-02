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

			switch(true) {
				case $num % 3 ===0 && $num % 6 === 0:
					print '3と6の倍数です。';
					break;
				case $num % 3 === 0 && $num % 6 !== 0:
					print '3の倍数で、6の倍数ではありません';
					break;
				default:
					print '倍数ではありません。';
				}


			print '<br>';


			//work05－2
			$random01 = rand(1, 10);
			$random02 = rand(1, 10);

			print 'random01 = ' . $random01 . ',random02 = ' . $random02 . 'です。';

			switch(true):
				case $random01 == $random02:
					print 'random01の方が大きいです。';
					break;
				case $random01 > $random02:
					print 'random01の方が大きいです。';
					break;
				default:
					print 'random02の方が大きいです。';
				endswitch;

			switch (true):
				case $random01 % 3 ===0 && $random02 % 3 === 0:
					print '2つの数字の中には3の倍数が2つ含まれています。';
					break;
				case $random01 % 3 !== 0 && $random02 % 3 !== 0:
					print '2つの数字の中に3の倍数が含まれていません。';
					break;
				default:
					print '2つの数字の中には3の倍数が1つ含まれています。';
				endswitch;
			?>
	</body>
</html>