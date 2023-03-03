<?php
function sanitize($array) {
    foreach ($array as $key => $value) {
        $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    return $sanitized;
}