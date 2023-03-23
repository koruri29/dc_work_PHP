<?php
require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_user.php');


$db = getDb();


if ($user_name = checkAuthToken($db)) {
    $timeout = setTimeout($db);
    $token = setAuthToken($db, $user_name);
    setcookie('token', $token, time() + $timeout);
}


session_start();
session_regenerate_id(true);


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