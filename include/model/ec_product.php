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
        if ($product == false) break;
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品ID：</th><td>' . $product['product_id'] . '</td></tr>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['stock_qty'] . '点</td></tr>';
        print '<tr><th>更新日：</th><td>' . $product['created_at'] . '</td></tr>';
        print '</table>';
        print '<img src="../../0006/include/images/' . $product['image_name'] . '">';
        print '</div>';
    } 
}


/**
 * 画像と商品データの登録
 * 
 * @return bool 登録が成功すればtrue
 */
function registerProduct(object $pdo): bool {
    global $msg;
    global $error;

    if (! checkImage()) {
        return false;
    }

    if (
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            '../../include/images/' . $_FILES['image']['name']
        )
    ) {
        //
    } else {
        $error = array_merge($error, ['upload_image' => '画像のアップロードに失敗しました。']);
        return false;
    }

    insertImage($pdo);
    $last_insert_id = lastInsertId($pdo);

    insertProduct($pdo, $last_insert_id);

    $msg = array_merge($msg, ['inserted' => '商品の登録が完了しました。']);
    return true;
}

/**
 * 商品登録フォームからpostされた画像のチェック
 * 
 * @return bool エラーがある場合はfalse
 */
function checkImage(): bool {
    global $error;
    if ($_FILES['img']['size'] === 0) {
        $error = array_merge($error, ['img' => '画像が選択されていません。']);
        return false;
    }
    $pathinfo = pathinfo($_FILES['image']['name']);
    $ext = strtolower($pathinfo['extension']);
    if ($ext != 'jpg' && $ext != 'png') {
        $error = array_merge($error, ['ext' => 'jpgまたはpngファイル以外が選択されています。']);
        return false;
    }
    return true;
}


/*-------------------------
 * index.php
 *-------------------------*/
function showPublicProduct(object $pdo) {
    $stmt = fetchPublicProduct($pdo);
    while (true) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product == false) break;
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['stock_qty'] . '点</td></tr>';
        print '</table>';
        print '<img src="../../0006/htdocs/img/' . $product['image_name'] . '">';
        print '<form action="./cart.php" method="post">';
        print '<input type="hidden" value="' . $product['product_id'] . '">';
        //print '<button type="submit">カートに入れる</button>';
        print '<input type="submit" name="submit" value="カートに入れる">';
        print '</form>';
        print '</div>';
    }
}


/*-------------------------
 * cart.php
 *-------------------------*/
