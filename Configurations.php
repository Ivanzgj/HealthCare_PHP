<?php

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/24
 * Time: 15:25
 */
class Configurations
{
    const DEBUG = true;
    const DATABASE = "healthcare";
    const USER = "root";
    const LOCALHOST ="localhost";
    const PASSWORD = "";
    const USER_TABLE = "user_tb";
    const VIBRATION_TABLE = "vibration_tb";
    const SRC_TABLE = "src_tb";
}

define('NO_ACCOUNT', 0);
define('PWD_ERROR', 1);
define('PARAM_ERROR', 2);
define('NO_SUCH_ACTION', 3);
define('ALREADY_REGISTERED', 4);
define('INTERNAL_ERROR', 5);
define('USER_TABLE', Configurations::USER_TABLE);
define('VIBRATION_TABLE', Configurations::VIBRATION_TABLE);
define('SRC_TABLE', Configurations::SRC_TABLE);

if (Configurations::DEBUG) {
    $REQ = $_REQUEST;
} else {
    $REQ = $_POST;
}

if (!isset($db)) {
    $db = new Database();
}