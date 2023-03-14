<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_user.php');


//ログイン認証
if (! isLogin($_SESSION)) {
    header('Location: login.php');
    exit();
}

$db = getDb();

$msg = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    insertSales($db);
    // showproducts($db);
    $cart_id = $_SESSION['cart_id'];
    restartCart($db);
}
var_dump($cart_id);


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_thankyou.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_thankyou.php');
include_once ('../../include/view/ec_footer.html');
