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
                :updated_at,
            );
        SQL;
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':user_id', $_SESSION['user_name']);
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
    // $pdo = getDb();

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
    // $pdo = getDb();

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
 * 商品一覧画面から「カートに入れる」を押した場合
 * 
 * @param object $pdo
 * @return void
 */
function addToCart(object $pdo): void {
    $sql = 'SELECT product_id FROM EC_cart_detail WHERE 1 = 1;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    while (true) {
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rec == false) {
            newlyAddToCart($pdo);
            break;
        }
        if (array_search($_POST['product_id'], $rec['product_id'], true)) {
            alreadyInCart($pdo);
            break;
        }
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
        $pdo->biginTransaction();

        $sql = <<<SQL
            INSERT INTO
                EC_cart_detail (
                    cart_id,
                    product_id,
                    product_qty
                ) VALUES (
                    :cart_id,
                    :product_id,
                    :qty
                );
        SQL;
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cart_id',$_SESSION['cart_id']);
        $stmt->bindValue(':product_id',$_POST['product_id']);
        $stmt->bindValue(':qty', 1, PDO::PARAM_INT);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        $e->getMessage();
        exit();
    }
}

/**
 * 「カートに入れる」を押したが、すでに同じ商品が入っている場合
 * 
 * @param object $pdo
 * @return void
 */
function alreadyInCart(object $pdo): void {
    try {
        $pdo->beginTransaction();

        $sql = <<<SQL
            SELECT
                product_qty
            FROM
                EC_cart_detail
            WHERE
                cart_id = :cart_id
            AND
                product_id = :product_id
        SQL;
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cart_id', $_SESSION['cart_id']);
        $stmt->bindValue(':product_id', $_POST['product_id']);
        $stmt->execute();
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $sql = 'UPDATE EC_cart_detail SET product_qty = :qty WHERE product_id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':qty', $rec['product_qty'] + 1, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $_POST['product_id']);
        $stmt->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}