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


$msg = array();

//LOCK TABLES
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
