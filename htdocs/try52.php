<?php setcookie('username', 'login_user', time() + 60 * 60* 24); ?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY52</title>
	</head>
	<body>
		<?php
			if (isset($_COOKIE['username'])) {
				print $_COOKIE['username'];
			} else {
				print 'Cookieのデータがありません。';
			}
		?>
	</body>
</html>