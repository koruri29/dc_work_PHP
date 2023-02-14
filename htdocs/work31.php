<?php
$dsn = 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK31</title>
	</head>
	<body>
		<?php
			try {
				$db = new PDO($dsn, $login_user, $password);
			} catch (PDOException $e) {
				print $e->getMessage();
				exit();
			}

			$sql = 'SELECT p.product_name, c.category_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id WHERE p.category_id = 1';
			if ($stmt = $db->query($sql)) {
				while ($rec = $stmt->fetch()) {
					print $rec['category_name'] . ': ' . $rec['product_name'] . '<br>';
				}
			}
		?>
	</body>
</html>