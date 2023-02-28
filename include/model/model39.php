<?php
/**
 * DB接続を行いPDOインスタンスを返す
 * 
 * @return object $pdo
 */
function get_connection() {
	try {
		define('DSN', 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;');
		define('LOGIN_USER', 'bcdhm_omiya_pf0006');
		define('PASSWORD', 'N3p!CxYc');
		$pdo = new PDO(DSN, LOGIN_USER, PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo $e->getMessage();
		exit();
	}
	return $pdo;
}


/**
 * データベースへ画像の情報を登録
 * 
 * @param object $pdo
 * @param string $filename 画像タイトル
 * @return string|void $msg 登録完了メッセージ または ロールバック
 */
function upload_photo($pdo, $filename) {
	$pdo->beginTransaction();
	$insert = 'INSERT INTO photo_submission(image_name, public_flag, create_date, update_date) VALUES(?, ?, ?, ?)';
	$stmt = $pdo->prepare($insert);
	$public_flag = 1;
	$date = date('Y-m-d');
	$stmt->bindValue(array($filename, $public_flag, $date, $date));

	global $error;
	if ($stmt->execute()) {
		$msg = '登録が完了しました。';
	} else {
		$error = array_merge($error, ['sql' => 'SQL実行エラー: ' . $insert]);
	}
	if (empty($error['sql'])) {
		$pdo->commit();
		$stmt->close();
		return $msg;
	} else {
		$pdo->rollBack();
	}
}


/**
 * 一覧画像の表示・非表示の設定
 * 
 * @param object $pdo
 * @param string $display 文字列「表示する」または「非表示にする」
 * @param string $image_id 画像ID
 * @param string $image_name データベースに保存されている画像の名前
 * @return string $msg 表示・非表示いずれへ変更したかを表示
 */
function display_change($pdo, $display, $image_id, $image_name) {
	$display = htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
	$image_id = htmlspecialchars($image_id, ENT_QUOTES, 'UTF-8');
	$image_name = htmlspecialchars($image_name, ENT_QUOTES, 'UTF-8');

	if ($display == '非表示にする') {
		$update = 'UPDATE photo_submission SET public_flag = 0 WHERE image_id = ' . $image_id;
		$pdo->query($update);
		$msg = $image_name . 'を非表示に変更しました。';
		return $msg;
	} else if ($display == '表示する') {
		$update = 'UPDATE photo_submission SET public_flag = 1 WHERE image_id = ' . $image_id;
		$pdo->query($update);
		$msg = $image_name . 'を表示に変更しました。';
		return $msg;
	}
}


/**
 * データベースの画像をすべて取得
 * 
 * @param object $pdo
 * @return array $record 結果の配列
 */
function fetch_all_images($pdo) {
	$select = 'SELECT * FROM photo_submission WHERE 1';
	$stmt = $pdo->query($select);
	return $stmt;
}