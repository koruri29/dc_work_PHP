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
    $timeout = setSessionTimeout();
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['timeout'] = $timeout;
    $_SESSION['expires'] = time() + $timeout;
}

/**
 * $_SESSIONにログイン情報が保存されているかチェック
 * 
 * @return bool
 */
function isLogin(object $pdo): bool {
    if ($_SESSION['user_name']) {
        if ($_SESSION['expires'] <= time()) {
            return true;
        }
    }

    $user_name = checkAuthToken($pdo);
    if ($user_name !== false) {//自動ログインが有効で、ユーザーIDが返って来た場合
        $user = fetchUser($pdo);
        setSession($user);
        $token = setAuthToken($pdo);
        setcookie('token', $token, $_SESSION['expires']);
        return true;
    } else {
        return false;
    }
}


function setSessionTimeout(): int {
    $timeout = 30 * 60;
    if ($_POST['auto-login'] == 'on') {
        $timeout = 7 * 24 * 60 * 60;
        session_set_cookie_params($timeout);
    }
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
        if ($rec['expires'] < time()) return false;
        return $rec['user_name'];
    } else {
        return false;
    }
}




/*-------------------------
 * SEARCH
 *-------------------------*/
function searchProduct(object $pdo): object {
    //文字列を整える
    $hankaku = mb_convert_kana($_POST['search'], 's', 'utf-8');
    $words = preg_split('/[\s]/', $hankaku);

    if ($_POST['and-or'] == 'and') {
        return andSearch($pdo, $words);
    } else {
        return orSearch($pdo, $words);
    }
}