<?php

/**
 * 配列・文字列にhtmlspecialcharsをかける関数
 * 
 * @param mixed $before サニタイズしたい値
 * @return mixed $after サニタイズ後の値
 */
function sanitize($before) {
	if (is_array($before)) {
		$after =[];
		foreach($before as $key=>$val)
		{
			$after[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
		}
		return $after;
	} else {
		$after = htmlspecialchars($before, ENT_QUOTES, 'UTF-8');
		return $after;
	}
}