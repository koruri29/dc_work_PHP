<?php
/**
 * 配列・文字列にhtmlspecialcharsをかける関数
 * 
 * @param mixed $before サニタイズしたい値
 * @return mixed $after サニタイズ後の値
 */
function sanitize($before) {
	if (is_array($before)) {
		$after =[];
		foreach($before as $key=>$val)
		{
			$after[$key] = htmlspecialchars($val, ENT_QUOTES|ENT_HTML5, 'UTF-8');
		}
		return $after;
	} else {
		$after = htmlspecialchars($before, ENT_QUOTES|ENT_HTML5, 'UTF-8');
		return $after;
	}
}


/*-------------------------
 * SESSION
 *-------------------------*/
/**
 * $_SESSIONにログイン管理に必要な値をセット
 * 
 * @param array $user fetchUser()で取得したユーザー情報
 * @return void
 */
function setSession(array $user): void {
    global $timeout;
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['timeout'] = $timeout;
    $_SESSION['expires'] = time() + $timeout;
}

/**
 * $_SESSIONにログイン情報が保存されているかチェック
 * 
 * @param object $pdo
 * @return bool
 */
function isLogin(object $pdo): bool {
print 'isloginDONE';

    if (isSessionInEffect()) {
        if ($_SESSION['user_name'] == 'ec_admin' && ! $_SERVER['REQUEST_URI'] == 'edit.php') {
            header('Location: edit.php');
            exit();
        }
        return true;
    } else {
        $_SESSION = array();
        session_destroy();
    }

    $user_name = checkAuthToken($pdo);
    if ($user_name !== false) {//自動ログインが有効で、ユーザーIDが返って来た場合
        $user = fetchUser($pdo, $user_name);
        setSession($user);
        $token = setAuthToken($pdo, $user_name);
        setcookie('token', '', -3600);
        setcookie('token', $token, $_SESSION['expires']);
        createCart($pdo);
        $_SESSION['cart_id'] = lastInsertId($pdo);
        setCartIdToAutologin($pdo);
        return true;
    } else {
        return false;
    }
}


/**
 * 自動ログインがonか判断して、onならクッキーとトークンをセット
 * 
 * @param object $pdo
 * @return int $timeout クッキー（セッション）の有効期限
 */
function setAutologin(object $pdo): void {
    global $timeout;

    if (isSessionInEffect()) return;

    if ($_POST['auto-login'] == 'on') {
        $token = setAuthToken($pdo, $_POST['user-name']);
        print $token;
        setcookie('token', '', time() - 3600);
        setcookie('token', $token, time() + $timeout);
        print 'setcookie1';
        var_dump($_COOKIE['token']);
        return;
    }
    if ($user_name = checkAuthToken($pdo)) {
        print 'checkAuthTokenDONE';
        $token = setAuthToken($pdo, $user_name);
        setcookie('token', '', time() - 3600);
        setcookie('token', $token, time() + $timeout);
        print 'setcookie2';
        return;
    }
    return;
}


/**
 * セッションが有効かどうか判断
 * 
 * @return bool
 */
function isSessionInEffect(): bool {
    if ($_SESSION['user_name']) {
        if ($_SESSION['expires'] > time()) {
            return true;
        }
        return false;
    }
    return false;
}


/**
 * セッション（クッキー)の期限を設定
 * 
 * @return int $timeout セッション（クッキー)の期限
 */
function setTimeout(): int {
        $timeout = 7 * 24 * 60 * 60;
        session_set_cookie_params($timeout);
    return $timeout;
}


/**
 * 自動ログインが有効ならユーザー名を返す。向こうの場合falseを返す。
 * 
 * @param object $pdo
 * @return bool|false ユーザーIDまたはfalseを返す
 */
function checkAuthToken(object $pdo) {
    $stmt = fetchAutoLogin($pdo, $_COOKIE['token']);
    if ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($rec['expires'] < time())  return false;
        return $rec['user_name'];
    } else {
        return false;
    }
}


/*-------------------------
 * SEARCH
 *-------------------------*/
/**
 * 検索機能をまとめた関数
 * 
 * @param object $pdo
 * @return object $stmt 検索結果
 */
function searchProduct(object $pdo): object {
    //文字列を整える
    if (empty($_POST['search'])) {
        $words = array();
    } else {
        $hankaku = mb_convert_kana($_POST['search'], 's', 'utf-8');
        $words = preg_split('/[\s]/', $hankaku);
    }

    if ($_POST['and-or'] == 'and') {
        return andSearch($pdo, $words);
    } else {
        return orSearch($pdo, $words);
    }
}