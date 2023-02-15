<?php
define('DSN', 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;');
define('LOGIN_USER', 'bcdhm_omiya_pf0006');
define('PASSWORD', 'N3p!CxYc');

$db = new PDO(DSN, LOGIN_USER, PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');

//投稿があった場合の処理
if (isset($_POST['title']) || isset($_POST['img'])) {
	$error = array();

	check_img($title);
	$filename = check_file($_FILES, $title);

	//エラーがなければSQL実行
	if (count($error) === 0) {
		$msg = upload_photo($db, $filename);
	}
}

//表示・非表示の処理
if (! empty($_POST['display'])) {
	$msg = display_change($db, $_POST['display'], $_POST['image_id'], $_POST['image_name']);
}


//functions
//画像名チェック
function check_img($title) {
	global $error;
	if (empty($title)) {
		$error = array_merge($error, ['title' => '画像名が入力されていません。']);
	} else if (! preg_match('/^[a-zA-Z]+$/', $title)) {
		$error['character'] = '画像名は半角英数字で入力してください。';
	}
}

//画像ファイルチェック
function check_file($file, $title) {
	global $error;
	if ($file['img']['size'] === 0) {
		$error = array_merge($error, ['img' => '画像が選択されていません。']);
	} else {
		$pathinfo = pathinfo($file['img']['name']);
		$ext = strtolower($pathinfo['extension']);
		if ($ext != 'jpg' && $ext != 'png') {
		$error = array_merge($error, ['ext' =>'jpgまたはpngファイル以外が選択されています。']);
		} else {
			$filename = date('Ymdhis') . $title;
			move_uploaded_file($file['img']['tmp_name'], './images/' . basename($filename));
			return $filename;
		}
	}
}


function upload_photo($db, $filename) {
	$db->beginTransaction();
	$insert = 'INSERT INTO photo_submission(image_name, public_flag, create_date, update_date) VALUES(?, ?, ?, ?)';
	$stmt = $db->prepare($insert);
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
		$db->commit();
		$stmt->close();
		return $msg;
	} else {
		$db->rollBack();
	}
}


	
//表示・非表示の設定
function display_change($db, $display, $image_id, $image_name) {
	$display = htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
	$image_id = htmlspecialchars($image_id, ENT_QUOTES, 'UTF-8');
	$image_name = htmlspecialchars($image_name, ENT_QUOTES, 'UTF-8');

	if ($display == '非表示にする') {
		$update = 'UPDATE photo_submission SET public_flag = 0 WHERE image_id = ' . $image_id;
		$db->query($update);
		$msg = $image_name . 'を非表示に変更しました。';
		return $msg;
	} else if ($display == '表示する') {
		$update = 'UPDATE photo_submission SET public_flag = 1 WHERE image_id = ' . $image_id;
		$db->query($update);
		$msg = $image_name . 'を表示に変更しました。';
		return $msg;
	}
}

//画像一覧を表示
function render_img_post($db) {
		$select = 'SELECT * FROM photo_submission WHERE 1';
		$stmt = $db->query($select);
	
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
	$stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<style>
			img {width: 150px;}
			.img { display: inline-block; text-align: center;}
			.non-display {background-color: #ccc;}
		</style>
		<title>投稿ページ</title>
	</head>
	<body>
		<?php if (! empty($error['sql'])) print $error['sql']; ?>
		<?php if (! empty($msg)) print $msg; ?>
		<form method="post" enctype="multipart/form-data">
			<?php if (! empty($error['title'])) print $error['title']; ?>
			<?php if (! empty($error['character'])) print $error['character']; ?>
			画像名：<input type="text" name="title">
			<br>
			<?php if (! empty($error['img'])) print $error['img']; ?>
			<?php if (! empty($error['ext'])) print $error['ext']; ?>
			画像ファイル：<input type="file" name="img">
			<br>
			<input type="submit" value="画像投稿">
		</form>
		<a href="work30_view.php">画像一覧ページへ</a>
		<div class="images">
			<?php
				render_img_post($db);
			?>
		</div>
		<script>
			const displayBtn = document.getElementsByClassName('display');
			const img = document.getElementsByClassName('img');
			for (let i = 0; i < img.length; i++) {
				if (displayBtn[i].value == '表示する') {
					img[i].classList.add('non-display');
				} else {
					img[i].classList.remove('non-display');
				}
			}
		</script>
	</body>
</html>