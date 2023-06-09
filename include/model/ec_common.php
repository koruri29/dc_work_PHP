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
    $_SESSION['cart_id'] = '';
}

/**
 * $_SESSIONにログイン情報が保存されているかチェック
 * 
 * @param object $pdo
 * @return bool
 */
function isLogin(object $pdo): bool {
    if (isSessionInEffect()) {
        return true;
    }

    $_SESSION = array();
    session_destroy();

    if ($user_name = checkAuthToken($pdo)) {//自動ログインが有効で、ユーザーIDが返って来た場合
        $user = fetchUser($pdo, $user_name);
        setSession($user);

        $token = setAuthToken($pdo, $user_name);
        setcookie('token', '', time() - 3600);
        setcookie('token', $token, $_SESSION['expires']);

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

    if ($user_name = checkAuthToken($pdo)) {
        $user = fetchUser($pdo, $user_name);
        setSession($user);

        $stmt = fetchAutoLogin($pdo);
        $autologin_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['cart_id'] = $autologin_info['cart_id'];

        $token = $_COOKIE['token'];
        setcookie('token', '', time() - 3600);
        setcookie('token', $token, time() + $timeout);

        return;
    }
    return;
}


/**
 * 
 */
function isEqualCartAndCookie(object $pdo) {
    $stmt  = fetchAutoLogin($pdo);
    $autologin_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($_SESSION['cart_id'] !== $autologin_info['cart_id']) {
        return false;
    }
    return true;
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
 * 検索窓に入力された文字列を整形する
 * 
 * @return array 検索ワードをスペースで区切って配列化
 */
function adjustSearchWords(): array {
    if (empty($_POST['search'])) {
        $words = array();
    } else {
        $hankaku = mb_convert_kana($_POST['search'], 's', 'utf-8');
        $words = preg_split('/[\s]/', $hankaku);
    }
    return $words;
}