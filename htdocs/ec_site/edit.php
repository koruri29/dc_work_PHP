<?php
require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_user.php');


$db = getDb();


//クッキー（セッション）の期限
$timeout = 30 * 60;

session_start();
session_regenerate_id(true);

setAutologin($db);//自動ログインがonならクッキーとトークンをセット


//ログイン認証
if (! isLogin($db) || $_SESSION['user_name'] != 'ec_admin') {
    header('Location: index.php');
    exit();
}


$error_register = array();
$error_update = array();
$msg_register = '';
$msg_update = array();
$product_num = countAllProduct($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        registerProduct($db);
    } else {
        proceedUpdateProduct($db);
    }
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_edit.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_edit.php');
include_once ('../../include/view/ec_footer.html');