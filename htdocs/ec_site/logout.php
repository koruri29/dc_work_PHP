<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_sql.php');


$db = getDb();

$_SESSION = array();
session_destroy();
deleteToken($db);

header('Location: ./index.php');
exit();