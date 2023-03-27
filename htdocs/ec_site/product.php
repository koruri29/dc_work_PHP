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


if (! isLogin($db)) {
    header('Location: index.php');
    exit();
}

$products = fetchPublicProduct($db);
$error = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['login'] == 'login') {//ログイン直後、商品一覧ページへリダイレクトされたとき
        createCart($db);//ログイン時にカートを作成
        $_SESSION['cart_id'] = lastInsertId($db);
        setCartIdToAutologin($db);
    } else if ($_POST['cart-in'] == 'on'){
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
