<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/24
 * Time: 22:49
 */

require_once "Database.php";
require_once "Configurations.php";

if (isset($REQ['action'])) {
    $action = $REQ['action'];
    $file = "log.txt";
    file_put_contents($file, $REQ, FILE_APPEND);
    file_put_contents($file, "\n", FILE_APPEND);

    if (strcmp($action, "login") == 0) {
        $ret = login();
    }
    else if (strcmp($action, "register") == 0) {
        $ret = register();
    }
    else if (strcmp($action, "modify") == 0) {
        $ret = changePwd();
    }
    else if (strcmp($action, "upload") == 0) {
        $ret = upload();
    }
    else if (strcmp($action, "info") == 0) {
        $ret = info();
    }
    else if (strcmp($action, "delete") == 0) {
        $ret = delete();
        echo "<script>window.history.back()</script>";
        return;
    }
    else if (strcmp($action, "reset") == 0) {
        $ret = resetUser();
        echo "<script>window.history.back()</script>";
        return;
    }
    else {
        $ret = array();
        $ret['errorCode'] = NO_SUCH_ACTION;
        $ret['error'] = "No such action";
    }

    echo json_encode($ret);
}

function login() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ['account']) && isset($REQ['pwd'])) {
        $results = $db->query()->table(USER_TABLE)
            ->field("pwd")
            ->field("uid")
            ->field("name")
            ->field("age")
            ->field("sex")
            ->field("birth")
            ->field("constellation")
            ->field("introduction")
            ->field("email")
            ->field("address")
            ->field("measure_today_times")
            ->field("measure_total_times")
            ->field("measure_total_assessment")
            ->where("account")->eq($REQ['account'], "string")
            ->select(1);
        if (count($results) == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "Account Error";
        } else {
            $result = $results[0];
            $pwd = $result["pwd"];
            if (strcmp($pwd, $REQ["pwd"]) != 0) {
                $ret['errorCode'] = PWD_ERROR;
                $ret['error'] = "Password Error";
            } else {
                $ret['uid'] = $result['uid'];
                $ret['name'] = $result['name'];
                $ret['age'] = $result['age'];
                $ret['sex'] = $result['sex'];
                $ret['birth'] = $result['birth'];
                $ret['constellation'] = $result['constellation'];
                $ret['introduction'] = $result['introduction'];
                $ret['email'] = $result['email'];
                $ret['address'] = $result['address'];
                $ret['measure_today_times'] = $result['measure_today_times'];
                $ret['measure_total_times'] = $result['measure_total_times'];
                $ret['measure_total_assessment'] = $result['measure_total_assessment'];
                $ret["avatar"] = "/upload/".$result['uid'].".png";
            }
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function register() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ['account']) && isset($REQ['pwd'])) {
        $results = $db->query()->table(USER_TABLE)
            ->field("account")
            ->where("account")->eq($REQ["account"], "string")
            ->select(1);
        if (count($results) != 0) {
            $ret["errorCode"] = ALREADY_REGISTERED;
            $ret["error"] = "Account already registered";
        } else {
            $results = $db->query()->table(USER_TABLE)
                ->field("uid")
                ->order("uid DESC")
                ->select(1);
            if (count($results) == 0) {
                $uid = 1;
            } else {
                $uid = intval($results[0]["uid"])+1;
            }
            $ok = $db->query()->table(USER_TABLE)
                ->add("uid", $uid, "String")
                ->add("account", $REQ["account"], "string")
                ->add("pwd", $REQ["pwd"], "string")
                ->add("name", "User_".$uid, "string")
                ->add("birth", "", "string")
                ->add("constellation", 0, "int")
                ->add("introduction", "", "string")
                ->add("email", "", "string")
                ->add("address", "", "string")
                ->add("measure_today_times", 0, "int")
                ->add("measure_total_times", 0, "int")
                ->add("measure_total_assessment", 0, "int")
                ->insert();
            if ($ok) {
                $ret["uid"] = $uid;
                $ret["name"] = "User_".$uid;
                $ret['age'] = 0;
                $ret['sex'] = 2;
                $ret['birth'] = "";
                $ret['constellation'] = 0;
                $ret['introduction'] = "";
                $ret['email'] = "";
                $ret['address'] = "";
                $ret['measure_today_times'] = 0;
                $ret['measure_total_times'] = 0;
                $ret['measure_total_assessment'] = 0;
            } else {
                $ret["errorCode"] = INTERNAL_ERROR;
                $ret["error"] = "Server internal error";
            }
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function changePwd() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"]) && isset($REQ["oldPwd"]) && isset($REQ["newPwd"])) {
        $result = $db->query()->table(USER_TABLE)
                    ->field("pwd")
                    ->where("uid")->eq($REQ["uid"], "string")
                    ->select(1);
        if (count($result) == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "uid error";
        } else {
            $pwd = $result[0]["pwd"];
            if (strcmp($pwd, $REQ["oldPwd"]) == 0) {
                $ok = $db->query()->table(USER_TABLE)
                            ->set("pwd", $REQ["newPwd"], "string")
                            ->where("uid")->eq($REQ["uid"], "string")
                            ->update();
                if ($ok) {
                    $ret["ok"] = true;
                } else {
                    $ret["errorCode"] = INTERNAL_ERROR;
                    $ret["error"] = "Server internal error";
                }
            } else {
                $ret['errorCode'] = PWD_ERROR;
                $ret['error'] = "Password Error";
            }
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function upload() {
    $ret= array();
    global $db, $REQ;

    if (isset($REQ["uid"]) && isset($REQ["name"]) && isset($REQ["birth"]) && isset($REQ["sex"])
        && isset($REQ["constellation"]) && isset($REQ["introduction"]) && isset($REQ["age"])
        && isset($REQ["email"]) && isset($REQ["address"])
        && isset($REQ["measure_today_times"]) && isset($REQ["measure_total_times"])
        && isset($REQ["measure_total_assessment"])) {

        $ok = $db->query()->table(USER_TABLE)
            ->field("uid")
            ->where("uid")->eq($REQ["uid"], "string")
            ->select(1);
        if ($ok == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "uid error";
        } else {
            $ok = $db->query()->table(USER_TABLE)
                ->set("name", $REQ["name"], "string")
                ->set("sex", $REQ["sex"], "int")
                ->set("age", $REQ["age"], "int")
                ->set("birth", $REQ["birth"], "string")
                ->set("constellation", $REQ["constellation"], "int")
                ->set("introduction", $REQ["introduction"], "string")
                ->set("email", $REQ["email"], "string")
                ->set("address", $REQ["address"], "string")
                ->set("measure_today_times", $REQ["measure_today_times"], "int")
                ->set("measure_total_times", $REQ["measure_total_times"], "int")
                ->set("measure_total_assessment", $REQ["measure_total_assessment"], "int")
                ->where("uid")->eq($REQ["uid"], "string")
                ->update();
            if ($ok) {
                require_once "FileReceiver.php";
                FileReceiver::receiveFile("avatar", "upload", $REQ["uid"].".png");
                $ret["ok"] = true;
            } else {
                $ret["errorCode"] = INTERNAL_ERROR;
                $ret["error"] = "Server internal error";
            }
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function info() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"])) {
        $results = $db->query()->table(USER_TABLE)
            ->field("name")
            ->field("age")
            ->field("sex")
            ->field("birth")
            ->field("constellation")
            ->field("introduction")
            ->field("email")
            ->field("address")
            ->field("measure_today_times")
            ->field("measure_total_times")
            ->field("measure_total_assessment")
            ->where("uid")->eq($REQ['uid'], "string")
            ->select(1);
        if (count($results) == 0) {
            $ret['errorCode'] = NO_ACCOUNT;
            $ret['error'] = "uid error";
        } else {
            $ret = $results[0];
            $ret["avatar"] = "/upload/".$REQ["uid"].".png";
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function delete() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"])) {
        $uid = $REQ["uid"];
        $ok = $db->query()->table(Configurations::USER_TABLE)
            ->where("uid")->eq($uid, "string")
            ->delete();
        if ($ok) {
            $ret["ok"] = true;
        } else {
            $ret["errorCode"] = INTERNAL_ERROR;
            $ret["error"] = "Server internal error";
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

function resetUser() {
    $ret = array();
    global $db, $REQ;
    if (isset($REQ["uid"])) {
        $uid = $REQ["uid"];
        $ok = $db->query()->table(USER_TABLE)
            ->set("name", "User_".$uid, "string")
            ->set("birth", "", "string")
            ->set("constellation", 0, "int")
            ->set("introduction", "", "string")
            ->set("email", "", "string")
            ->set("address", "", "string")
            ->set("measure_today_times", 0, "int")
            ->set("measure_total_times", 0, "int")
            ->set("measure_total_assessment", 0, "int")
            ->where("uid")->eq($uid, "string")
            ->update();
        if ($ok) {
            $ret["ok"] = true;
        } else {
            $ret["errorCode"] = INTERNAL_ERROR;
            $ret["error"] = "Server internal error";
        }
    } else {
        $ret['errorCode'] = PARAM_ERROR;
        $ret['error'] = "Parameters Error";
    }
    return $ret;
}

//require_once "FileReceiver.php";
//echo FileReceiver::receiveFile("file", "upload", "ok.png");

//$file = fopen("upload/laucher.png", 'r');
//$data = fread($file, filesize("upload/laucher.png"));
//echo $data;
