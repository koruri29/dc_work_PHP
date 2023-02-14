<?php
$dsn = 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY46</title>
	</head>
	<body>
		<?php
			try {
				$db = new PDO($dsn, $login_user, $password);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$db->beginTransaction();

				$sql = 'UPDATE product SET price = 160 WHERE product_id = 1';
				$stmt = $db->query($sql);
				$rec = $stmt->rowCount();
				print $rec . '件更新しました。';

				$db->commit();
			}catch (PDOException $e) {
				print $e->getMessage();
				$db->rollBack();
			}
		?>
	</body>
</html>