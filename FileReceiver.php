<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/23
 * Time: 14:14
 */

class FileReceiver {
    /**
     * 接收文件
     * @param $fileName string 上传的文件名
     * @param $location string 保存位置文件夹路径
     * @param $rename string 保存文件名，默认为null，表示不重命名
     * @param $cover boolean 若相同名字的文件存在时是否覆盖原文件，默认为true
     * @return bool|string 返回true表示成功，或者返回错误信息
     */
    public static function receiveFile($fileName, $location, $rename = null, $cover = true) {
        if (isset($_FILES[$fileName])) {

            if ($_FILES[$fileName]["error"] > 0) {
                // 上传错误
                return "Return Code: " . $_FILES["file"]["error"];
            } else {
//        echo "Upload: " . $_FILES["file"]["name"] . "<br>";
//        echo "Type: " . $_FILES["file"]["type"] . "<br>";
//        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//        echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

                // 保存的文件名字
                if ($rename == null) {
                    $rename = $_FILES[$fileName]["name"];
                }
                // 保存路径
                $saveFile = $location . "/". $rename;
                if (file_exists($saveFile)) {
                    if ($cover) {
                        // 覆盖文件
                        @unlink($saveFile);
                    } else {
                        return $rename . " already exists. ";
                    }
                }
                // 移动文件到指定文件夹upload/
                if (move_uploaded_file($_FILES[$fileName]["tmp_name"], $saveFile)) {
                    return true;
                } else {
                    return "move fail!";
                }
            }
        } else {
            return "Invalid file";
        }
    }
}