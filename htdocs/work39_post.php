<?php
require_once '../include/model/model39.php';


//データベース接続
$pdo = 	get_connection();


//投稿があった場合の処理
if (isset($_POST['title']) || isset($_POST['img'])) {
	$error = array();

	//投稿されたファイルのチェック
	check_title($_POST['title']);
	check_file($_FILES);

	//エラーがなければSQL実行
	if (count($error) === 0) {
		$msg = upload_photo($pdo, $_POST['title'], $_FILES);
	}
} else if(! empty($_POST['display'])) {//表示・非表示の変更
	$msg = display_change($pdo, $_POST['display'], $_POST['image_id'], $_POST['image_name']);
}


//データベースから画像データを取得
$stmt = fetch_all_images($pdo);


include_once '../include/view/view39.php';