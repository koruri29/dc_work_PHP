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


//index.phpから遷移の場合、自動ログインを判断して前回のカートにつなげるor新しくカートを作成
if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == '/omiya/0006/ec_site/index.php') {
    if (checkAuthToken($db)) {
        $stmt = fetchAutoLogin($db);
        $autologin_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! empty($autologin_info['cart_id'])) {
            $_SESSION['cart_id'] = $autologin_info['cart_id'];
        } else {
            createCart($db);
            $_SESSION['cart_id'] = lastInsertId($db);
            setCartIdToAutologin($db);
        }
    } else {
        createCart($db);
        $_SESSION['cart_id'] = lastInsertId($db);
        setCartIdToAutologin($db);
    }
}

$products = fetchPublicProduct($db);
$error = '';
$msg = array();


//post（カートに入れるor検索）が来た場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['cart-in'] == 'on'){
        addToCart($db);
    } else {//検索
        $products = searchResult($db);
    }
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_product.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_product.php');
include_once ('../../include/view/ec_footer.html');
