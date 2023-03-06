<?php

function setSession(array $user): void {
    $_SESSION['id'] = $user['id'];
    $_SESSION['time'] = time();
}

function isLogin(array $session): bool {
    if (empty($_SESSION['id'])) {
        return false;
    }
    if ($_SESSION['time'] + 3600 <= time()) {
        $_SESSION = array();
        session_destroy();
        return false;
    }
    return true;
}
//if (isLogin($_SESSION) == false) header('Location: login.php');