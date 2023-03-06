	<body>
        <a href="register.php">ユーザー登録はこちら</a>
        <h2>ログイン</h2>
        <?php if (! empty($error['login'])) print '<p>' . $error['login'] . '</p>'; ?>
        <form action="./login.php" method="post">
            <dl>
                <dt>ユーザー名</dt>
                <dd><input type="text" name="user-name"></dd>
                <dt>パスワード</dt>
                <dd><input type="password" name="password"></dd>
                <input type="submit" value="ログイン">
            </dl>
        </form>