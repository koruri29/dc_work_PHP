<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');


$db = getDb();


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


//購入ボタンを表示するか（カートに商品が入っているか）
$stmt = fetchAllInCart($db);
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    $does_show_button = true;
} else {
    $does_show_button = false;
}

include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_cart.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_cart.php');
include_once ('../../include/view/ec_footer.html');
