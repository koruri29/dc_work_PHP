<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql_user.php');
require_once ('../../include/model/ec_user.php');
require_once ('../../include/model/ec_session.php');


if (isLogin($_SESSION)) {
    header('Location: index.php');
    exit();
}


$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    authUser($_POST);
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_login.html');
include_once ('../../include/view/ec_login.php');
include_once ('../../include/view/ec_footer.html');