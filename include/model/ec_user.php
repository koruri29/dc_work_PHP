<?php

/*-------------------------
 * register.php
 *-------------------------*/
/**
 * 登録画面で入力のユーザー情報をチェックする。
 * 問題がなければデータベースに登録する。
 * 
 * @return void 
 */
function checkUserThenResister(): void {
    global $error;
    global $msg;
    
    $pdo = getDb();

    validateUserName($_POST['user-name']);
    validatePassword($_POST['password']);

    if (! empty($error['user_name']) || ! empty($error['password'])) {
        return;
    }
    if (isExistingUserName($pdo, $_POST['user-name'])) {
        $error = array_merge($error, ['existing_user_name' => 'すでに登録されているユーザー名です。']);
        return;
    }
    if (insertUser($pdo, $_POST['user-name'], $_POST['password'])) {
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


/*-------------------------
 * login.php
 *-------------------------*/
/**
 * ユーザーログイン認証
 * 
 * 認証がOKなら商品一覧（index.php）へ飛ぶ。
 * adminユーザー(ID: ec_admin)の場合は商品管理ページ（product.php）へ飛ぶ。
 * 
 * @return void 
 */
function authUser(object $pdo): void {
    global $error;

    $user = fetchUser($pdo);

    if ($user == false) {
        $error  = array_merge($error, ['login' => 'ユーザー名かパスワードが間違っています。']);
        return;
    }
    if (! password_verify($_POST['password'], $user['password'])) {
        $error  = array_merge($error, ['login' => 'ユーザー名かパスワードが間違っています。']);
        return;
    }

    setSession($user);

    if ($user['user_name'] == 'ec_admin') {
        header('Location: product.php');
        exit();
    } else {
        createCart($pdo);//ログイン時にカートを作成
        $_SESSION['cart_id'] = lastInsertId($pdo);
        header('Location: index.php');
        exit();   
    }
}