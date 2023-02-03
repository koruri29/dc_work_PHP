<?php
	$animals = array();
	if (isset($_POST['animals'])) {
		foreach ($_POST['animals'] as $key => $val) {
			$animals[] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK17</title>
	</head>
	<body>
		<p>好きな動物は？</p>
		<form method="post">
			<input type="checkbox" value="キリン" name="animals[]">キリン
			<input type="checkbox" value="ライオン" name="animals[]">ライオン
			<input type="checkbox" value="ぞう" name="animals[]">ぞう
			<input type="submit" value="送信">
		</form>
		<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
			<div>好きな動物は<?php foreach($animals as $key => $val) {print $val;}; ?>です。</div>
		<?php endif; ?>
	</body>
</html>