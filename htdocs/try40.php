<?php
	$host = 'mysql34.conoha.ne.jp';
	$login_user = 'bcdhm_omiya_pf0006';
	$password = 'N3p!CxYc';
	$database = 'bcdhm_omiya_pf0006';
	$error_msg = [];
	$product_name;
	$price;
	$price_val;
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY40</title>
	</head>
	<body>
		<?php
			$db = new mysqli($host, $login_user, $password, $database);
			if ($db->connect_error) {
				echo $db->connect_error;
				exit();
			} else {
				$db->set_charset('utf8');
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['price'])) {
					$price = $_POST['price'];
				}
				$db->begin_transaction();

				$update = 'UPDATE product SET price = ' . $price . ' WHERE product_id = 1;';
				if ($result = $db->query($update)) {
					$row = $db->affected_rows;
				} else {
					$error_msg[] = 'UPDATE実行エラー[実行SQL]' . $update;
				}

				if (count($error_msg) == 0) {
					echo $row . '件更新しました。';
					$db->commit();
				} else {
					echo '更新が失敗しました。';
					$db->rollback();
				}
				// var_dump($error_msg);
			}

			$select = 'SELECT product_name, price FROM product WHERE product_id = 1;';
			if ($result = $db->query($select)) {
				while ($row = $result->fetch_assoc()) {
					$product_name = $row['product_name'];
					$price = $row['price'];
				}
				$result->close();
			}
			if ($price == 150) {
				$price_val = 130;
			} else {
				$price_val = 150;
			}

			$db->close();
		?>
		<form method="post">
			<p><?php echo $product_name ?>の現在の価格は<?php echo $price; ?>円です。</p>
			<input type="radio" name="price" value="<?php echo $price_val; ?>" checked><?php echo $price_val; ?>円に変更する
			<input type="submit" value="送信">
		</form>
	</body>
</html>