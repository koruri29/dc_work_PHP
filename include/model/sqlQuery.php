<?php

function getSqlResult(object $pdo, string $sql, mixed ...$args): array {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rec;
}


/*-------------------------
 * login.php
 *-------------------------*/
/**
 * ユーザー登録時、すでに同じユーザー名が登録されていないかチェック
 * 
 * @param object $pdo
 * @param string $userName
 * @return bool 重複があればtrue
 */
function isExistingUserName(object $pdo, string $userName): bool {
    $sql = 'SELECT COUNT(*) AS cnt FROM EC_user WHERE user_name = :name';
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
    
    $pdo->beginTransaction();

    $sql = 'INSERT INTO EC_user (user_name, password, created_at, updated_at) VALUES(:name, :password, :created_at, :updated_at)';
    $stmt = $pdo->prepare($sql);
	$date = date('Y-m-d');
    $stmt->bindValue(':name', $userName);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':created_at', $date);
    $stmt->bindValue(':updated_at', $date);

    if ($stmt->execute()) {
        //
    } else {
        $error = array_merge($error, ['sql' => 'SQL実行エラー：' . $sql]);
    }
    
    if (! empty($error['sql'])) {
        $pdo->rollback();
        return false;
    } else {
        $pdo->commit();
        return true;
    }
}