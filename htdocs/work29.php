<?php
	$host = 'mysql34.conoha.ne.jp';
	$login_user = 'bcdhm_omiya_pf0006';
	$password = 'N3p!CxYc';
	$database = 'bcdhm_omiya_pf0006';
	$error_msg = [];
	$product_id = 21;
	$product_code = 1021;
	$product_name = 'エシャロット';
	$price = 200;
	$category_id = 1;
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK29</title>
	</head>
	<body>
		<?php
			$db = new mysqli($host, $login_user, $password, $database);
			if ($db->connect_error) {
				print $db->connect_error;
				exit();
			} else {
				$db->set_charset('utf8');
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$db->begin_transaction();
				//insertの場合
				if (($_POST['insert']) =='挿入') {
					$insert = 'INSERT INTO product(product_id, product_code, product_name, price, category_id) VALUES(?, ?, ?, ?, ?)';
					$stmt = $db->prepare($insert);
					$stmt->bind_param('sssss', $product_id, $product_code, $product_name, $price, $category_id);
					
					if($stmt->execute()) {
						$row = $db->affected_rows;
					} else {
						$error_msg[] = 'INSERT実行エラー[実行SQL]' . $insert;
					}
				}
				//deleteの場合
				if (($_POST['delete']) == '削除') {
					$delete = 'DELETE FROM product WHERE product_code = ?';
					$stmt = $db->prepare($delete);
					$stmt->bind_param('s', $product_code);
				
					if($stmt->execute()) {
						$row = $db->affected_rows;
					} else {
						$error_msg[] = 'DELETE実行エラー[実行SQL]' . $delete;
					}
				}
				//更新の成否
				if (count($error_msg) === 0) {
					print $row . '件更新しました。';
					$db->commit();
				} else {
					print '更新が失敗しました。';
					$db->rollback();
				}
				$stmt->close();
				var_dump($error_msg);
			}

			$db->close();
		?>
		<form method="post">
			<div class="product-data">
				<p>商品データ</p>
				<p>product_id: <?php print $product_id; ?></p>
				<p>product_code: <?php print $product_code; ?></p>
				<p>product_name: <?php print $product_name; ?></p>
				<p>price: <?php print $price; ?></p>
				<p>category_id: <?php print $category_id; ?></p>
			</div>
			<input type="submit" name="insert" value="挿入">
			<input type="submit" name="delete" value="削除">
		</form>
	</body>
</html>