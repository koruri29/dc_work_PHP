<?php
define('MAX','3'); // 1ページの表示数
 
$customers = array( // 表示データの配列
          array('name' => '佐藤', 'age' => '10'),
          array('name' => '鈴木', 'age' => '15'),
          array('name' => '高橋', 'age' => '20'),
          array('name' => '田中', 'age' => '25'),
          array('name' => '伊藤', 'age' => '30'),
          array('name' => '渡辺', 'age' => '35'),
          array('name' => '山本', 'age' => '40'),
            );
            
$customers_num = count($customers); // トータルデータ件数
 
$max_page = ceil($customers_num / MAX); // トータルページ数

// データ表示、ページネーションを実装
$get = array();
if (isset($_GET['page'])) {
	$get['page'] = htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'); 
}

if (empty($get['page'])) {
	$page = 1;
} else {
	$page = (int) $get['page'];
	if ($page < 1) {
		$page = 1;
	}
}
$page = min($page, $max_page);
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>WORK18</title>
	</head>
	<body>
		<table border="1">
			<thead>
				<tr>
					<th>名前</th>
					<th>年齢</th>
				</tr>
			</thead>
			<tbody>
				<?php for ($i = 0; $i < 3; $i++): ?>
					<tr>
						<?php if (empty($customers[($page - 1) * 3 + $i])) break; ?>
						<td><?php print $customers[($page - 1) * 3 + $i]['name'];?></td>
						<td><?php print $customers[($page - 1) * 3 + $i]['age'];?></td>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>
		<?php for ($i = 1; $i <= $max_page; $i++): ?>
			<?php if ($i === $page): ?>
				<?php print $i; ?>
			<?php else: ?>
				<a href="work18.php?page=<?php print $i; ?>"><?php print $i; ?></a>
			<?php endif; ?>
		<?php endfor; ?>
	</body>
</html>