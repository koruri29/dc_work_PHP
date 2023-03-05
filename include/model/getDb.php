<?php

/**
 * データベースへ接続する関数
 * 
 * ※オプションについて（上から）
 * エラー発生の場合に例外を投げる
 * SQLの複文を禁止する
 * 静的プレースホルダの設定
 * 
 * @return object $pdo
 */
function getDb(): object {
    require_once (__DIR__ . '/../config/const.php');
    $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    );
    try {
        $pdo = new PDO(DSN, LOGIN_USER, PASSWORD, $opt);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
    return $pdo;
}