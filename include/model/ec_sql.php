<?php

function getSqlResult(object $pdo, string $sql, mixed ...$args): array {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rec;
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
function isExistingUserName(object $pdo, string $userName): bool {
    $sql = 'SELECT COUNT(*) AS cnt FROM EC_user WHERE user_name = :name;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $userName);
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
function insertUser(object $pdo, string $userName, string $password): bool {
    $password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();
    
        $sql = 'INSERT INTO EC_user (user_name, password, created_at, updated_at) VALUES(:name, :password, :created_at, :updated_at);';
        $stmt = $pdo->prepare($sql);
        $date = date('Y-m-d');
        $stmt->bindValue(':name', $userName);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);
    
        $stmt->execute();
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        return false;
    }
}


/*-------------------------
 * login.php
 *-------------------------*/
/**
 * データベースからユーザー情報を取得する関数
 * 
 * @param array $post フォームから投稿された情報
 * @return array|bool データがあれば配列。なければfalse
 */
function fetchUser(array $post) {
    $post = sanitize($_POST);
    
    $pdo = getDb();
    try {
        $sql = 'SELECT * FROM EC_user WHERE user_name = :name;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $post['user-name']);
        $stmt->execute();
    } catch (PDOException $e) {
        $e->getMessage();
        exit();
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
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
 * @param array $file postされた画像データ
 * @return 
 */
function insertImage(object $pdo, array $file): void {
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
        $stmt->bindValue(':name', $file['image']['name']);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);

        if ($stmt->execute()) {
            $pdo->commit();
        }

        // $last_insert_id = lastInsertId();
        // return $last_insert_id;
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}
/**
 * postされた商品情報をデータベースに挿入する
 * 
 * @param array $post フォームから投稿された情報
 * @param int $last_insert_id 直前にEC_imageへ挿入された画像データのID
 * @return bool 挿入が成功すればtrue
 */
function insertProduct(object $pdo, array $post, int $last_insert_id):bool {
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
        $stmt->bindValue(':name', $post['name']);
        $stmt->bindValue(':price', $post['price']);
        $stmt->bindValue(':qty', $post['qty']);
        $stmt->bindValue(':image_id', $last_insert_id);
        $stmt->bindValue(':flag', $post['public_flag'], PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $date);
        $stmt->bindValue(':updated_at', $date);

        if ($stmt->execute()) {
            $pdo->commit();
            return true;
        }
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit();
    }
}

/**
 * LAST_INSERT_IDを取得する
 * 
 * @return int LAST_INSERT_ID
 */
function lastInsertId(object $pdo): int {
    // $pdo = $getDb();

    $sql = 'SELECT LAST_INSERT_ID()';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_insert_id = $rec['LAST_INSERT_ID()'];
    return $last_insert_id;
}