<?php

/*-------------------------
 * product.php
 *-------------------------*/
function showProductData() {
    $products = fetchAllProduct();

    foreach ($products as $product) {
        print '<div class="img">';
        print '<table>';
        print '<tr><th>商品ID：</th><td>' . $product['id'] . '</td></tr>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['stock_qty'] . '</td></tr>';
        print '<tr><th>更新日：</th><td>' . $product['created_at'] . '</td></tr>';
        print '</table>';
        print '<img src="../../include/images/' . $product['image_name'] . '">';
        print '</div>';
    } 
}