<?php

/*-------------------------
 * product.php
 *-------------------------*/
/**
 * 商品管理画面で商品データを一覧表示する
 * 
 * @return void
 */
function showProductData(object $pdo): void {
    $stmt = fetchAllProduct($pdo);

    while (true) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product == false) {
            break;
        }
        $product = sanitize($product);
        print '<div class="img">';
        print '<table>';
        print '<tr><th>商品ID：</th><td>' . $product['product_id'] . '</td></tr>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['stock_qty'] . '</td></tr>';
        print '<tr><th>更新日：</th><td>' . $product['created_at'] . '</td></tr>';
        print '</table>';
        print '<img src="../../0006/htdocs/img/' . $product['image_name'] . '">';
        print '</div>';
    } 
}


/**
 * 画像と商品データの登録
 * 
 * @param array $file フォームからpostされた画像情報
 * @param array $post フォームからpostされた商品情報
 * @return bool 登録が成功すればtrue
 */
function registerProduct(object $pdo, array $file, array $post): bool {
    global $msg;
    global $error;

    if (! checkImage($file)) {
        return false;
    }

    if (
        move_uploaded_file(
            $file['image']['tmp_name'],
            '../../include/images/' . $file['image']['name']
        )
    ) {
        //
    } else {
        $error = array_merge($error, ['upload_image' => '画像のアップロードに失敗しました。']);
        return false;
    }

    insertImage($pdo, $file);
    $last_insert_id = lastInsertId($pdo);

    insertProduct($pdo, $post, $last_insert_id);

    $msg = array_merge($msg, ['inserted' => '商品の登録が完了しました。']);
    return true;
}

/**
 * 商品登録フォームからpostされた画像のチェック
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
    return true;
}