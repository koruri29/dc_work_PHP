<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>TRY49</title>
	</head>
	<body>
		<?php
			output_function();

			output_function_num(10);

			$function_num = make_function_num(10);
			print $function_num;

			function output_function() {
				print '<p>引数：なし、返り値：なしの関数</p>';
			}

			function output_function_num($num) {
				print '<p>引数：' . $num . '返り値：なしの関数</p>';
			}

			function make_function_num($num) {
				$str = '<p>引数：' . $num . '返り値：ありの関数</p>';
				return $str;
			}
		?>
	</body>
</html>