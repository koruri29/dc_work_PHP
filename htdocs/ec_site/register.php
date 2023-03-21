<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_user.php');


$db = getDb();


if (isLogin($db)) {
    header('Location: product.php');
    exit();
}


$error = array();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkUserThenResister();
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_register.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_register.php');
include_once ('../../include/view/ec_footer.html');