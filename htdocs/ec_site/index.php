<?php
require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_user.php');


$db = getDb();


//セッション（クッキー）の期限
$timeout = 30 * 60;
if ($_POST['auto-login'] == 'on' || $user_name = checkAuthToken($db)) {
    $timeout = setTimeout($db);
}


session_start();
session_regenerate_id(true);


$error = array();
//ログインセッションが切れていれば、ユーザー認証を行う
if (! isSessionInEffect()) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        authUser($db);
    }
}


setAutologin($db);//自動ログインの場合、クッキーとトークンをセット


if (isLogin($db)) {
     if ($_SESSION['user_name'] == 'ec_admin' && $_SERVER['REQUEST_URI'] != '/omiya/0006/ec_site/edit.php') {
        header('Location: edit.php');
        exit();
    }
    $user_name = checkAuthToken($db);
    if ($user_name == 'ec_admin' && $_SERVER['REQUEST_URI'] != '/omiya/0006/ec_site/edit.php') {
        header('Location: edit.php');
        exit();
    }
    header('Location: product.php');
    exit();
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_index.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_index.php');
include_once ('../../include/view/ec_footer.html');