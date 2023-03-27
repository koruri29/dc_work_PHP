<?php

/*-------------------------
 * register.php
 *-------------------------*/
/**
 * 登録画面で入力のユーザー情報をチェックする。
 * 問題がなければデータベースに登録する。
 * 
 * @param object $pdo
 * @return void 
 */
function checkUserThenResister(object $pdo): void {
    global $error;
    global $msg;
    
    validateUserName();
    validatePassword();

    if (isExistingUserName($pdo)) {
        $error = array_merge($error, ['existing_user_name' => 'すでに登録されているユーザー名です。']);
    }
    if (empty($error)) {
        if (insertUser($pdo)) {
            $msg = '登録が完了しました。';
        } else {
            $error = array_merge($error, ['register' => '登録に失敗しました。管理者にお問い合わせください。']);
        }
    }
}


/**
 * ユーザー名のバリデーション
 * 
 * @return void
 */
function validateUserName(): void {
    global $error;
    if (preg_match('/^[a-zA-Z0-9]{5,}$/', $_POST['user-name'])) {
        //
    } else {
        $error = array_merge($error, ['user_name' => 'ユーザー名は半角英数字5文字以上で入力してください。']);
    }
}

/**
 * パスワードのバリデーション
 * 
 * @return void
 */
function validatePassword(): void {
    global $error;
    if (preg_match('/^[a-zA-Z0-9]{8,}$/', $_POST['password'])) {
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
 * @param object $pdo
 * @return void 
 */
function authUser(object $pdo): void {
    global $error;

    $user = fetchUser($pdo, $_POST['user-name']);

    if ($user == false) {
        $error = array_merge($error, ['login' => 'ユーザー名かパスワードが間違っています。']);
        return;
    }
    if (! password_verify($_POST['password'], $user['password'])) {
        $error = array_merge($error, ['login' => 'ユーザー名かパスワードが間違っています。']);
        return;
    }

    setSession($user);

    if ($user['user_name'] == 'ec_admin') {
        header('Location: edit.php');
        exit();
    } else {
        header('Location: product.php', true, 307);
        exit();   
    }
}


/*-------------------------
 * thankyou.php
 *-------------------------*/
/**
 * 購入完了時、カート内商品を削除し新しいカートを作る
 *  
 * @param object $pdo
 * @return void
 */
function restartCart(object $pdo): void {

    clearCart($pdo);
    createCart($pdo);
    $_SESSION['cart_id'] = lastInsertId($pdo);
}
