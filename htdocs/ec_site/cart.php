<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');


//ログイン認証
if (! isLogin($_SESSION)) {
    header('Location: login.php');
    exit();
}


$db = getDb();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['submit'] == 'カートに入れる') {
        addToCart($db);
    } elseif ($_POST['submit'] == '数量変更') {
        changeQty($db);
    }
} 




include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_index.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_cart.php');
include_once ('../../include/view/ec_footer.html');
