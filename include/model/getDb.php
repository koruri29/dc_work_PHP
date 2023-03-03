<?php
function getDb() {
    require_once ('../config/const.php');
    $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    );
    try {
        $db = new PDO(DSN, LOGIN_USER, PASSWORD, $opt);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
    return $db;
}