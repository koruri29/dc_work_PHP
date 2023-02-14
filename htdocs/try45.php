<?php
$dsn = 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;';
$login_user = 'bcdhm_omiya_pf0006';
$password = 'N3p!CxYc';
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY45</title>
	</head>
	<body>
		<?php
			try {
				$db = new PDO($dsn, $login_user, $password);
			} catch (PDOException $e) {
				echo $e->getmessage();
				exit();
			}

			$sql = 'SELECT product_name, price FROM product WHERE price <= 100';
			if ($result = $db->query($sql)) {
				while($row = $result->fetch()) {
					echo $row['product_name'] . $row['price'] . '<br>';
				}
			}
		?>
	</body>
</html>