<?php
$filename = chmod('work19.txt', 0666);
$fp = fopen($filename, 'a');

if (! empty($_POST)) {
	$title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
	$text = htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8');
	
	if (empty($title) || empty($text)) {
		print '<p>入力情報が不足しています。</p>';
	} else {
		fwrite($fp, $title . '：' . $text . "\n");
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
		<form method="post">
			タイトル
			<input type="text" name="title">
			書き込み内容
			<input type="text" name="text">
			<input type="submit" value="送信">
		</form>
		<ul>
			<?php
			while ($line = fgets($fp)) {
				echo '<li>' . $line . '</li>';
			}
			?>
		</ul>
		<?php fclose($fp); ?>
	</body>
</html>