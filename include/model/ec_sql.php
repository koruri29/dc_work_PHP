<?php
/*-------------------------
 * common
 *-------------------------*/
/**
 * LAST_INSERT_IDを取得する
 * 
 * @param object $pdo
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
 * @return bool すでに登録があればtrue
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
 * @param object $pdo
 * @param string $userName
 * @return array|bool データがあれば配列。なければfalse
 */
function fetchUser(object $pdo, string $user_name) {
    $sql = 'SELECT * FROM EC_user WHERE user_name = :name;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $user_name);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}


/**
 * カートIDを作成
 * 
 * @param object $pdo
 * @return void
 */
function createCart(object $pdo): void {
    $sql = <<<SQL
        INSERT INTO
            EC_cart (
                user_name,
                created_at,
                updated_at
        ) VALUES (
            :user_name,
            :created_at,
            :updated_at
        );
    SQL;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':user_name', $_SESSION['user_name']);
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


/*-------------------------
 * edit.php
 *-------------------------*/
/**
 * データベースからすべての商品データを取得
 * 
 * @param object $pdo
 * @return object $stmt 商品データ
 */
function fetchAllProduct(object $pdo): object {
    $sql = <<<SQL
        SELECT
            product_id,
            product_name,
            price,
            qty,
            public_flag,
            p.updated_at,
            image_name
        FROM EC_product p 
        LEFT JOIN EC_image i
        ON p.image_id = i.image_id
        WHERE 1 = 1
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt;
}


/**
 * postされた画像ファイルをデータベースへ挿入
 * 
 * @param object $pdo
 * @return void
 */
function insertImage(object $pdo): void {
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

    try {
        $pdo->beginTransaction();

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
 * @param object $pdo
 * @param int $last_insert_id 直前にEC_imageへ挿入された画像データのID
 * @return bool 挿入が成功すればtrue
 */
function insertProduct(object $pdo, int $last_insert_id):bool {
    $sql = <<<SQL
        INSERT INTO
        EC_product (
            product_name,
            price,
            qty,
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

    try {
        $pdo->beginTransaction();
        
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
 * @param int $id 商品ID
 * @param int $qty 変更したい商品の数量
 * @return void
 */
function updateStock(object $pdo, int $id, int $qty): void {
    global $msg_update;
    $date = date('Y-m-d');

    $sql = <<<SQL
        UPDATE
            EC_product
        SET
            qty = :qty,
            updated_at = :updated_at
        WHERE
            product_id = :id
    SQL;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':qty', $qty);
        $stmt->bindValue(':updated_at', $date);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        $pdo->commit();
        
        if ($stmt->rowCount() > 0) {            
            $msg_update = array_merge($msg_update, ['stock' => '在庫数を更新しました。']);
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
 * @param int $id 商品ID
 * @return void
 */
function updateFlag(object $pdo, int $id): void {
    global $msg_update;
    $product = fetchOneFromProduct($pdo, $id);
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
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        $pdo->commit();

        if ($stmt->rowCount() > 0) {            
            if ($flag === 0) {
                $msg_update = array_merge($msg_update, ['non-display' => '非公開に変更しました。']);
            } else {
                $msg_update = array_merge($msg_update, ['display' => '公開に変更しました。']);
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
 * @param int $id 商品ID
 * @return void
 */
function deleteProduct(object $pdo, int $id): void {
    global $msg_update;
    $sql_image = 'DELETE FROM EC_image WHERE image_id = :id;';
    $sql_product = 'DELETE FROM EC_product WHERE product_id = :id;';

    $product = fetchOneFromProduct($pdo, $id);    

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql_product);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $stmt = $pdo->prepare($sql_image);
        $stmt->bindValue(':id', $product['image_id']);
        $stmt->execute();

        $pdo->commit();

        if ($stmt->rowCount() > 0) {            
            $msg_update = array_merge($msg_update, ['delete' => '商品を削除しました。']);
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
 * @param object $pdo
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


/**
 * 商品テーブルに登録の商品の種類数をカウント
 * 
 * 商品編集画面で、フォームで飛ばすために商品の総種類数を数える
 * 
 * @param object $pdo
 * @return int $count['cnt'] 商品テーブルに登録されている商品の種類数
 */
function countAllProduct(object $pdo): int {
    $sql = 'SELECT COUNT(*) AS cnt FROM EC_product WHERE 1 = 1;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    return $count['cnt'];
}


/*-------------------------
 * product.php
 *-------------------------*/
/**
 * データベースから公開フラグが「公開」の商品情報を取得する
 * 
 * @param object $pdo
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
        SELECT 
            p.product_name,
            p.price,
            d.qty,
            i.image_name,
            p.product_id
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


/**
 * カート内の商品の種類数をカウント
 * 
 * カート内の商品数変更フォームで、配列の数を数えるための関数
 * 
 * @param object $pdo
 * @return int 配列が何番目まであるかの数字
 */
function countProductInCart(object $pdo): int {
    $sql = 'SELECT count(*) AS cnt FROM EC_cart_detail WHERE cart_id = :id;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['cart_id']);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    return $rec['cnt'];
}


/**
 * カート内の商品を削除する関数
 * 
 * @param object $pdo
 * @param int $id 商品ID
 * @return void
 */
function deleteProductInCart(object $pdo, int $id): void {
    global $msg;
    $sql = <<<SQL
        DELETE FROM
            EC_cart_detail
        WHERE
            cart_id = :cart_id
        AND
            product_id = :product_id;
    SQL;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $id);
        $stmt->execute();

        $pdo->commit();
        $msg = array_merge($msg, ['deleted' => '商品を削除しました。']);
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/**
 * カートにない商品をカートに入れる場合
 * 
 * @param object $pdo
 * @return void
 */
function newlyAddToCart(object $pdo): void {
    global $msg;
    $sql = <<<SQL
        INSERT INTO
            EC_cart_detail (
            cart_id,
            product_id,
            qty,
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

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $_POST['product-id']);
        $stmt->bindValue(':qty', 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();

        $pdo->commit();
        $msg = array_merge($msg, ['add_to_cart' => 'カートに商品を追加しました。']);
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * カート内の商品数を変更する
 * 
 * @param object $
 * @param int $id 商品ID
 * @param int $qty 変更したい商品数量
 * @return void
 */
function updateQty(object $pdo, int $id, int $qty): void {
    global $msg;
    $sql = <<<SQL
        UPDATE
            EC_cart_detail
        SET
            qty = :qty,
            updated_at = :updated_at
        WHERE
            cart_id = :cart_id
        AND
            product_id = :product_id;
    SQL;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':qty', $qty, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $date);
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $id);
        $stmt->execute();

        $pdo->commit();
        if ($_SERVER['REQUEST_URI'] == '/omiya/0006/ec_site/product.php') {
            $msg = array_merge($msg, ['into_cart' => 'カートに商品を追加しました。']);
        } else {
            $msg = array_merge($msg, ['changed_qty' => '数量を変更しました。']);
        }
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
 * @param int $id 商品ID
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
 * @param int $id 取得したい商品ID
 * @return array $rec 商品テーブルの指定の1商品の情報
 */
function fetchOneFromProduct(object $pdo, int $id): array {
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
 * 編集中のテーブルにロックをかける
 * 
 * @param object $pdo
 * @return void
 */
function lockTable(object $pdo): void {
    $sql = <<<SQL
        LOCK TABLES
            EC_cart_detail WRITE,
            EC_product WRITE,
            EC_sales WRITE
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

/**
 * テーブルにかけたロックを外す
 * 
 * @param object $pdo
 * @return void
 */
function unlockTable(object $pdo): void {
    $sql = 'UNLOCK TABLES';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}


/**
 * カート内の全商品を売上・仕入テーブルへ登録
 * 
 * @param object $pdo
 * @param array $product カート内商品テーブルから取得した商品情報
 * @return void
 */
function insertSales(object $pdo, array $product): void {
    global $msg;
    $sql = <<<SQL
        INSERT INTO
            EC_sales (
                product_id,
                cart_id,
                qty,
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
        $stmt->bindValue(':qty', $product['qty']);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
        $stmt->execute();

        $pdo->commit();

        if ($stmt->rowCount() > 0) {
            $msg = '購入が完了しました。ありがとうございました。';
        }
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/**
 * 商品決済の際、商品テーブルから売れた分の在庫数を減らす
 * 
 * @param object $pdo
 * @param array $product
 * @return void
 */
function changeStock(object $pdo, array $product) : void{
    $stock = fetchOneFromProduct($pdo, $product['product_id']);
    $changed_qty = $stock['qty'] - $product['qty'];
    $date = date('Y-m-d');

    $sql = <<<SQL
        UPDATE
            EC_product
        SET
            qty = :qty,
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
 * @return object|null $stmt
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
        $sql = 'DELETE FROM EC_cart_detail WHERE cart_id = :id;';
    try {
        $pdo->beginTransaction();
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
 * @return object $stmt
 */
function getSales(object $pdo): object {
    $sql = <<<SQL
        SELECT
            p.product_id,
            p.product_name,
            p.price,
            s.qty,
            i.image_name
        FROM EC_sales s
        LEFT JOIN EC_product p
        ON s.product_id = p.product_id
        JOIN EC_image i
        ON p.image_id = i.image_id
        WHERE s.cart_id = :id
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['cart_id']);
    $stmt->execute();
    return $stmt;
}


/*-------------------------
 * SEARCH
 *-------------------------*/
/**
 * 商品の検索機能
 * 
 * @param object $pdo
 * @return object|null $stmt 検索結果のデータ
 */
function searchResult(object $pdo) {
    $words = adjustSearchWords();

    $arr = array();
    $arr[] = <<<SQL
        SELECT *
        FROM EC_product p
        LEFT JOIN EC_image i
        ON p.image_id = i.image_id
        WHERE public_flag = 1 AND product_name LIKE 
    SQL;
    if ($_POST['and-or'] == 'and') {
        for ($i = 0; $i < count($words); $i++) {
            $arr[] = '?';
            $arr[] = ' AND product_name LIKE ';
        }
    } else {
        for ($i = 0; $i < count($words); $i++) {
            $arr[] = '?';
            $arr[] = ' OR product_name LIKE ';
        }
    }
    $arr[count($words) * 2] = '';//最後のORを削除

    $sql = implode('', $arr);
    if (empty($words)) $sql = 'SELECT * FROM EC_product WHERE 1 = 0;';

    $stmt = $pdo->prepare($sql);
    if (! empty ($words)) {
        $i = 1;
        foreach ($words as $word) {
            $stmt->bindValue($i, '%' . $word . '%');
            $i++;
        }
    }
    $stmt->execute();

    return $stmt;
}
/** 
 * 商品検索結果の個数をカウントする
 * 
 * @param object $pdo
 * @return int 検索結果の個数
 */
function countSearchResult(object $pdo): int {
    $words = adjustSearchWords();
    if (empty($words)) return 0;

    $arr = array();
    $arr[] = <<<SQL
        SELECT COUNT(*) AS cnt
        FROM EC_product p
        LEFT JOIN EC_image i
        ON p.image_id = i.image_id
        WHERE public_flag = 1 AND product_name LIKE 
    SQL;
    if ($_POST['and-or'] == 'and') {
        for ($i = 0; $i < count($words); $i++) {
            $arr[] = '?';
            $arr[] = ' AND product_name LIKE ';
        }
    } else {
        for ($i = 0; $i < count($words); $i++) {
            $arr[] = '?';
            $arr[] = ' OR product_name LIKE ';
        }
    }

    $arr[count($words) * 2] = '';//最後のORを削除
    $sql = implode('', $arr);

    $stmt = $pdo->prepare($sql);
    $i = 1;
    foreach ($words as $word) {
        $stmt->bindValue($i, '%' . $word . '%');
        $i++;
    }
    $stmt->execute();

    $cnt = $stmt->fetch(PDO::FETCH_ASSOC);
    return $cnt['cnt'];
}


/*-------------------------
 * AutoLogin（追加要件）
 *-------------------------*/
/**
 * トークンを生成し、自動ログインテーブルにセット
 * 
 * 自動ログインを判断するためのトークンを生成し、
 * ユーザーID・有効期限とともに自動ログインテーブルにセットする。
 * 
 * @param object $pdo
 * @param string $user_name ユーザー名
 * @return string $token トークン（乱数）
 */
function setAuthToken(object $pdo, string $user_name): string {
    global $timeout;
    $token = bin2hex(random_bytes(100));
    $sql = <<<SQL
        INSERT INTO
            EC_autologin (
                token,
                user_name,
                expires
            ) VALUES (
                :token,
                :user_name,
                :expires
            )
    SQL;

    try {
        $pdo->beginTransaction();
    
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':user_name', $user_name);
        $stmt->bindValue(':expires', time() + $timeout);
        $stmt->execute();
    
        $pdo->commit();
        return $token;
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}


/**
 * 自動ログイン時、カートIDをセットする
 * 
 * @param object $pdo
 * @return void
 */
function setCartIdToAutologin(object $pdo): void {
        $sql = 'UPDATE EC_autologin SET cart_id = :id WHERE token = :token';
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['cart_id']);
        $stmt->bindValue(':token', $_COOKIE['token']);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * 自動ログインテーブルから、該当するトークンのレコードを取得
 * 
 * @param object $pdo
 * @return object $stmt
 */
function fetchAutoLogin(object $pdo): object {
    $sql = 'SELECT * FROM EC_autologin WHERE token = :token';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':token', $_COOKIE['token']);
    $stmt->execute();
    
    return $stmt;
}


/**
 * ログアウト時、登録済みのトークンを削除する
 * 
 * @param object $pdo
 * @return void
 */
function deleteToken (object $pdo): void {
        $sql = 'DELETE FROM EC_autologin WHERE token = :token;';
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':token', $_COOKIE['token']);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}