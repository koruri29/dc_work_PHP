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


// ログイン認証
if (! isLogin($db)) {
    header('Location: index.php');
    exit();
}
if ($_SESSION['user_name'] != 'ec_admin') {
    header('Location: product.php');
    exit(); 
}


$error_register = array();
$error_update = array();
$msg_register = '';
$msg_update = array();
$product_num = countAllProduct($db);
$stmt = fetchAllProduct($db);

var_dump($_POST);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        registerProduct($db);
    } else if (isset($_POST['update'])) {
        proceedUpdateProduct($db);
    } else {
        $stmt = searchResult($db);
        $product_num = countSearchResult($db);
    }
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_edit.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_edit.php');
include_once ('../../include/view/ec_footer.html');