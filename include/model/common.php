<?php

/**
 * 配列にhtmlspecialcharsをかける関数
 * 
 * @param array $array サニタイズしたい配列
 * @return array サニタイズ後の配列
 */
function sanitize($array) {
    foreach ($array as $key => $value) {
        $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    return $sanitized;
}