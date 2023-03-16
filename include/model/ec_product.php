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

    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<form action="./edit.php" method="post">';
        print '<table>';
        print '<tr><th>商品ID：</th><td>' . $product['product_id'] . '</td></tr>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '</td></tr>';
        print '<tr>';
        print '<th>在庫数：</th>';
        print '<td><input type="text" name="qty" value="' . $product['stock_qty'] . '"></td>';
        print '</tr>';
        print '<tr>';
        print '<th>公開フラグ：</th>';
        print '<td class="display">';
        // print '<input class="radioBtn" type="radio" name="display-flag" value="display" checked>表示する';
        // print '<input class="radioBtn" type="radio" name="display-flag" value="non-display">非表示にする';
        if ($product['public_flag'] == 1) {
            print '公開中です<br>';
        } else {
            print'非公開です<br>';
        }
        print '<input class="display-flag" type="checkbox" name="display-flag">設定を変更';
        print '</td>';
        print '</tr>';
        print '<tr><th>削除：</th><td><input type="checkbox" name="delete"></td></tr>';
        print '<tr><th>更新日：</th><td>' . $product['updated_at'] . '</td></tr>';
        print '<input type="hidden" name="product-id" value="' . $product['product_id'] . '">';
        print '<tr><th><input class="submit" name="submit" type="submit" value="設定を変更する"></th><td></td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '</form>';
        print '</div>';
    } 
}


/**
 * 画像と商品データの登録
 * 
 * @return bool 登録が成功すればtrue
 */
function registerProduct(object $pdo): void {
    global $msg_register;
    global $error;

    validateImage();
    validateRegisteredProduct();

    if (empty($error)) {
        if (
            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                '../../images/' . $_FILES['image']['name']
            )
        ) {
            //
        } else {
            $error = array_merge($error, ['upload_image' => '画像のアップロードに失敗しました。']);
        }
    } else {
        return;
    }

    if (empty($error)) {
        insertImage($pdo);
        $last_insert_id = lastInsertId($pdo);
    } else {
        return;
    }

    if (insertProduct($pdo, $last_insert_id)) {
        //
    } else {
        $error = array_merge($error, ['insert_product' => '商品の登録に失敗しました。']);
        return;
    }

    $msg_register = '商品の登録が完了しました。';
}

/**
 * 商品登録フォームからpostされた画像のチェック
 * 
 * @return bool エラーがある場合はfalse
 */
function validateImage(): bool {
    global $error;
    if ($_FILES['img']['size'] == 0) {
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
 * 商品登録の際のバリデーションチェック
 * 
 * @return bool 未入力や不正な値がなければtrue
 */
function validateProduct(): array {
    $error = array();
    $flag = true;
    
    if (empty($_POST['name'])) {
        $error = array_merge($error, ['name_empty' => '商品名が入力されていません。']);
        $flag = false;
    }
    if ($_POST['price'] === '') {
        $error = array_merge($error, ['price_empty' => '価格が入力されていません。']);
        $flag = false;
    }
    if (! is_numeric($_POST['price'])) {
        $error = array_merge($error, ['price_not_num' => '価格は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['price'] < 0) {
        $error = array_merge($error, ['price_minus' => '価格は正の整数を入力してください。']);
        $flag = false;
    }
    if ($_POST['qty'] === '') {
        $error = array_merge($error, ['qty_empty' => '在庫数が入力されていません。']);
        $flag = false;
    }
    if (! is_numeric($_POST['qty'])) {
        $error = array_merge($error, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['qty'] < 0) {
        $error = array_merge($error, ['qty_minus' => '在庫数は正の整数を入力してください。']);
        $flag = false;
    }
    if ($_POST['public_flag'] === 0 | $_POST['public_flag'] === 1) {
        $error = array_merge($error, ['flag_empty' => '公開ステータスを選択してください']);
        $flag = false;
    }

    if ($flag === true) {
        return $error;
    } else {
        return $error;
    }
}


/*-------------------------
 * index.php
 *-------------------------*/
function showPublicProduct(object $pdo) {
    $stmt = fetchPublicProduct($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['stock_qty'] . '点</td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '">';
        print '<form action="./cart.php" method="post">';
        print '<input type="hidden" name="product_id" value="' . $product['product_id'] . '">';
        //print '<button type="submit">カートに入れる</button>';
        if ($product['stock_qty'] == 0) {
            print '<p class="sold-out">売り切れ</p>';
        } else {
            print '<input type="submit" name="submit" value="カートに入れる">';
        }
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
        print '<tr><th>数量：</th><td><input type="number" name="qty" value="' . $product['product_qty'] . '">点</td></tr>';
        print '<tr><th>小計：</th><td>' . $product['price'] * $product['product_qty'] . '円</td></tr>';
        print '<tr><th>削除</th><td><input type="checkbox" name="delete"></td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '<br>';
        print '<input type="hidden" name="product_id" value="' . $product['product_id'] . '">';
        print '<input type="submit" name="submit" value="数量変更">';
        print '</form>';
        print '</div>';
    }
}


/**
 * 商品一覧画面から「カートに入れる」を押した場合の関数
 * 
 * @param object $pdo
 * @return void
 */
function addToCart(object $pdo): void {
    if (doesExistInCart($pdo)) {
        $qty = getNewQty($pdo, $_POST['product_id']);
        updateQty($pdo, $qty);
    } else {
        newlyAddToCart($pdo);
    }
}


function validateQty($qty) {
    global $error;
    if (preg_match('/^[0-9]+$/', $qty) == 0) {
        $error = array_merge($error, ['character' => '数量は半角数字で入力してください。']);
        return false;
    }
    return true;
}

/*-------------------------
 * thankyou.php
 *-------------------------*/
function proceedSales(object $pdo) {
    $stmt = fetchAllInCart($pdo);
    if ($stmt === false) return;

    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($product === false) return;
        insertSales($pdo, $product);
        changeStock($pdo, $product);
    }
}


function showPurchasedProducts(object $pdo, int $cartId): void {
    $stmt = getSales($pdo, $cartId);

    if ($stmt === false) return;

    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '</td></tr>';
        print '<tr><th>数量：</th><td>' . $product['changed_qty'] . '</td></tr>';
        print '<tr><th>小計：</th><td>' . $product['price'] * $product['changed_qty'] . '</td></tr>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '</table>';
        print '</div>';
    }    
}