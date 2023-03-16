<?php

function getSqlResult(object $pdo, string $sql, mixed ...$args): array {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rec;
}


/*-------------------------
 * common
 *-------------------------*/
/**
 * LAST_INSERT_IDを取得する
 * 
 * @return int LAST_INSERT_ID
 */
function lastInsertId(object $pdo): int {
    $sql = 'SELECT LAST_INSERT_ID()';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_insert_id = $rec['LAST_INSERT_ID()'];
    return $last_insert_id;
}

/*-------------------------
 * register.php
 *-------------------------*/
/**
 * ユーザー登録時、すでに同じユーザー名が登録されていないかチェック
 * 
 * @param object $pdo
 * @param string $userName
 * @return bool 重複があればtrue
 */
function isExistingUserName(object $pdo): bool {
    $sql = 'SELECT COUNT(*) AS cnt FROM EC_user WHERE user_name = :name;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $_POST['user-name']);
    $stmt->execute();
    $cnt = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cnt['cnt'] > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * データベースへユーザーを登録する関数
 * 
 * @param object $pdo
 * @param string $userName
 * @param string $password
 * @return bool 登録が成功すればtrue
 */
function insertUser(object $pdo): bool {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();
    
        $sql = 'INSERT INTO EC_user (user_name, password, created_at, updated_at) VALUES(:name, :password, :created_at, :updated_at);';
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':name', $_POST['user-name']);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/*-------------------------
 * login.php
 *-------------------------*/
/**
 * データベースからユーザー情報を取得する関数
 * 
 * @return array|bool データがあれば配列。なければfalse
 */
function fetchUser(object $pdo) {
    $sql = 'SELECT * FROM EC_user WHERE user_name = :name;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $_POST['user-name']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}


/**
 * カートIDを作成
 * 
 * @param object $pdo
 * 
 */
function createCart(object $pdo): object {
    try {
        $pdo->beginTransaction();

        $sql = <<<SQL
            INSERT INTO
                EC_cart (
                    user_id,
                    created_at,
                    updated_at
            ) VALUES (
                :user_id,
                :created_at,
                :updated_at
            );
        SQL;
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();
        
        $pdo->commit();
        return $stmt;
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/*-------------------------
 * product.php
 *-------------------------*/
/**
 * データベースからすべての商品データを取得
 * 
 * @return array $products 商品データ
 */
function fetchAllProduct(object $pdo): object {
    // $pdo = getDb();

    $sql = 'SELECT * FROM EC_product p LEFT JOIN EC_image i ON p.image_id = i.image_id WHERE 1 = 1;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt;
}

/**
 * postされた画像ファイルをデータベースへ挿入
 * 
 * @return 
 */
function insertImage(object $pdo): void {
    try {
        $pdo->beginTransaction();

        $sql = <<<SQL
            INSERT INTO
                EC_image (
                    image_name,
                    created_at,
                    updated_at
                ) VALUES (
                    :name,
                    :created_at,
                    :updated_at
                );
        SQL;

        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':name', $_FILES['image']['name']);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);

        $stmt->execute();
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * postされた商品情報をデータベースに挿入する
 * 
 * @param int $last_insert_id 直前にEC_imageへ挿入された画像データのID
 * @return bool 挿入が成功すればtrue
 */
function insertProduct(object $pdo, int $last_insert_id):bool {
    global $msg_register;

    try {
        $pdo->beginTransaction();

        $sql = <<<SQL
            INSERT INTO
            EC_product (
                product_name,
                price,
                stock_qty,
                image_id,
                public_flag,
                created_at,
                updated_at
            ) VALUES (
                :name,
                :price,
                :qty,
                :image_id,
                :flag,
                :created_at,
                :updated_at
            );
        SQL;
        
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':name', $_POST['name']);
        $stmt->bindValue(':price', $_POST['price']);
        $stmt->bindValue(':qty', $_POST['qty']);
        $stmt->bindValue(':image_id', $last_insert_id);
        $stmt->bindValue(':flag', $_POST['public_flag'], PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/**
 * 商品管理画面で、在庫数を変更する
 * 
 * @param object $pdo
 * @return void
 */
function updateStock(object $pdo) {
    global $msg_update;
    $sql = <<<SQL
        UPDATE
            EC_product
        SET
            stock_qty = :qty,
            updated_at = :updated_at
        WHERE
            product_id = :id
    SQL;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':qty', $_POST['qty']);
        $stmt->bindValue(':updated_at', $date);
        $stmt->bindValue(':id', $_POST['product-id']);
        $stmt->execute();
        
        $pdo->commit();
        
        if ($stmt->rowCount() > 0) {            
            $msg_update = '在庫数を更新しました。';
        }
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * 商品管理画面で、公開フラグを変更する
 * 
 * @param object $pdo
 * @return void
 */
function updateFlag(object $pdo) {
    global $msg_update;
    $product = fetchOneFromProduct($pdo, $_POST['product-id']);
    $flag = $product['public_flag'] === 1 ? 0 : 1;
    $date = date('Y-m-d');

    $sql = <<<SQL
        UPDATE
            EC_product
        SET
            public_flag = :flag,
            updated_at = :updated_at
        WHERE
            product_id = :id
    SQL;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':flag', $flag);
        $stmt->bindValue(':updated_at', $date);
        $stmt->bindValue(':id', $_POST['product-id']);
        $stmt->execute();
        
        $pdo->commit();

        if ($stmt->rowCount() > 0) {            
            if ($flag === 0) {
                $msg_update = '非公開に変更しました。';
                } else {
                $msg_update = '公開に変更しました。';
                }
        }
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * 商品管理画面で、指定の商品を削除する
 * 
 * @param object $pdo
 * @return void
 */
function deleteProduct(object $pdo) {
    global $msg_update;
    $sql = 'DELETE FROM EC_product WHERE product_id = :id;';
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_POST['product-id']);
        $stmt->execute();
        
        $pdo->commit();

        if ($stmt->rowCount() > 0) {            
            $msg_update = '商品を削除しました。';
        }
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * 公開フラグ情報を取得する
 * 
 * @param $pdo
 * @param int $id 商品ID
 * @return int 公開フラグ情報（1は公開、0は非公開）
 */
function getPublicFlag(object $pdo, int $id): int {
    $sql = 'SELECT public_flag FROM EC_product WHERE product_id = :id;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    return $rec['public_flag'];
}

/*-------------------------
 * index.php
 *-------------------------*/
/**
 * データベースから公開フラグが「公開」の商品情報を取得する
 * 
 * @param $pdo
 * @return object $stmt 公開フラグが「公開」の商品情報一式
 */
function fetchPublicProduct(object $pdo): object {
    $sql = <<<SQL
        SELECT * 
        FROM EC_product p 
        LEFT JOIN EC_image i 
        ON p.image_id = i.image_id
        WHERE p.public_flag = 1;
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt;
}


/*-------------------------
* cart.php
*-------------------------*/
/**
 * カート内商品詳細（EC_cart_detail）の商品データを取得
 * 
 * @param object $pdo
 * @return object $stmt カート内の商品の情報
 */
function fetchProductsInCart(object $pdo): object {
    $sql = <<<SQL
        SELECT *
        FROM EC_cart_detail d
        JOIN EC_product p ON d.product_id = p.product_id
        JOIN EC_image i ON p.image_id = i.image_id
        WHERE d.cart_id = :id;
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['cart_id']);
    $stmt->execute();

    return $stmt;
}


function changeQtyInCart(object $pdo): void {
    $rec = fetchOneInCart($pdo,$_POST['product_id']);
    $current_qty = $rec['product_qty'];

    if (! empty($_POST['delete'])) {
        deleteProductInCart($pdo);
        return;
    }
    if ($_POST['qty'] === $current_qty) {
        //
    } else {
        $qty = getNewQty($pdo, $_POST['product_id'], $_POST['qty']);
        updateQty($pdo, $qty);
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
    $current_qty = $product['product_qty'];
   
    $product = fetchOneFromProduct($pdo, $id);
    $max_qty = $product['stock_qty'];

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

function deleteProductInCart(object $pdo): void {
    try {
        $pdo->beginTransaction();
        $sql = <<<SQL
            DELETE FROM
                EC_cart_detail
            WHERE
                cart_id = :cart_id
            AND
                product_id = :product_id;
        SQL;
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $_POST['product_id']);
        $stmt->execute();
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/**
 * 「カートに入れる」ボタン押下時、商品がすでにカートに入っているか判断
 * 
 * 該当の商品は$_POST['product_id']で受けることを想定
 * 
 * @param object $pdo
 * @return bool 指定の商品がすでにカートに入っていればtrue
 */
function doesExistInCart(object $pdo): bool {
    $product = FetchOneInCart($pdo, $_POST['product_id']);
    if ($product['product_id'] !== null) {
        return true;
    } else {
        return false;
    }
}

/**
 * カートにない商品をカートに入れる場合
 * 
 * @param object $pdo
 * @return void
 */
function newlyAddToCart($pdo): void {
    try {
        $pdo->beginTransaction();

        $sql = <<<SQL
            INSERT INTO
                EC_cart_detail (
                    cart_id,
                    product_id,
                    product_qty,
                    created_at,
                    updated_at
            ) VALUES (
                :cart_id,
                :product_id,
                :qty,
                :created_at,
                :updated_at
            );
        SQL;
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $_POST['product_id']);
        $stmt->bindValue(':qty', 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * カート内の商品数を変更する
 * 
 * @param object $pdo
 * @return void
 */
function updateQty(object $pdo, int $qty): void {
    try {
        $pdo->beginTransaction();
        $sql = <<<SQL
            UPDATE
                EC_cart_detail
            SET
                product_qty = :qty,
                updated_at = :updated_at
            WHERE
                cart_id = :cart_id
            AND
                product_id = :product_id;
        SQL;
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':qty', $qty, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $date);
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $_POST['product_id']);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/**
 * カート内から指定の1商品の情報を取得
 * 
 * @param object $pdo
 * @return object $stmt カート内の指定の1商品の情報
 */
function fetchOneInCart(object $pdo, int $id) {
    $sql = 'SELECT * FROM EC_cart_detail WHERE cart_id = :cart_id AND product_id = :product_id;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
    $stmt->bindValue(':product_id', $id);
    $stmt->execute();

    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rec;
}
/**
 * 商品テーブルから指定の1商品の情報を取得
 * 
 * @param object $pdo
 * @return array $rec 商品テーブルの指定の1商品の情報
 */
function fetchOneFromProduct(object $pdo, int $id) {
    $sql = 'SELECT * FROM EC_product WHERE product_id = :id;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rec;
}


/*-------------------------
 * thankyou.php
 *-------------------------*/
/**
 * カート内の全商品を売上・仕入テーブルへ登録
 * 
 * @param object $pdo
 * @return void
 */
function insertSales(object $pdo, array $product): void {
    $sql = <<<SQL
        INSERT INTO
            EC_sales_purchases (
                product_id,
                cart_id,
                changed_qty,
                created_at,
                updated_at
        ) VALUES (
            :product_id,
            :cart_id,
            :qty,
            :created_at,
            :updated_at
        )
    SQL;
    try {
        $pdo->beginTransaction();
  
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':product_id', $product['product_id']);
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':qty', $product['product_qty']);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();

        $pdo->commit();

    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


function changeStock(object $pdo, array $product) {
    $stock = fetchOneFromProduct($pdo, $product['product_id']);
    $changed_qty = $stock['stock_qty'] - $product['product_qty'];
    $date = date('Y-m-d');

    $sql = <<<SQL
        UPDATE
            EC_product
        SET
            stock_qty = :qty
            updated_at = :updated_at
        WHERE
            product_id = :id
    SQL;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':qty', $changed_qty, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $date);
        $stmt->bindValue(':id', $product['product_id']);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }

}

/**
 * カート内商品テーブルから全情報を取得
 * 
 * @param object $pdo
 * @return object $stmt
 */
function fetchAllInCart(object $pdo) {
    $sql = 'SELECT * FROM EC_cart_detail WHERE cart_id = :id;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['cart_id']);
    $stmt->execute();

    return $stmt;
}


/**
 * カート内の商品を全削除する
 * 
 * @param object $pdo
 * @return void
 */
function clearCart(object $pdo): void {
    try {
        $pdo->beginTransaction();
        $sql = 'DELETE FROM EC_cart_detail WHERE cart_id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['cart_id']);
        $stmt->execute();
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * 売上・仕入テーブルから販売した商品情報を取得
 * 
 * @param object $pdo
 * @param int $cartId カート作成時に付与されたカートID
 * @return object $stmt
 */
function getSales(object $pdo, int $cartId) {
    $sql = <<<SQL
        SELECT *
        FROM EC_sales_purchases sp
        LEFT JOIN EC_product p
        ON sp.product_id = p.product_id
        JOIN EC_image i
        ON p.image_id = i.image_id
        WHERE sp.cart_id = :id
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $cartId);
    $stmt->execute();

    return $stmt;
}