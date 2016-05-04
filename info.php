<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/29
 * Time: 12:53
 */
require_once "Database.php";
require_once "Configurations.php";

if (isset($REQ['action'])) {
    $action = $REQ['action'];
    $file = "log.txt";
    file_put_contents($file, $REQ, FILE_APPEND);
    file_put_contents($file, "\n", FILE_APPEND);

    if (strcmp($action, "info") == 0) {
        $ret = info();
    } else {
        $ret = array();
        $ret['errorCode'] = NO_SUCH_ACTION;
        $ret['error'] = "<html><head></head><body><p>Error!</p></body></html>";
    }

    echo json_encode($ret);
}

function info() {
    $ret = array();
    $ret["info"] = "<html><head><meta charset='utf-8'></head><body>".
                    "<p style=\"color: #FF0000; font-size: small\">今日通知</p>".
                    "<p style=\"font-size: small\">Hello, 大家好，我是美丽可爱善良活泼大方聪明机智善解人意体贴入微的服务器MM。很高兴认识大家~</p>".
                    "<p style=\"color: #FF0000; font-size: small\">今日建议</p>".
                    "<p style=\"font-size: small\">听说每天健身有助于快速脱单哦~</p>".
                    "</body></html>";
    return $ret;
}

