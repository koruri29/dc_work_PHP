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
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_session.php');


if (! isLogin($_SESSION) || $_SESSION['id'] != 'ec_admin') {
    header('Location: login.php');
    exit();
}
print '<h1> test.product</h1>';


$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    registerProduct($_FILES, $_POST);
}

showProductData();


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_login.html');
include_once('../../include/view/ec_product.php');
include_once ('../../include/view/ec_footer.html');