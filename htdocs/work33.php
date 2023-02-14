<?php
$dsn = 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK33</title>
	</head>
	<body>
		<?php
			try {
				$db = new PDO($dsn, $login_user, $password);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql = 'SELECT * FROM product WHERE price <= :price';
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':price', 200);
				$stmt->execute();


				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					print $row['product_name'] . ': ' . $row['price'] . '円<br>';
				}
			} catch (PDOException $e) {
				print $e->getMessage();
				exit();
			}
		?>
	</body>
</html>