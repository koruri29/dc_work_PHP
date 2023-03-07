<body>
	<a href="login.php">ログインはこちら</a>
	<h2>ユーザー登録</h2>
	<?php if (! empty($msg['registered'])) print '<p>' . $msg['registered'] . '</p>'; ?>
	<?php if (! empty($error['register'])) print '<p>' . $error['register'] . '</p>'; ?>
	<?php if (! empty($error['user_name'])) print $error['user_name']; ?>
	<?php if (! empty($error['existing_user_name'])) print $error['existing_user_name']; ?>
	<?php if (! empty($error['password'])) print $error['password']; ?>
	<form action="./register.php" method="post">
		<dl>
			<dt>ユーザー名</dt>
			<dd><input type="text" name="user-name" value="<?php if (! empty($post['user-name'] || empty($msg['registered']))) print $post['user-name']; ?>"></dd>
			<dt>パスワード</dt>
			<dd><input type="password" name="password"></dd>
			<input type="submit" value="登録">
		</dl>
	</form>