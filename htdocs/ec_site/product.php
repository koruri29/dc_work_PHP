<?php
// if (mkdir('../../include/images', 0644)) {
//     print 'フォルダを作成しました。';
// } else {
//     print 'フォルダの作成に失敗しました。';
// }
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql_product.php');
require_once ('../../include/model/ec_sql_user.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_session.php');


if (! isLogin($_SESSION) || $_SESSION['id'] != 'ec_admin') {
    header('Location: login.php');
    exit();
}
print '<h1> test.product</h1>';

showProductData();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
}