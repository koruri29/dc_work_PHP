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
function registerProduct(object $pdo): void {
    global $msg;
    global $error;

    if (! validateImage()) return;
    if (! validateProduct()) return;

    if (
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            '../../include/images/' . $_FILES['image']['name']
        )
    ) {
        //
    } else {
        $error = array_merge($error, ['upload_image' => '画像のアップロードに失敗しました。']);
        return;
    }

    insertImage($pdo);
    $last_insert_id = lastInsertId($pdo);

    insertProduct($pdo, $last_insert_id);

    $msg = array_merge($msg, ['inserted' => '商品の登録が完了しました。']);
    return;
}

/**
 * 商品登録フォームからpostされた画像のチェック
 * 
 * @return bool エラーがある場合はfalse
 */
function validateImage(): bool {
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

/**
 * フォームから投稿された商品情報のバリデーションチェック
 * 
 * @return bool 未入力や不正な値がなければtrue
 */
function validateProduct(): bool {
    global $error;
    $flag = true;
    
    if (empty($_POST['name'])) {
        $error = array_merge($error, ['name_empty' => '商品名が入力されていません。']);
        $flag = false;
    }
    if (empty($_POST['price'])) {
        $error = array_merge($error, ['price_empty' => '価格が入力されていません。']);
        $flag = false;
    }
    if (! is_int($_POST['price'])) {
        $error = array_merge($error, ['price_not_num' => '価格は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['price'] < 0) {
        $error = array_merge($error, ['price_minus' => '価格は正の整数を入力してください。']);
        $flag = false;
    }
    if (empty($_POST['qty'])) {
        $error = array_merge($error, ['qty_empty' => '在庫数が入力されていません。']);
        $flag = false;
    }
    if (! is_int($_POST['qty'])) {
        $error = array_merge($error, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['qty'] < 0) {
        $error = array_merge($error, ['qty_minus' => '在庫数は正の整数を入力してください。']);
        $flag = false;
    }
    if (empty($_POST['public_flag'])) {
        $error = array_merge($error, ['flag_empty' => '公開ステータスが入力されていません']);
        $flag = false;
    }

    if ($flag === true) {
        return true;
    } else {
        return false;
    }
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
        print '<input type="hidden" name="product_id" value="' . $product['product_id'] . '">';
        //print '<button type="submit">カートに入れる</button>';
        print '<input type="submit" name="submit" value="カートに入れる">';
        print '</form>';
        print '</div>';
    }
}


/*-------------------------
 * cart.php
 *-------------------------*/
function showProductInCart($pdo): void {
    $stmt = fetchProductsInCart($pdo);
    while (true) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product == false) break;
        $product = sanitize($product);

        print '<div class="item">';
        print '<form action="./cart.php" method="post">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>数量：</th><td><input type="number" name="qty[]" value="' . $product['product_qty'] . '">点</td></tr>';
        print '<tr><th>小計：</th><td>' . $product['price'] * $product['product_qty'] . '円</td></tr>';
        print '<tr><th>削除</th><td><input type="checkbox" name="delete[]"></td></tr>';
        print '</table>';
        print '<img src="../../0006/htdocs/img/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '<br>';
        print '<input type="submit" name="submit" value="数量変更">';
        print '</form>';
        print '</div>';
    }
}
/**
 * カートの商品数を変更する際のバリデーション
 * 
 * @param object $pdo
 * @return bool 
 */
function validateQty(object $pdo): bool {
    $products = sanitize($_POST);
    foreach ($products as $product) {
        FetchOneInCart($pdo)
        if (preg_match('/\A[0-9]+\z/', ));
    }
}