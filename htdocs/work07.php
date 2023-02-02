<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK05</title>
	</head>
	<body>
		<?php
			$num = rand(1, 100);
		?>
			<?php if ($num % 3 === 0 && $num % 6 === 0): ?>
				<p>3と6の倍数です。</p>
			<?php elseif ($num % 3 === 0 && $num % 6 !== 0): ?>
				<p>3の倍数で、6の倍数ではありません</p>
			<?php else: ?>
				<p>3の倍数で、6の倍数ではありません</p>
			<?php endif; ?>


			<br>

			<?php
				$random01 = rand(1, 10);
				$random02 = rand(1, 10);
			?>

			<p>random01 = <?php print $random01; ?>, random02 = <?php print $random02; ?>です。</p>

			<?php if ($random01 == $random02): ?>
				<p>2つは同じ数です。</p>
			<?php elseif ($random01 > $random02): ?>
				<p>random01の方が大きいです。</p>
			<?php else: ?>
				<p>random02の方が大きいです。</p>
			<?php endif; ?>

			<?php if ($random01 % 3 ===0 && $random02 % 3 === 0): ?>
				<p>2つの数字の中には3の倍数が2つ含まれています。</p>
			<?php elseif ($random01 % 3 !== 0 && $random02 % 3 !== 0): ?>
				<p>2つの数字の中に3の倍数が含まれていません。</p>
			<?php else: ?>
				<p>2つの数字の中には3の倍数が1つ含まれています。</p>
			<?php endif; ?>
	</body>
</html>