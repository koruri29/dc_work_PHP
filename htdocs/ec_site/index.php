<?php
require_once ('../../include/model/ec_getDb.php');
require_once ('../../include/model/ec_common.php');
require_once ('../../include/model/ec_sql.php');
require_once ('../../include/model/ec_product.php');
require_once ('../../include/model/ec_user.php');


$db = getDb();


//セッション（クッキー）の期限
$timeout = 30 * 60;
if ($_POST['auto-login'] == 'on' || $user_name = checkAuthToken($db)) {
    $timeout = setTimeout($db);
}

session_start();
session_regenerate_id(true);

if (! isSessionInEffect()) setAutologin($db);//クッキーとトークンをセット


$error = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    authUser($db);
}


// if (isEqualCartAndCookie($db)) {
//     //
// } else {
//     if (isset($_COOKIE['token'])) {
//         $stmt = fetchAutoLogin($db);
//         $autologin_info = $stmt->fetch(PDO::FETCH_ASSOC);
//         if (! empty($autologin_info['cart_id'])) {
//             var_dump($autologin_info['cart_id']);
//             $_SESSION['cart_id'] = $autologin_info['cart_id'];
//             print '自動ログインでセットしたよ： cart_id' . $_SESSION['cart_id'];
//         } else {
//             createCart($db);
//             $_SESSION['cart_id'] = lastInsertId($db);
//             setCartIdToAutologin($db);
//             print '自動ログインだけど新しくセットしたよ： cart_id' . $_SESSION['cart_id'];
//         }
//     } else {
//         createCart($db);
//         $_SESSION['cart_id'] = lastInsertId($db);
//         setCartIdToAutologin($db);
//         print 'セットしたよ： cart_id' . $_SESSION['cart_id'];
//     }
// }


if (isLogin($db)) {
    // header('Location: product.php');
    // exit();
}





include_once ('../../include/view/ec_head.html');
include_once ('../../include/view/ec_head_index.html');
include_once ('../../include/view/ec_header.php');
include_once ('../../include/view/ec_index.php');
include_once ('../../include/view/ec_footer.html');