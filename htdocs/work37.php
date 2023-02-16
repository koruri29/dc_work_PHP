<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK37</title>
	</head>
	<body>
		<?php
			if (isset($_COOKIE['cookie_confirmation'])) {
				$cookie_confirmation = 'checked';
			} else {
				$cookie_confirmation = '';
			}

			if (isset($_COOKIE['user_id'])) {
				$user_id = $_COOKIE['user_id'];
			} else {
				$user_id = '';
			}
		?>
		<form action="work37_home.php" method="post">
			ユーザーID<input type="number" name="user_id" value="<?php print $user_id; ?>"><br>
			パスワード<input type="password" name="password" <?php print $cookie_confirmation; ?>><br>
			<input type="checkbox" name="cookie_confirmation" value="checked" <?php print $cookie_confirmation; ?>>次回からログインIDの入力を省略する	<br>
			<input type="submit" value="ログイン">
		</form>
	</body>
</html>