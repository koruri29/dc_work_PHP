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
        print '<td><input type="text" name="qty" value="' . $product['qty'] . '"></td>';
        print '</tr>';
        print '<tr>';
        print '<th>公開フラグ：</th>';
        print '<td class="display">';
        // print '<input class="radioBtn" type="radio" name="display-flag" value="display" checked>表示する';
        // print '<input class="radioBtn" type="radio" name="display-flag" value="non-display">非表示にする';
        if ($product['public_flag'] == 1) {
            print '<span class="displayed">公開中です</span><br>';
        } else {
            print '<span class="non-displayed">非公開です</span><br>';
        }
        print '<input class="display-flag" type="checkbox" name="display-flag">設定を変更';
        print '</td>';
        print '</tr>';
        print '<tr><th>削除：</th><td><input type="checkbox" name="delete"></td></tr>';
        // print '<tr><th>更新日：</th><td>' . $product['updated_at'] . '</td></tr>';
        print '<input type="hidden" name="product-id" value="' . $product['product_id'] . '">';
        print '<tr><th><input class="submit" name="submit" type="submit" value="設定を変更する"></th><td></td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '</form>';
        print '</div>';
    } 
}

function proceedUpdateProduct(object $pdo): void {
    if (! validateUpdatedProduct()) return;

    updateStock($pdo);
    if ($_POST['display-flag']) {
        updateFlag($pdo);
        return;
    }
    if ($_POST['delete']) {
        deleteProduct($pdo);
        return;
    }
}
/**
 * 画像と商品データの登録
 * 
 * @param object $pdo
 * @return void
 */
function registerProduct(object $pdo): void {
    global $msg_register;
    global $error_register;

    validateImage();
    validateProduct();

    if (empty($error_register)) {
        if (
            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                '../../images/' . $_FILES['image']['name']
            )
        ) {
            //
        } else {
            $error_register = array_merge($error_register, ['upload_image' => '画像のアップロードに失敗しました。']);
            return;
        }
    } else {
        return;
    }

    if (empty($error_register)) {
        insertImage($pdo);
        $last_insert_id = lastInsertId($pdo);
    } else {
        return;
    }

    if (insertProduct($pdo, $last_insert_id)) {
        //
    } else {
        $error_register = array_merge($error_register, ['insert_product' => '商品の登録に失敗しました。']);
        return;
    }

    $msg_register = '商品の登録が完了しました。';
}

/**
 * 商品登録フォームからpostされた画像のチェック
 * 
 * @return void
 */
function validateImage(): void {
    global $error_register;
    if ($_FILES['image']['size'] == 0) {
        $error_register = array_merge($error_register, ['img' => '画像が選択されていません。']);
        return;
    }
    $pathinfo = pathinfo($_FILES['image']['name']);
    $ext = strtolower($pathinfo['extension']);
    if ($ext != 'jpg' && $ext != 'png') {
        $error_register = array_merge($error_register, ['ext' => 'jpgまたはpngファイル以外が選択されています。']);
        return;
    }
}

/**
 * 商品登録の際のバリデーションチェック
 * 
 * @return bool 未入力や不正な値がなければtrue
 */
function validateProduct(): bool {
    global $error_register;
    $flag = true;
    
    if (empty($_POST['name'])) {
        $error_register = array_merge($error_register, ['name_empty' => '商品名が入力されていません。']);
        $flag = false;
    }
    if ($_POST['price'] === '') {
        $error_register = array_merge($error_register, ['price_empty' => '価格が入力されていません。']);
        $flag = false;
    }
    if (! is_numeric($_POST['price'])) {
        $error_register = array_merge($error_register, ['price_not_num' => '価格は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['price'] < 0) {
        $error_register = array_merge($error_register, ['price_minus' => '価格は正の整数を入力してください。']);
        $flag = false;
    }
    if ($_POST['qty'] === '') {
        $error_register = array_merge($error_register, ['qty_empty' => '在庫数が入力されていません。']);
        $flag = false;
    }
    if (! is_numeric($_POST['qty'])) {
        $error_register = array_merge($error_register, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['qty'] < 0) {
        $error_register = array_merge($error_register, ['qty_minus' => '在庫数は正の整数を入力してください。']);
        $flag = false;
    }
    if ($_POST['public_flag'] === 0 | $_POST['public_flag'] === 1) {
        $error_register = array_merge($error_register, ['flag_empty' => '公開ステータスを選択してください']);
        $flag = false;
    }

    if ($flag === true) {
        return true;
    } else {
        return false;
    }
}


/**
 * 商品登録の際のバリデーションチェック
 * 
 * @return bool 未入力や不正な値がなければtrue
 */
function validateUpdatedProduct(): bool {
    global $error_update;

    $flag = true;
    
    if ($_POST['qty'] === '') {
        $error_update = array_merge($error_update, ['qty_empty' => '在庫数が入力されていません。']);
        $flag = false;
    }
    if (! is_numeric($_POST['qty'])) {
        $error_update = array_merge($error_update, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
        $flag = false;
    }
    if ($_POST['qty'] < 0) {
        $error_update = array_merge($error_update, ['qty_minus' => '在庫数は正の整数を入力してください。']);
        $flag = false;
    }
    if ($_POST['public_flag'] === 0 | $_POST['public_flag'] === 1) {
        $error_update = array_merge($error_update, ['flag_empty' => '公開ステータスを選択してください']);
        $flag = false;
    }

    if ($flag === true) {
        return true;
    } else {
        return false;
    }
}


/*-------------------------
 * product.php
 *-------------------------*/
function showPublicProduct(object $pdo) {
    $stmt = fetchPublicProduct($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['qty'] . '点</td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '">';
        print '<form action="./product.php" method="post">';
        print '<input type="hidden" name="product-id" value="' . $product['product_id'] . '">';
        //print '<button type="submit">カートに入れる</button>';
        if ($product['qty'] == 0) {
            print '<p class="sold-out">売り切れ</p>';
        } else {
            print '<input type="submit" name="submit" value="カートに入れる">';
        }
        print '</form>';
        print '</div>';
    }
}


function countTotalProduct(object $pdo) : int {
    $stmt = fetchAllInCart($pdo);
    $total = 0;
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $total += $product['qty'];
    }
    return $total;
}

/*-------------------------
 * cart.php
 *-------------------------*/
function showProductInCart($pdo): void {
    $stmt = fetchProductsInCart($pdo);
    $i = 0;
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>数量：</th><td><input type="number" name="qty' . $i . '" value="' . $product['qty'] . '">点</td></tr>';
        print '<tr><th>小計：</th><td>' . $product['price'] * $product['qty'] . '円</td></tr>';
        print '<tr><th>削除</th><td><input type="checkbox" name="delete' . $i . '"></td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '<br>';
        print '<input type="hidden" name="product-id' . $i . '" value="' . $product['product_id'] . '">';
        print '</div>';
        
        $i++;
    }
}


function isStockAvailable(object $pdo) {
    global $error;
    $stmt = fetchAllInCart($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = fetchOneFromProduct($pdo, $product['product_id']);
        if ($product['qty'] == 0 ) {
            $error = array_merge($error, ['stock' => "カート内の{$product['product_name']}が売り切れています。"]);
        } 
    }
}


/**
 * 合計金額の計算
 * 
 * @param object $pdo
 * @return int $total 合計金額
 */
function calcTotal(object $pdo, callable $funk): int {
    $total = 0;
    $stmt = $funk($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qty = $product['qty'];
        $product = fetchOneFromProduct($pdo, $product['product_id']);
        $total += $product['price'] * $qty;
    }
    return $total;
}
/**
 * 商品一覧画面から「カートに入れる」を押した場合の関数
 * 
 * @param object $pdo
 * @return void
 */
function addToCart(object $pdo): void {
    if (doesExistInCart($pdo)) {
        $qty = getNewQty($pdo, $_POST['product-id']);
        updateQty($pdo, $_POST['product-id'], $qty);
    } else {
        newlyAddToCart($pdo);
    }
}


function validateQty() {
    global $error;
    global $product_num;

    $flag = true;
    
    for ($i = 0; $i < $product_num; $i++) {
        if ($_POST['qty' . $i] === '') {
            $error = array_merge($error, ['qty_empty' => '数量が入力されていません。']);
            $flag = false;
        }
        if (! is_numeric($_POST['qty' . $i])) {
            $error = array_merge($error, ['qty_not_num' => '数量は半角数字で入力してください。']);
            $flag = false;
        }
        if ($_POST['qty' . $i] < 0) {
            $error = array_merge($error, ['qty_minus' => '数量は正の整数で入力してください。']);
            $flag = false;
        }
    }

    if ($flag === true) {
        return true;
    } else {
        return false;
    }
}

/*-------------------------
 * thankyou.php
 *-------------------------*/
function proceedSales(object $pdo, object $stmt) {
    lockTable($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        insertSales($pdo, $product);
        changeStock($pdo, $product);
    }
    unlockTable($pdo);
}


/**
 * 
 * 
 * @param $stmt getSalesの返り値
 */
function showPurchasedProducts(object $pdo, object $stmt): void {
    if ($stmt === false) return;

    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '</td></tr>';
        print '<tr><th>数量：</th><td>' . $product['qty'] . '</td></tr>';
        print '<tr><th>小計：</th><td>' . $product['price'] * $product['qty'] . '</td></tr>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '</table>';
        print '</div>';
    }    
}