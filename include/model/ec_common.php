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
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['time'] = time();
}

/**
 * $_SESSIONにログイン情報が保存されているかチェック
 * 
 * @return bool ログインしていればtrue
 */
function isLogin(): bool {
    if (empty($_SESSION['user_name'])) {
        return false;
    }
    if ($_SESSION['time'] + 3600 <= time()) {
        $_SESSION = array();
        session_destroy();
        return false;
    }
    $_SESSION['time'] = time();
    return true;
}