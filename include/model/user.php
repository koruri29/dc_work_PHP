<?php

/**
 * 登録画面で入力のユーザー情報をチェックする。
 * 問題がなければデータベースに登録する。
 * 
 * @param array $post フォームからのpost
 * @return void 
 */
function checkUserThenResister(array $post): void {
    global $error;
    global $msg;
    
    $pdo = getDb();
    $post = sanitize($_POST);

    validateUserName($post['user-name']);
    validatePassword($post['password']);

    if (! empty($error['user_name']) || ! empty($error['password'])) {
        return;
    }
    if (isExistingUserName($pdo, $post['user-name'])) {
        $error = array_merge($error, ['existing_user_name' => 'すでに登録されているユーザー名です。']);
        return;
    }
    if (insertUser($pdo, $post['user-name'], $post['password'])) {
        $msg = array_merge($msg, ['registered' =>'登録が完了しました。']);
    } else {
        $error = array_merge($error, ['register' => '登録に失敗しました。管理者にお問い合わせください。']);
    }
}


/**
 * ユーザー名のバリデーション
 * 
 * @param string $userName
 * @return void
 */
function validateUserName(string $userName): void {
    global $error;
    if (preg_match('/^[a-zA-Z0-9]{5,}+$/', $userName)) {
        //
    } else {
        $error = array_merge($error, ['user_name' => 'ユーザー名は半角英数字5文字以上で入力してください。']);
    }
}

/**
 * パスワードのバリデーション
 * 
 * @param string $password
 * @return void
 */
function validatePassword(string $password): void {
    global $error;
    if (preg_match('/^[a-zA-Z0-9]{8,}+$/', $password)) {
        //
    } else {
        $error = array_merge($error, ['password' => 'パスワードは半角英数字8文字以上で入力してください。']);
    }
}
