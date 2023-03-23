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
if (! isLogin($db)) {
    header('Location: index.php');
    exit();
}

$products = fetchPublicProduct($db);
$error = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['submit'] == 'カートに入れる'){
        addToCart($db);
    } else {//検索
        $products = searchProduct($db);
    }
} 


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_product.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_product.php');
include_once ('../../include/view/ec_footer.html');
