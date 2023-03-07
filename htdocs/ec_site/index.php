<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql_product.php');
require_once ('../../include/model/ec_sql_user.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_session.php');


if (! isLogin($_SESSION)) {
    header('Location: login.php');
    exit();
}
print '<h1>テスト</h1>';