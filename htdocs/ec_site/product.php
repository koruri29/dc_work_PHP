<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');


//ログイン認証
if (! isLogin($_SESSION)) {
    header('Location: index.php');
    exit();
}


$db = getDb();


$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    addToCart($db);
} 


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_product.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_product.php');
include_once ('../../include/view/ec_footer.html');
