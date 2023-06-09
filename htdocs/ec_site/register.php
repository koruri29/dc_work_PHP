<?php
require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_user.php');


$db = getDb();


//クッキー（セッション）の期限
$timeout = 30 * 60;
if ($_POST['auto-login'] == 'on' || $user_name = checkAuthToken($db)) {
    $timeout = setTimeout($db);
}

session_start();
session_regenerate_id(true);

setAutologin($db);//自動ログインがonならクッキーとトークンをセット


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


$error = array();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkUserThenResister($db);
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_register.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_register.php');
include_once ('../../include/view/ec_footer.html');