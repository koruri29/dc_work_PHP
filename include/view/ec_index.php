	<body>
        <a href="register.php">ユーザー登録はこちら</a>
        <h2>ログイン</h2>
        <?php if (! empty($error['login'])) print '<p class="error">' . $error['login'] . '</p>'; ?>
        <form action="./index.php" method="post">
            <dl>
                <dt>ユーザー名</dt>
                <dd><input type="text" name="user-name"></dd>
                <dt>パスワード</dt>
                <dd><input type="password" name="password"></dd>
                <input type="checkbox" name="auto-login">自動ログインをオンにする<br>
                <input type="submit" value="ログイン">
            </dl>
        </form>