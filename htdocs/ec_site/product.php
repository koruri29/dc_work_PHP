<?php
session_start();
session_regenerate_id(true);

// if (mkdir('../../include/images', 0644)) {
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
if (! isLogin($_SESSION) || $_SESSION['id'] != 'ec_admin') {
    header('Location: login.php');
    exit();
}


$db = getDb();


$msg = array();
$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    registerProduct($db, $_FILES, $_POST);
}


include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_product.html');
include_once ('../../include/view/ec_product.php');
include_once ('../../include/view/ec_footer.html');