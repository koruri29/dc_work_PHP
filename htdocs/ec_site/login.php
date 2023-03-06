<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/getDb.php');
require_once ('../../include/model/common.php');
require_once ('../../include/model/sqlQuery.php');
require_once ('../../include/model/user.php');


$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    authUser($_POST);
}


include_once ('../../include/view/head.html');
include_once ('../../include/view/head_login.html');
include_once ('../../include/view/login.php');
include_once ('../../include/view/footer.html');