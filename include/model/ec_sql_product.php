<?php

/*-------------------------
 * product.php
 *-------------------------*/
/**
 * データベースからすべての商品データを取得
 * 
 * @return array $products 商品データ
 */
function fetchAllProduct() {
    $pdo = getDb();

    $sql = 'SELECT * FROM EC_product p LEFT JOIN EC_image i ON p.image_id = i.image_id WHERE 1;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $products = $stmt->fetch(PDO::FETCH_ASSOC);
    return $products;
}