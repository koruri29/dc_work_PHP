<?php
// Modelを読み込む
require_once 'model.php';


$product_data = [];

define('DSN', 'mysql:host=mysql34.conoha.ne.jp;dbname=bcdhm_omiya_pf0006;');
define('LOGIN_USER', 'bcdhm_omiya_pf0006');
define('PASSWORD', 'N3p!CxYc');
$pdo = new PDO(DSN, LOGIN_USER, PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// $pdo = get_connection();
var_dump($pdo);
$product_data = get_product_list($pdo);
$product_data = h_array($product_data);


// View読み込み
include_once 'view.php';