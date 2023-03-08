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
			$after[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
		}
		return $after;
	} else {
		$after = htmlspecialchars($before, ENT_QUOTES, 'UTF-8');
		return $after;
	}
}


/*-------------------------
 * SESSION
 *-------------------------*/
/**
 * $_SESSIONに値をセット
 * 
 * @param array $user fetchUser()で取得したユーザー情報
 * @return void
 */
function setSession(array $user): void {
    $_SESSION['id'] = $user['user_name'];
    $_SESSION['time'] = time();
}

/**
 * $_SESSIONにログイン情報が保存されているかチェック
 * 
 * @param array $session
 * @return bool ログインしていればtrue
 */
function isLogin(array $session): bool {
    if (empty($session['id'])) {
        return false;
    }
    if ($session['time'] + 3600 <= time()) {
        $session = array();
        session_destroy();
        return false;
    }
    $session['time'] = time();
    return true;
}
//if (isLogin($_SESSION) == false) header('Location: login.php');