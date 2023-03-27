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


//ログイン認証
if (! isLogin($db)) {
    header('Location: index.php');
    exit();
}


$error = array();
$msg = '';
$product_num = countProductInCart($db);//カート内商品の種類数。formで渡す用

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (validateQty()) {
        changeQtyInCart($db);
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
$stmt = fetchAllInCart($db);
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    if (isset($error['stock'])) {//カート内商品が売り切れていたら、購入ボタンを表示しない
        $show_purchase_btn = false;
    } else {
        $show_purchase_btn = true;
    }
} else {
    $show_purchase_btn = false;
}

include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_cart.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_cart.php');
include_once ('../../include/view/ec_footer.html');
