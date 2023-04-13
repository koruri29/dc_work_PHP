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


$error = array();
$msg = array();
$product_num = countProductInCart($db);//カート内商品の種類数。formで渡す用


//削除または数量変更があった場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    for ($i = 0; $i < $_POST['product-num']; $i++) {
        if ($_POST['delete' . $i] != '') {
            deleteProductInCart($db, $_POST['product-id' . $i]);
        } else {//削除以外の場合はバリデーションチェック
            validateQty($_POST['qty' . $i]);
        }
    }
    if (empty($error)) {
        for ($i = 0; $i < $_POST['product-num']; $i++) {
            changeQtyInCart($db, $_POST['product-id' . $i], $_POST['qty' . $i]);
        }
    }
}


//カート内の商品が売り切れたときのエラー表示
isStockAvailable($db);


//合計金額の計算
$total = calcTotal($db, 'fetchAllInCart');


//数量変更ボタンを表示するか（カートに商品が入っているか）
$stmt = fetchAllInCart($db);
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    $show_change_btn = true;
} else {
    $show_change_btn = false;
}

//購入ボタンを表示するか
$show_purchase_btn = doesShowPurchaseButton($db);
for ($i = 0; $i < $product_num; $i++) {
    if (isset($error['stock' . $i])) {//カート内商品が売り切れていたら、購入ボタンを表示しない
        $show_purchase_btn = false;
    }
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_cart.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_cart.php');
include_once ('../../include/view/ec_footer.html');
