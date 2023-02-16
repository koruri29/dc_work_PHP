<?php
	define('EXPIRATION_PERIOD', 30);
	$cookie_expiration = time() + EXPIRATION_PERIOD * 60 * 24 * 365;

	if (isset($_POST['cookie_confirmation']) === true) {
		$cookie_confirmation = $_POST['cookie_confirmation'];
	} else {
		$cookie_confirmation = '';
	}

	if (isset($_POST['login_id']) === true) {
		$login_id = $_POST['login_id'];		
	} else {
		$login_id = '';
	}

	if ($cookie_confirmation === 'checked') {
		setcookie('cookie_confirmation', $cookie_confirmation, $cookie_expiration);
		setcookie('login_id', $login_id, $cookie_expiration);
	} else {
		setcookie('cookie_confirmation', '', time() - 30);
		setcookie('login_id', '', time() - 30);
	}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY53</title>
	</head>
	<body>
		<p>ログイン（疑似的）が完了しました。</p>
	</body>
</html>