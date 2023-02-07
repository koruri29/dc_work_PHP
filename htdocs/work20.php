<?php
$filename = chmod('work19.txt', 0666);
$fp = fopen($filename, 'a');

if (! empty($_POST)) {
	$title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
	$text = htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8');
	$img_desp = '';
	
	if (empty($title) || empty($text)) {
		print '<p>入力情報が不足しています。</p>';
	} else {
		if (! empty($_FILES['img'])) {
			$img_desp = '<img src="./img/' . basename($_FILES['img']['name']) . '">';
			$save = 'img/' . basename($_FILES['img']['name']);
			if (move_uploaded_file($_FILES['img']['tmp_name'], $save)) {
				print '<p>アップロード成功しました。</p>';
			} else {
				print '<p>アップロード失敗しました。</p>';
			}
		}
		fwrite($fp, $title . '：' . $text . $img_desp . "\n");
	}
}
fclose($fp);

$fp = fopen($filename, 'r');
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK19</title>
	</head>
	<body>
		<form method="post" enctype="multipart/form-data">
			タイトル
			<input type="text" name="title">
			書き込み内容
			<input type="text" name="text">
			画像ファイル
			<input type="file" name="img">
			<input type="submit" value="送信">
		</form>
		<ul>
			<?php
			$text_array = [];
			while ($line = fgets($fp)) {
				array_unshift($text_array, $line);
			}
			for ($i = 0; $i < count($text_array); $i++) {
				print '<li>' . $text_array[$i] . '</li>';
			}
			?>
		</ul>
		<?php fclose($fp); ?>
	</body>
</html>