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

if (! isSessionInEffect()) setAutologin($db);//クッキーとトークンをセット


//ログイン認証
if (! isLogin($db)) {
    header('Location: index.php');
    exit();
} else {
    if ($_SESSION['user_name'] == 'ec_admin' && $_SERVER['REQUEST_URI'] != '/omiya/0006/ec_site/edit.php') {
        header('Location: edit.php');
        exit();
    }
    $user_name = checkAuthToken($db);
    if ($user_name == 'ec_admin' && $_SERVER['REQUEST_URI'] != '/omiya/0006/ec_site/edit.php') {
        header('Location: edit.php');
        exit();
    }
}


$msg = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = fetchAllInCart($db);
    proceedSales($db, $stmt);
    $stmt = getSales($db);
    $total = calcTotal($db, 'getSales');//合計金額の計算
    restartCart($db);
}




include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_thankyou.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_thankyou.php');
include_once ('../../include/view/ec_footer.html');
