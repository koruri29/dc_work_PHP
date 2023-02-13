<?php
$host = 'mysql34.conoha.ne.jp';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
$database = 'bcdhm_omiya_pf0006';

$db = new mysqli($host, $login_user, $password, $database);
if ($db->connect_error) {
	print $db->connect_error;
	exit();
} else {
	$db->set_charset('utf8');
}


$title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');


if (isset($_POST['title']) || isset($_POST['img'])) {
	$error = array();
	//画像名チェック
	if (empty($title)) {
		$error['title'] = '画像名が入力されていません。';
	} else if (! preg_match('/^[a-zA-Z]+$/', $title)) {
		$error['character'] = '画像名は半角英数字で入力してください。';
	}
	//画像ファイルチェック
	if ($_FILES['img']['size'] === 0) {
		$error['img'] = '画像が選択されていません。';
	} else {
		$pathinfo = pathinfo($_FILES['img']['name']);
		$ext = strtolower($pathinfo['extension']);
		if ($ext != 'jpg' && $ext != 'png') {
		 $error['ext'] = 'jpgまたはpngファイル以外が選択されています。';
		} else {
			$filename = date('Ymdhis') . $title;
			move_uploaded_file($_FILES['img']['tmp_name'], './images/' . basename($filename));
		}
	}
	//エラーの有無で分岐
	if (count($error) === 0) {
		$db->begin_transaction();
		$insert = 'INSERT INTO photo_submission(image_name, public_flag, create_date, update_date) VALUES(?, ?, ?, ?)';
		$stmt = $db->prepare($insert);
		$public_flag = 1;
		$date = date('Y-m-d');
		$stmt->bind_param('siss', $filename, $public_flag, $date, $date);
		if ($stmt->execute()) {
			$msg = '登録が完了しました。';
		} else {
			$error['sql'] = 'SQL実行エラー: ' . $insert;
		}
		if (empty($error['sql'])) {
			$db->commit();
		} else {
			$db->rollback();
		}
		$stmt->close();
	}



}
	$display = htmlspecialchars($_POST['display'], ENT_QUOTES, 'UTF-8');
	$image_id = htmlspecialchars($_POST['image_id'], ENT_QUOTES, 'UTF-8');
	$image_name = htmlspecialchars($_POST['image_name'], ENT_QUOTES, 'UTF-8');
	//表示・非表示の設定
	if ($display == '非表示にする') {
		$update = 'UPDATE photo_submission SET public_flag = 0 WHERE image_id = ' . $image_id;
		$stmt = $db->query($update);
		$msg = $image_name . 'を非表示に変更しました。';
	} else if ($display == '表示する') {
		$update = 'UPDATE photo_submission SET public_flag = 1 WHERE image_id = ' . $image_id;
		$stmt = $db->query($update);
		$msg = $image_name . 'を表示に変更しました。';
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
				//画像一覧を取得
				$select = 'SELECT * FROM photo_submission WHERE 1';
				$stmt = $db->query($select);
			
				while($row = $stmt->fetch_assoc()):
			?>
				<div class="img">
					<p><?php print substr($row['image_name'], 14); ?></p>
					<img src="./images/<?php print $row['image_name']; ?>">
					<form method="post">
						<input type="hidden" name="image_id" value="<?php print $row['image_id']; ?>">
						<input type="hidden" name="image_name" value="<?php print substr($row['image_name'], 14); ?>">
						<input type="submit" class="display" name="display" value="<?php $row['public_flag'] == 1 ? print '非表示にする' : print '表示する';?>">
					</form>
				</div>
			<?php endwhile; ?>
			<?php $stmt->close(); ?>
		</div>
		<?php $db->close(); ?>
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