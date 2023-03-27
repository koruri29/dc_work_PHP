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

setAutologin($db);//自動ログインがonならクッキーとトークンをセット


$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    authUser($db);//ログイン成功なら同時にカートも作成
}


if (isLogin($db)) {
    header('Location: product.php');
    exit();
}





include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_index.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_index.php');
include_once ('../../include/view/ec_footer.html');