<?php
session_start();

define('EXPIRATION_PERIOD', 30);
$cookie_expiration= time() + EXPIRATION_PERIOD * 60 * 24 * 365;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['cookie_confirmation'])) {
		$cookie_confirmation = $_POST['cookie_confirmation'];
	} else {
		$cookie_confirmation = '';
	}

	if (isset($_POST['login_id']) && preg_match('/^[a-zA-Z0-9]+$/', $_POST['login_id'])) {
		$login_id = $_POST['login_id'];
		$_SESSION['login_id'] = $login_id;
	} else {
		$login_id = '';
		$_SESSION['err_flg'] = true;
	}
}


if ($cookie_confirmation === 'checked') {
	setcookie('cookie_confirmation', $cookie_confirmation, $cookie_expiration);
	setcookie('login_id', $login_id, $cookie_expiration);
} else {
	setcookie('cookie_confirmation', '', time() -30);
	setcookie('login_id', '', time() - 30);
}


if (! isset($_SESSION['login_id'])) {
	header('Location: try55.php');
	exit();
} else {
	print '<p>' . $_SESSION['login_id'] . 'さん：ログイン中です。</p>'
}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>try55</title>
	</head>
	<body>
		<form action="try55.php" method="post">
			<input type="hidden" name="logout" value="logout">
			<input type="submit" value="ログアウト">
		</form>
	</body>
</html>