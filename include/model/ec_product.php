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

    validateProduct();
    validateImage();

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
    if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png') {
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
    $pattern = '/^[0-9]*[\.][0-9]*/';
    
    if (empty($_POST['name'])) {
        $error_register = array_merge($error_register, ['name_empty' => '商品名が入力されていません。']);
    }
    if ($_POST['price'] === '') {
        $error_register = array_merge($error_register, ['price_empty' => '価格が入力されていません。']);
    }
    if (! is_numeric($_POST['price'])) {
        $error_register = array_merge($error_register, ['price_not_num' => '価格は半角数字で入力してください。']);
    }
    if ($_POST['price'] < 0 || preg_match($pattern, $_POST['price'])) {
        $error_register = array_merge($error_register, ['price_minus' => '価格は正の整数を入力してください。']);
    }
    if ($_POST['qty'] === '') {
        $error_register = array_merge($error_register, ['qty_empty' => '在庫数が入力されていません。']);
    }
    if (! is_numeric($_POST['qty'])) {
        $error_register = array_merge($error_register, ['qty_not_num' => '在庫数は半角数字で入力してください。']);
    }
    if ($_POST['qty'] < 0 || preg_match($pattern, $_POST['qty'])) {
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
        if ($product['qty'] == 0) {
            print '<p class="sold-out">売り切れ</p>';
        } else {
            print '<form class="cart-in-form" name="cartInForm" action="./product.php" method="post">';
            print '<input type="hidden" name="product-id" value="' . $product['product_id'] . '">';
            print '<input type="hidden" name="cart-in" value="on">';
            print '<input class="cart-in-btn" type="submit" name="send" value="カートに入れる">';
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
        print '<tr><th>数量：</th><td><input class="qty" class="qty" type="number" name="qty' . $i . '" value="' . $product['qty'] . '">点</td></tr>';
        print '<tr><th>小計：</th><td>' . $product['price'] * $product['qty'] . '円</td></tr>';
        print '<tr><th>削除</th><td><input class="delete" type="checkbox" name="delete' . $i . '"></td></tr>';
        print '</table>';
        print '<img src="../../0006/images/' . $product['image_name'] . '" alt="' . $product['image_name'] . '">';
        print '<br>';
        print '<input type="hidden" name="product-id' . $i . '" value="' . $product['product_id'] . '">';
        print '</div>';
        
        $i++;
    }
}


function doesShowPurchaseButton(object $pdo): bool {
    global $product_num;
    $stmt = fetchAllInCart($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($product['qty'] == 0) {
            return false;
        }
    }
    for ($i =0; $i < $product_num; $i++) {
        if (isset($error['stock' . $i])) {//カート内商品が売り切れていたら、購入ボタンを表示しない
            return false;
        }
    }
    return true;
}


/**
 * カート内の商品の数量変更の関数
 * 
 * @param object $pdo
 * @return void
 */
function changeQtyInCart(object $pdo, int $id): void {
    for ($i = 0; $i < $_POST['product-num']; $i++) {
        $rec = fetchOneInCart($pdo, $id);
        $current_qty = $rec['qty'];

        if ($_POST['qty' . $i] == $current_qty) {  
            //
        } else {
            $qty = getNewQty($pdo, $_POST['product-id' . $i], $_POST['qty' . $i]);
            updateQty($pdo, $_POST['product-id' . $i], $qty);
        }
    }
}

/**
 * カート内の商品を削除する
 * 
 * @param object $pdo
 * @return void
 */
function deleteFromCart(object $pdo) {
    for ($i = 0; $i < $_POST['product-num']; $i++) {
        if (($_POST['delete' . $i])) {
            deleteProductInCart($pdo, $_POST['product-id' . $i]);
        }
    }
}


/**
 * カート内商品テーブルと在庫数を比べて、適切な数量を設定する
 * 
 * @param object $pdo
 * @param int $id 商品ID
 * @param int|null $posted_qty 入力された数量。入力がなければnull
 * @return int $changed_qty 変更したい数量
 */
function getNewQty(object $pdo, int $id, $posted_qty = null): int {
    $product = fetchOneInCart($pdo, $id);
    $current_qty = $product['qty'];
   
    $product = fetchOneFromProduct($pdo, $id);
    $max_qty = $product['qty'];
    if ($posted_qty !== null) {
        if ($posted_qty >= $max_qty) {
            $changed_qty = $max_qty;
        } else {
            $changed_qty = $posted_qty;
        }
        return $changed_qty;
    }

    if ($current_qty + 1 >= $max_qty) {
        $changed_qty = $max_qty;
    } else {
        $changed_qty = $current_qty + 1;
    }
    return $changed_qty;
}


/**
 * カート内の商品が売り切れたときのエラー表示
 * 
 * @param object $pdo
 * @return void
 */
function isStockAvailable(object $pdo): void {
    global $error;
    $i = 0;
    $stmt = fetchAllInCart($pdo);
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = fetchOneFromProduct($pdo, $product['product_id']);
        if ($product['qty'] == 0) {
            $error = array_merge($error, ['stock' . $i => "カート内の{$product['product_name']}が売り切れています。"]);
        }
        $i++;
    }
}


/**
 * 合計金額の計算
 * 
 * @param object $pdo
 * @param calldable $func データベースから商品郡を取得する関数
 * @return int $total 合計金額
 */
function calcTotal(object $pdo, callable $func): int {
    $total = 0;
    $stmt = $func($pdo);
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
 * 「カートに入れる」ボタン押下時、商品がすでにカートに入っているか判断
 * 
 * 該当の商品は$_POST['product-id']で受けることを想定
 * 
 * @param object $pdo
 * @return bool 指定の商品がすでにカートに入っていればtrue
 */
function doesExistInCart(object $pdo): bool {
    $product = FetchOneInCart($pdo, $_POST['product-id']);
    if ($product['product_id'] !== null) {
        return true;
    } else {
        return false;
    }
}


/** 
 * カート内商品の数量変更の際のバリデーション
 * 
 * @param $qty 確認する値（正の整数を想定）
 * @return bool
*/
function validateQty($qty): bool {
    global $error;
    global $product_num;
    $pattern = '/^[0-9]*[\.][0-9]*/';

        if ($qty === '') {
            $error = array_merge($error, ['qty_empty' => '数量が入力されていません。']);
        }
        if (! is_numeric($qty)) {
            $error = array_merge($error, ['qty_not_num' => '数量は半角数字で入力してください。']);
        }
        if ($qty < 1 || preg_match($pattern, $qty)) {
            $error = array_merge($error, ['qty_minus' => '数量は正の整数で入力してください。']);
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