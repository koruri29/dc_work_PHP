<?php
session_start();
session_regenerate_id(true);

// if (mkdir('../../include/images', 0755)) {
//     print 'フォルダを作成しました。';
// } else {
//     print 'フォルダの作成に失敗しました。';
// }

// if (rmdir('../../include/images')) {
//     print '削除しました。';
// } else {
//     print '削除に失敗しました。';
// }

// if (is_dir('../../include/images')) {
//     // ディレクトリ内に別のディレクトリやファイルがあるかどうか確認
//     $files = array_diff(scandir('../../include/images'), array('.','..'));
//     if (empty($files)) {
//         // ディレクトリを削除
//         echo "ディレクトリ:sampleを削除";
//         rmdir('../../include/images');
//     } else {
//         print 'ディレクトリが空ではありません。';
//     }
// } else {
//     print 'ディレクトリが存在しません。';
// }

require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');


//ログイン認証
if (! isLogin($_SESSION) || $_SESSION['user_name'] != 'ec_admin') {
    header('Location: index.php');
    exit();
}

//商品登録
$db = getDb();


$msg_register = '';
$msg_update = '';
 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        registerProduct($db);
    } else {
        $error_update = validateProduct();
        if (empty($error_update)) {
            updateStock($db);
            if ($_POST['display-flag']) updateFlag($db);
            if (! empty($_POST['delete'])) deleteProduct($db);
        }
    }
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_edit.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_edit.php');
include_once ('../../include/view/ec_footer.html');