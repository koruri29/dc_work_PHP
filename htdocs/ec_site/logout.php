<?php
session_start();
session_regenerate_id(true);

require_once ('../../include/model/ec_common.php');


$session = array();
session_destroy();

header('Location: ./index.php');
exit();