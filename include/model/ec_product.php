<?php

/*-------------------------
 * edit.php
 *-------------------------*/
/**
 * 商品管理画面で商品データを一覧表示する
 * 
 * @param object $pdo
 * @return void
 */
function showProductData(object $pdo): void {
    $stmt = fetchAllProduct($pdo);
    $i = 0;

    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品ID：</th><td>' . $product['product_id'] . '</td></tr>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '</td></tr>';
        print '<tr>';
        print '<th>在庫数：</th>';
        print '<td><input class="qty" type="text" name="qty' . $i . '" value="' . $product['qty'] . '"></td>';
        print '</tr>';
        print '<tr>';
        print '<th>公開フラグ：</th>';
        print '<td class="display">';
        if ($product['public_flag'] == 1) {
            print '<span class="displayed">公開中です</span><br>';
        } else {
            print '<span class="non-displayed">非公開です</span><br>';
        }
        print '<input class="public-flag" type="checkbox" name="display-flag' . $i . '">設定を変更';
        print '</td>';
        print '</tr>';
        print '<tr><th>削除：</th><td><input type="checkbox" name="delete' . $i . '"></td></tr>';
        print '<tr><th>更新日：</th><td>' . $product['updated_at'] . '</td></tr>';
        print '<input type="hidden" name="product-id' . $i . '" value="' . $product['product_id'] . '">';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '</div>';

        $i++;
    } 
}


/**
 * 商品管理画面での更新機能をまとめた関数
 * 
 * @param object $pdo
 * @return void
 */
function proceedUpdateProduct(object $pdo): void {
    for ($i = 0; $i <$_POST['product-num']; $i++) {
        if (! validateUpdatedProduct($_POST['qty' . $i])) return;

        $rec = fetchOneFromProduct($pdo, $_POST['product-id' . $i]);
        $current_qty = $rec['qty'];
        if ($_POST['qty' . $i] != $current_qty){
            updateStock($pdo, $_POST['product-id' . $i], $_POST['qty' . $i]);
        }
        if ($_POST['display-flag'. $i]) {
            updateFlag($pdo, $_POST['product-id' . $i]);
        }
        if ($_POST['delete' . $i]) {
            deleteProduct($pdo, $_POST['product-id' . $i]);
        }
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

    // validateProduct();
    // validateImage();

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

    insertImage($pdo);
    $last_insert_id = lastInsertId($pdo);

    insertProduct($pdo, $last_insert_id);
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
 * @return void
 */
function validateProduct(): void {
    global $error_register;
    
    if (empty($_POST['name'])) {
        $error_register = array_merge($error_register, ['name_empty' => '商品名が入力されていません。']);
    }
    if ($_POST['price'] === '') {
        $error_register = array_merge($error_register, ['price_empty' => '価格が入力されていません。']);
    }
    if (! is_numeric($_POST['price'])) {
        $error_register = array_merge($error_register, ['price_not_num' => '価格は半角数字で入力してください。']);
    }
    if ($_POST['price'] < 0) {
        $error_register = array_merge($error_register, ['price_minus' => '価格は正の整数を入力してください。']);
    }
    if ($_POST['qty'] === '') {
        $error_register = array_merge($error_register, ['qty_empty' => '在庫数が入力されていません。']);
    }
    if (! is_numeric($_POST['qty'])) {
        $error_register = array_merge($error_register, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
    }
    if ($_POST['qty'] < 0) {
        $error_register = array_merge($error_register, ['qty_minus' => '在庫数は正の整数を入力してください。']);
    }
}


/**
 * 商品登録の際のバリデーションチェック
 * 
 * @return bool 未入力や不正な値がなければtrue
 */
function validateUpdatedProduct($qty): bool {
    global $error_update;

    $flag = true;
    
    if ($qty === '') {
        $error_update = array_merge($error_update, ['qty_empty' => '在庫数が入力されていません。']);
        $flag = false;
    }
    if (! is_numeric($qty)) {
        $error_update = array_merge($error_update, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
        $flag = false;
    }
    if ($qty < 0) {
        $error_update = array_merge($error_update, ['qty_minus' => '在庫数は正の整数を入力してください。']);
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
/**
 * 商品一覧画面で、公開中の商品すべてを表示する関数
 * 
 * @param object $stmt 商品一覧データまたは検索結果
 * @return void
 */
function showPublicProduct(object $stmt): void {
    global $error;
    $flag = false;

    if ($stmt === null) {
        $error = '検索結果は0件です。';
        return;
    }
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $flag = true;
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>在庫数：</th><td>' . $product['qty'] . '点</td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '">';
        //print '<button type="submit">カートに入れる</button>';
        if ($product['qty'] == 0) {
            print '<p class="sold-out">売り切れ</p>';
        } else {
        print '<form action="./product.php" method="post">';
        print '<input type="hidden" name="product-id" value="' . $product['product_id'] . '">';
        print '<input class="submit" type="submit" name="submit" value="カートに入れる">';
        print '</form>';
    }
        print '</div>';
    }
    if (! $flag) $error = '検索結果は0件です。';
}

/**
 * カート内の商品点数の合計を表示する
 * 
 * ヘッダーのナビゲーションの「カートを見る」の部分に表示する
 * 
 * @param object $pdo
 * @return int $total カート内の商品点数の合計
 */
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
/**
 * カート内商品を一覧化する関数
 * 
 * @param object $pdo
 * @return void
 */
function showProductInCart(object $pdo): void {
    $stmt = fetchProductsInCart($pdo);
    $i = 0;
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = sanitize($product);

        print '<div class="item">';
        print '<table>';
        print '<tr><th>商品名：</th><td>' . $product['product_name'] . '</td></tr>';
        print '<tr><th>価格：</th><td>' . $product['price'] . '円</td></tr>';
        print '<tr><th>数量：</th><td><input class="qty" type="number" name="qty' . $i . '" value="' . $product['qty'] . '">点</td></tr>';
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


/**
 * カート内の商品が売り切れたときのエラー表示
 * 
 * @param object $pdo
 * @return void
 */
function isStockAvailable(object $pdo): void {
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
 * @param calldable $funk データベースから商品郡を取得する関数
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


/** 
 * カート内商品の数量変更の際のバリデーション
 * 
 * @return bool
*/
function validateQty(): bool {
    global $error;
    global $product_num;

    for ($i = 0; $i < $product_num; $i++) {
        if ($_POST['qty' . $i] === '') {
            $error = array_merge($error, ['qty_empty' => '数量が入力されていません。']);
        }
        if (! is_numeric($_POST['qty' . $i])) {
            $error = array_merge($error, ['qty_not_num' => '数量は半角数字で入力してください。']);
        }
        if ($_POST['qty' . $i] < 0) {
            $error = array_merge($error, ['qty_minus' => '数量は正の整数で入力してください。']);
        }
    }

    if (count($error) === 0) {
        return true;
    } else {
        return false;
    }
}

/*-------------------------
 * thankyou.php
 *-------------------------*/
/**
 * 商品決済を進める関数
 * 
 * @param object $pdo
 * @param object $stmt カート内商品テーブルのデータ
 * @return void
 */
function proceedSales(object $pdo, object $stmt): void {
    lockTable($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        insertSales($pdo, $product);
        changeStock($pdo, $product);
    }
    unlockTable($pdo);
}


/**
 * 購入完了した商品の一覧表示
 * 
 * @param $stmt getSalesの返り値
 * @return void
 */
function showPurchasedProducts(object $stmt): void {
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