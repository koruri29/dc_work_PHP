<body>
	<a href="./index.php">ログインはこちら</a>
	<h2>ユーザー登録</h2>
	<?php if (! empty($msg)) print '<p class="msg">' . $msg . '</p>'; ?>
	<?php if (! empty($error)) {
		foreach ($error as $error_msg) {
			print '<p class="error">' . $error_msg . '</p>';
		}
	} ?>
	<form action="./register.php" method="post">
		<dl>
			<dt>ユーザー名</dt>
			<dd><input type="text" name="user-name" value="<?php if (! empty($post['user-name'] || empty($msg['registered']))) print $post['user-name']; ?>"></dd>
			<dt>パスワード</dt>
			<dd><input type="password" name="password"></dd>
			<input type="submit" value="登録">
		</dl>
	</form>