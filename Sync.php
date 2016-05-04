<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/26
 * Time: 14:19
 */

require_once "Database.php";
require_once "Configurations.php";

if (isset($REQ['action'])) {
    $action = $REQ['action'];
    $file = "log.txt";
    file_put_contents($file, $REQ, FILE_APPEND);
    file_put_contents($file, "\n", FILE_APPEND);

    if (strcmp($action, "upload") == 0) {
        $ret = upload();
    }
    else if (strcmp($action, "download") == 0) {
        $ret = download();
    }
    else if (strcmp($action, "clear") == 0) {
        $ret = clear();
    }
    else {
        $ret = array();
        $ret['errorCode'] = NO_SUCH_ACTION;
        $ret['error'] = "No such action";
    }

    echo json_encode($ret);
}

function upload() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"]) && isset($REQ["data"])) {

        $uid = $REQ["uid"];
        $result = $db->query()->table(USER_TABLE)
            ->field("uid")
            ->where("uid")->eq($uid, "string")
            ->select(1);
        if (count($result) == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "uid error";
        } else {
            $data = json_decode($REQ["data"], true);
            foreach ($data as $val) {
                $time = $val["time"];
                $acc = $val["acc_data"];
                $src = $val["src_time"];
                $status = $val["src_status"];

                $result = $db->query()->table(VIBRATION_TABLE)
                    ->field("data")
                    ->where("time")->eq($time, "string")
                    ->where("uid")->eq($uid, "string")
                    ->select();
                if (count($result) == 0) {
                    $db->query()->table(VIBRATION_TABLE)
                        ->add("time", $time, "string")
                        ->add("data", $acc, "string")
                        ->add("uid", $uid, "string")
                        ->insert();
                }

                $result = $db->query()->table(SRC_TABLE)
                    ->field("status")
                    ->where("date")->eq($time, "string")
                    ->where("uid")->eq($uid, "string")
                    ->select();
                if (count($result) == 0) {
                    $db->query()->table(SRC_TABLE)
                        ->add("date", $time, "string")
                        ->add("time", $src, "string")
                        ->add("status", $status, "string")
                        ->add("uid", $uid, "string")
                        ->insert();
                }
            }
            $ret["ok"] = true;
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function download() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"]) && isset($REQ["time"])) {

        $uid = $REQ["uid"];
        $result = $db->query()->table(USER_TABLE)
            ->field("uid")
            ->where("uid")->eq($uid, "string")
            ->select(1);
        if (count($result) == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "uid error";
        } else {
            $time = $REQ["time"];
            if (strcmp($time, "0")) {
                $result = $db->query()->table(VIBRATION_TABLE)
                    ->field("data")
                    ->where("uid")->eq($uid, "string")
                    ->where("time")->eq($time, "string")
                    ->select(1);
                if (count($result) != 0) {
                    $ret["acc_data"] = $result[0]["data"];
                } else {
                    $ret["acc_data"] = "";
                }
                $result = $db->query()->table(SRC_TABLE)
                    ->field("time")
                    ->field("status")
                    ->where("uid")->eq($uid, "string")
                    ->where("date")->eq($time, "string")
                    ->select(1);
                if (count($result) != 0) {
                    $ret["src_time"] = $result[0]["time"];
                    $ret["src_status"] = $result[0]["status"];
                } else {
                    $ret["src_time"] = "";
                    $ret["src_status"] = "";
                }
                $ret["date"] = $time;
            } else {
                $acc = $db->query()->table(VIBRATION_TABLE)
                    ->field("data")
                    ->field("time")
                    ->where("uid")->eq($uid, "string")
                    ->select();
                $src = $db->query()->table(SRC_TABLE)
                    ->field("time")
                    ->field("status")
                    ->where("uid")->eq($uid, "string")
                    ->select();
                $data = array();
                for ($i=0;$i<count($acc);$i++) {
                    $data[$i]["time"] = $acc[$i]["time"];
                    $data[$i]["acc_data"] = $acc[$i]["data"];
                    $data[$i]["src_time"] = $src[$i]["time"];
                    $data[$i]["src_status"] = $src[$i]["status"];
                }
                $ret["data"] = $data;
            }
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function clear() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"])) {
        $uid = $REQ["uid"];
        $result = $db->query()->table(USER_TABLE)
            ->field("uid")
            ->where("uid")->eq($uid, "string")
            ->select(1);
        if (count($result) == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "uid error";
        } else {
            $db->query()->table(VIBRATION_TABLE)
                ->where("uid")->eq($uid, "string")
                ->delete();
            $db->query()->table(SRC_TABLE)
                ->where("uid")->eq($uid, "string")
                ->delete();
            $ret["ok"] = true;
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}
