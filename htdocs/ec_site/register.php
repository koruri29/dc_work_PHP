<?php
require_once ('../../include/model/getDb.php');
require_once ('../../include/model/common.php');
require_once ('../../include/model/sqlQuery.php');
require_once ('../../include/model/user.php');


$error = array();
$msg = array();
$post = sanitize($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkUserThenResister($_POST);
}


include_once ('../../include/view/head_register.html');
include_once ('../../include/view/register.php');
include_once ('../../include/view/footer.html');