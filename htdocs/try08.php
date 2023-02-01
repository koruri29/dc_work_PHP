<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY08</title>
	</head>
	<body>
		<?php
			$fruit01 = 'りんご';
			$fruit02 = 'バナナ';

			if($fruit01 == 'りんご' && $fruit02 == 'バナナ') {
				echo '<p>fruit01はリンゴで、かつ、fruit02はバナナです！</p>';
			}
			if ($fruit01 == 'りんご' || $fruit02 == 'りんご') {
				echo '<p>fruit01がリンゴ、あるいは、fruit02がりんごのどちらかです！</p>';
			}
			if (!($fruit01 == 'バナナ')) {
				echo '<p>fruit01はバナナではありません。</p>';
			}
		?>
	</body>
</html>