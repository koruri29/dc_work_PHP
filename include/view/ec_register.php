<body>
	<a href="./index.php">ログインはこちら</a>
	<h2 class="h2">ユーザー登録</h2>
	<div id="show-msg">
		<?php if (! empty($msg)) print '<p class="msg">' . $msg . '</p>'; ?>
		<?php if (! empty($error)) {
			foreach ($error as $error_msg) {
				print '<p class="error">' . $error_msg . '</p>';
			}
		} ?>
	</div>
	<form name="register" action="./register.php" method="post">
		<dl>
			<dt>ユーザー名</dt>
			<dd>
				<input id="user-name" type="text" name="user-name"
					value="<?php if (! empty($post['user-name'] || empty($msg['registered']))) print $post['user-name']; ?>"
			 		placeholder="英数字5文字以上">
			</dd>
			<dt>パスワード</dt>
			<dd><input id="password" type="password" name="password" placeholder="英数字8文字以上"></dd>
			<input id="register-user" type="submit" value="登録">
		</dl>
	</form>
	<script src="../../0006/js/register.js"></script>
