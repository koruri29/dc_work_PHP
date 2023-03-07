<?php

/*-------------------------
 * product.php
 *-------------------------*/
/**
 * 商品管理画面で商品データを一覧表示する
 * 
 * @return void
 */
function showProductData(): void {
    $products = fetchAllProduct();

    foreach ($products as $product) {
        $product = sanitize($product);

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
/**
 * postされた画像のチェック
 * 
 * @param array $file postされた画像データ
 * @return bool エラーがある場合はfalse
 */
function checkImage(array $file): bool {
    global $error;
    if ($file['img']['size'] === 0) {
        $error = array_merge($error, ['img' => '画像が選択されていません。']);
        return false;
    }
    $pathinfo = pathinfo($file['image']['name']);
    $ext = strtolower($pathinfo['extension']);
    if ($ext != 'jpg' && $ext != 'png') {
        $error = array_merge($error, ['ext' => 'jpgまたはpngファイル以外が選択されています。']);
        return false;
    }
}
/**
 * 画像と商品データの登録
 * 
 * @param array $file フォームからpostされた画像情報
 * @param array $post フォームからpostされた商品情報
 * @return bool 登録が成功すればtrue
 */
function registerProduct(array $file, array $post): bool {
    global $msg;
    if (! checkImage($file)) {
        return false;
    }
    //画像データの処理
    $last_insert_id = insertImage($file);
    move_uploaded_file($file['image']['tmp_name'], '../../include/images' . basename($file['image']['name']));

    insertProduct($post, $last_insert_id);

    $msg = array_merge($msg, ['inserted' => '商品の登録が完了しました。']);

}