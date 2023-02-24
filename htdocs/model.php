<?php
/**
* DB接続を行いPDOインスタンスを返す
* 
* @return object $pdo
*/
function get_connection() {
	try {
		define('DSN', 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;');
		define('LOGIN_USER', 'bcdhm_omiya_pf0006');
		define('PASSWORD', 'N3p!CxYc');
		$pdo = new PDO(DSN, LOGIN_USER, PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo $e->getMessage();
		exit();
	}
	return $pdo;
}


/**
 *  SQL文を実行・結果を配列で取得する
 * 
 * @param object $pdo
 * @param string $sql 実行されるSQL文章
 * @return array 結果セットの配列
 */
function get_sql_result($pdo, $sql) {
	$data = [];
	if ($result = $pdo->query($sql)) {
		if ($result->rowcount() > 0) {
			while ($row = $result->fetch()) {
				$data[] = $row;
			}
		}
	}
	return $data;
}


/**
 * 全商品の商品名データ取得
 * 
 * @param object
 * @return array
 */
function get_product_list($pdo) {
	$sql = 'SELECT product_name, price FROM product';
	return get_sql_result($pdo, $sql);
}


/**
 * htmlspecialchars（特殊文字の変換）のラッパー関数
 * 
 * @param string
 * @return string
 */
function h($str) {
	return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
}


/**
 * 特殊文字の変換（二次元配対応）
 * 
 * @param array
 * @return array
 */
function h_array($array) {
	foreach ($array as $keys => $values) {
		foreach ($values as $key => $value) {
			$array[$keys][$key] = h($value);
		}
	}
	return $array;
}