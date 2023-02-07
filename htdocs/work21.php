<?php
	if (! empty($_POST)) {
		foreach ($_POST as $key => $val) {
			$post[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); 
		}
	}

	if (! empty($post['dc_check'])) {
		$pattern = '/^[a-zA-Z]+$/';
		if (! preg_match($pattern, $post['dc_check'])) {
			print '<p>正しい入力形式ではありません。</p>';
			exit();
		} else {
			if (preg_match('/.*[dc].*/', $post['dc_check'])) {
				print '<p>ディーキャリアが含まれています。</p>';
			} else if (preg_match('/.*[end]+$/', $post['dc_check'])) {
				print '<p>終了です！</p>';
			}
		}
	}


	if (!empty($post['phone_number'])) {
		$pettern = '/^[090]+[-]+[0-9]{4}[-]+[0-9]{4}||^[080]+[-]+[0-9]{4}[-]+[0-9]{4}||^[070]+[-]+[0-9]{4}[-]+[0-9]{4}/';
		if (! preg_match($pettern, $post['phone_number'])) {
			print '<p>携帯電話番号の形式ではありません。</p>';
		}
	}

?><!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK21</title>
	</head>
	<body>
		<form method="post">
			半角アルファベットのみで入力してください。
			<input type="text" name="dc_check">
			<input type="submit" value="send">
		</form>

		<form method="post">
			携帯電話の番号のみ入力できます。
			<input type="text" name="phone_number">
			<input type="submit" value="send">
		</form>
	</body>
</html>