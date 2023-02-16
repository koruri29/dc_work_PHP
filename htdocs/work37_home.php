<?php
define('DSN', 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;');
define('LOGIN_USER', 'bcdhm_omiya_pf0006');
define('PASSWORD', 'N3p!CxYc');

define('EXPIRATION_PERIOD', 30);
$cookie_expiration = time() + EXPIRATION_PERIOD * 60 ;

$db = new PDO(DSN, LOGIN_USER, PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_POST['user_id'])) {
	$user_id = htmlspecialchars($_POST['user_id'], ENT_QUOTES, 'utf-8');
} else {
	$user_id = '';
}

if (isset($_POST['password'])) {
	$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'utf-8');
} else {
	$password = '';
}

if (isset($_POST['cookie_confirmation'])) {
	$cookie_confirmation = htmlspecialchars($_POST['cookie_confirmation'], ENT_QUOTES, 'utf-8');
} else {
	$cookie_confirmation = '';
}


if ($cookie_confirmation === 'checked') {
	setcookie('cookie_confirmation', $cookie_confirmation, $cookie_expiration);
	setcookie('user_id', $user_id, $cookie_expiration);
} else {
	setcookie('cookie_confirmation', '', time() - 30);
	setcookie('user_id', '', time() - 30);
}


$sql = 'SELECT user_name, password FROM user_table WHERE user_id = :id';
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $user_id);
$stmt->execute();

$rec = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK37</title>
	</head>
	<body>
		<?php if ($rec['password'] === $password):?>
			<p>ログイン（擬似的）が完了しました。</p>
			<p><?php print $rec['user_name']; ?>さん、ようこそ！</p>
		<?php else: ?>
			<p>ログインに失敗しました。</p>
		<?php endif; ?>
	</body>
</html>