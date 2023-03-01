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
 * 画像名のバリデーションチェック
 * 
 * @return void
 */
function check_title($title) {
	global $error;
	if (empty($title)) {
		$error = array_merge($error, ['title' => '画像名が入力されていません。']);
	} else if (! preg_match('/^[a-zA-Z]+$/', $title)) {
		$error = array_merge($error, ['character' => '画像名は半角英数字で入力してください。']);
	}
}

/**
 * 画像ファイルチェック、アップロード
 * 
 * 中身のあるファイルであるか、拡張子は正しいかをチェックし、
 * 問題がなければアップロード処理を行う
 * 
 * @param $file postされたファイル情報
 * @param string $title バリデーションチェック済の画像タイトル
 * @return string|array $filename 日時の文字列を付加した$title または $error エラー配列
 */
function check_file($file) {
	global $error;
	if ($file['img']['size'] === 0) {
		$error = array_merge($error, ['img' => '画像が選択されていません。']);
	} else {
		$pathinfo = pathinfo($file['img']['name']);
		$ext = strtolower($pathinfo['extension']);
		if ($ext != 'jpg' && $ext != 'png') {
		$error = array_merge($error, ['ext' =>'jpgまたはpngファイル以外が選択されています。']);
		}
	}
}


/**
 * データベースへ画像の情報を登録
 * 
 * @param object $pdo
 * @param string $filename 画像タイトル
 * @return string|void $msg 登録完了メッセージ または ロールバック
 */
function upload_photo($pdo, $title, $file) {
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
	$filename = date('Ymdhis') . $title;
	move_uploaded_file($file['img']['tmp_name'], './images/' . basename($filename));

	$pdo->beginTransaction();
	$insert = 'INSERT INTO photo_submission(image_name, public_flag, create_date, update_date) VALUES(:name, :flag, :c_date, :u_date)';
	$stmt = $pdo->prepare($insert);
	$public_flag = 1;
	$date = date('Y-m-d');
	$stmt->bindValue(':name', $filename);
	$stmt->bindValue(':flag', $public_flag);
	$stmt->bindValue(':c_date', $date);
	$stmt->bindValue(':u_date', $date);

	global $error;
	if ($stmt->execute()) {
		$msg = '登録が完了しました。';
	} else {
		$error = array_merge($error, ['sql' => 'SQL実行エラー: ' . $insert]);
	}
	if (empty($error['sql'])) {
		$pdo->commit();
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


/**
 * 取得した画像一覧を表示
 * 
 * @param array $stmt データベースから取得した画像の情報の配列
 * @return void
 */
function render_img_post($stmt) {
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		print '<div class="img">';
		print '<p>' . substr($row['image_name'], 14) . '</p>';
		print '<img src="./images/' . $row['image_name'] . '">';
		print '<form method="post">';
		print '<input type="hidden" name="image_id" value="' . $row['image_id'] . '">';
		print '<input type="hidden" name="image_name" value="' . substr($row['image_name'], 14) . '">';
		print '<input type="submit" class="display" name="display" value="';
		$row['public_flag'] == 1 ? print '非表示にする' : print '表示する';
		print '">';
		print '</form>';
		print '</div>';
	}
}


/**
 * 取得した画像一覧を表示
 * 
 * @param object $pdo
 * @return void
 */
function render_img_view() {
	$pdo = get_connection();
	$select = 'SELECT * FROM photo_submission WHERE public_flag = 1';
	$stmt = $pdo->query($select);

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		print '<div class="img">';
		print '<p>' . substr($row['image_name'], 14) . '</p>';
		print '<img src="./images/' . $row['image_name'] . '">';
		print '</div>';
	}
}