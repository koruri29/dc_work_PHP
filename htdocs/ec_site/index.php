<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_user.php');


if (isLogin($_SESSION)) {
    header('Location: product.php');
    exit();
}

$db = getDb();
$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    authUser($db);//ログイン成功なら同時にカートも作成
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_index.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_index.php');
include_once ('../../include/view/ec_footer.html');