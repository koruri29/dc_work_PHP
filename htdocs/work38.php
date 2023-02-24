<?php
session_start();

define('EXPIRATION_PERIOD', 30);
$cookie_expiration = time() + EXPIRATION_PERIOD * 60 * 24;


if ($_SESSION['err_flg']) {
	print '<p>ログインが失敗しました：正しいログインID（半角英数字）を入力してください。</p>';
}

$_SESSION['err_flg'] = false;


if (isset($_POST['logout'])) {
	$session = session_name();
	$_SESSION = [];

	if (isset($_COOKIE[$session])) {
		setcookie($session, '', time() - 30, '/');
		$message = '<p>ログアウトされました。</p>';
	}
} else {
	if (isset($_SESSION['user_id'])) {
		header('Location: work38_home.php');
		exit();
	}
}

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
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK38</title>
	</head>
	<body>
		<form action="work38_home.php" method="post">
			ユーザーID<input type="number" name="user_id" value="<?php print $user_id; ?>"><br>
			パスワード<input type="password" name="password"><br>
			<input type="checkbox" name="cookie_confirmation" value="checked" <?php print $cookie_confirmation; ?>>次回からログインIDの入力を省略する	<br>
			<input type="submit" value="ログイン">
		</form>
	</body>
</html>