<?php
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