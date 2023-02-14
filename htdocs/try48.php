<?php
$dsn = 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY48</title>
	</head>
	<body>
		<?php
			try {
				$db = new PDO($dsn, $login_user, $password);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
				$db->beginTransaction();
	
				$sql = 'UPDATE product SET price = ? WHERE product_id = ?';
				$stmt = $db->prepare($sql);
				$stmt->bindValue(1, 170);
				$stmt->bindValue(2, '1');
				$stmt->execute();
	
				$row = $stmt->rowCount();
				print $row . '件更新しました。';
				
				$db->commit();
			} catch (PDOException $e) {
				print $e->getMessage();
				$db->rollBack();
			}
		?>
	</body>
</html>