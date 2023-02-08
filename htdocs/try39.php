<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY39</title>
	</head>
	<body>
		<?php
			$db = new mysqli('mysql34.conoha.ne.jp', 'bcdhm_omiya_pf0006', 'N3p!CxYc', 'bcdhm_omiya_pf0006');
			if ($db->connect_error) {
				echo $db->connect_error;
				exit();
			} else {
				$db->set_charset('utf8');
			}

			$sql = 'SELECT product_name, price FROM product WHERE price <= 100';
			if ($result = $db->query($sql)) {
				foreach ($result as $row) {
					echo $row['product_name'] . $row['price'] . '<br>';
				}
				$result->close();
			}
			$db->close();
		?>
	</body>
</html>