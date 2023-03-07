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
    $error = array();
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
        $e->getMessage();
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