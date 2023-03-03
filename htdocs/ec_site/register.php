<?php
require_once ('../../include/model/common.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = sanitize($_POST);
    
    //ユーザー名5字以上、半角英数字
    //パス8字以上、半角英数字
    //クエリ
    //ユーザー名登録済かどうか

}

include_once ('../../include/view/head_register.html');
include_once ('../../include/view/register.html');
include_once ('../../include/view/footer.html');