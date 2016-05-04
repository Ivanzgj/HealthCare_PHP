<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/4/23
 * Time: 14:34
 */

class Database {
    private $connection;
    private $builder;

    public function __construct() {
        $this->connection = Connection::getConnection();
        $this->builder = null;
    }

    /**
     * 获得数据库查询工具
     * @return null|QueryBuilder
     */
    public function query() {
        if ($this->builder == null) {
            $this->builder = new QueryBuilder($this->connection);
        }
        return $this->builder;
    }

    public function __destruct()
    {
        $this->builder = null;
        $this->connection->close();
        $this->connection = null;
    }
}

/**
 * Class Connection 保持数据库连接类
 */
require_once "Configurations.php";
class Connection {
    private static $host = Configurations::LOCALHOST;
    private static $username = Configurations::USER;
    private static $password = Configurations::PASSWORD;
    private static $databaseName = Configurations::DATABASE;
    /**
     * @var mysqli
     */
    private static $conn;

    /**
     * 获得sqlite连接
     * @return bool|mysqli 若连接失败则返回false， 否则返回sqlite连接
     */
    public static function getConnection() {
        if (self::$conn != null) {
            return self::$conn;
        }
        self::$conn = new mysqli(self::$host, self::$username, self::$password);
        if (self::$conn->connect_error) {
            return false;
        }
        self::$conn->select_db(self::$databaseName);
        return self::$conn;
    }

    public static function close() {
        if (self::$conn != null) {
            self::$conn->close();
        }
    }
}

/**
 * Class QueryBuilder 数据库查询构造器
 */
class QueryBuilder {

    private $tableName;
    private $fields;
    private $insertFields;
    private $insertValues;
    private $whereString;
    private $updateString;
    private $connection;
    private $orderString;

    /**
     * QueryBuilder constructor.
     * @param $conn mysqli
     */
    public function __construct($conn) {
        $this->connection = $conn;
        $this->flush();
    }

    private function flush() {
        $this->fields = NULL;
        $this->insertFields = NULL;
        $this->insertValues = NULL;
        $this->whereString = NULL;
        $this->updateString = NULL;
        $this->orderString = NULL;
    }
    /**
     * 添加查询表
     * @param $table string 表名
     * @return self 对象自身
     */
    public function table($table) {
        $this->tableName = $table;
        return $this;
    }

    /**
     * 添加select语句的查询字段
     * @param $field string 查询字段名
     * @return self 对象自身
     */
    public function field($field) {
        if ($this->fields == NULL) {
            $this->fields = $field;
        } else {
            $this->fields .= ",". $field;
        }
        return $this;
    }

    /**
     * 为insert语句添加字段及值
     * @param $field string 字段名
     * @param $value string|int|float 字段值
     * @param $type string 数据类型
     * @return self 对象自身
     */
    public function add($field, $value, $type) {
        if ($this->insertFields == NULL) {
            $this->insertFields = $field;
        } else{
            $this->insertFields .= ",". $field;
        }
        if (gettype($value) == gettype(NULL)) {
            $value = "null";
        }else if (strcmp($type, "string") == 0) {
            $value = "'".strval($value)."'";
        }
        if ($this->insertValues == NULL) {
            $this->insertValues = $value;
        } else {
            $this->insertValues .= ",".$value;
        }
        return $this;
    }

    /**
     * 为update语句添加字段及值
     * @param $field string 字段名
     * @param $value string|int|float 字段值
     * @param $type string 数据类型
     * @return self 对象自身
     */
    public function set($field, $value, $type) {
        if (gettype($value) == gettype(NULL)) {
            $value = "null";
        }else if (strcmp($type, "string") == 0) {
            $value = "'".strval($value)."'";
        }
        if ($this->updateString == NULL) {
            $this->updateString = $field."=".$value;
        } else {
            $this->updateString .= ",".$field."=".$value;
        }
        return $this;
    }

    /**
     * 添加where子句
     * @param $field string 字段名
     * @return $this self 对象自身
     */
    public function where($field) {
        if ($this->whereString == NULL) {
            $this->whereString = "where ".$field;
        } else {
            $this->whereString .= " and ".$field;
        }
        return $this;
    }

    /**
     * 添加where or子句
     * @param $field string 字段名
     * @return $this self 对象自身
     */
    public function whereOr($field) {
        if ($this->whereString == NULL) {
            $this->whereString = "where ".$field;
        } else {
            $this->whereString .= " or ".$field;
        }
        return $this;
    }

    /**
     * @param $value string|int|float
     * @return self 对象自身
     */
    public function eq($value, $type) {
        if (strcmp($type, "string")) {
            $this->whereString .= "=" . strval($value);
        } else {
            $this->whereString .= "=" . "'".strval($value)."'";
        }
        return $this;
    }

    /**
     * @param $value string|int|float
     * @return self 对象自身
     */
    public function less($value) {
        $this->whereString .= "<".strval($value);
        return $this;
    }

    public function order($ord) {
        $this->orderString = $ord;
        return $this;
    }

    /**
     * @param $value string|int|float
     * @return self 对象自身
     */
    public function large($value) {
        $this->whereString .= ">".strval($value);
        return $this;
    }

    /**
     * @param $limit int 限制个数，默认0为不限制
     * 执行select语句
     * @return array 查询结果数组
     */
    public function select($limit = 0) {
        $selectString = "select " . $this->fields . " from " . $this->tableName;
        if ($this->whereString != NULL) {
            $selectString .= " ".$this->whereString;
        }
        if ($this->orderString != NULL) {
            $selectString .= " order by ".$this->orderString;
        }
        if ($limit != 0) {
            $selectString .= " limit ".strval($limit);
        }
        $this->flush();
        $result = $this->connection->query($selectString);
        $arr = array();
        $i = 0;
        while ($data = $result->fetch_assoc()) {
            $arr[$i] = $data;
            $i ++;
        }
        $result->close();
        return $arr;
    }

    /**
     * 执行insert操作
     * @return bool 操作结果
     */
    public function insert() {
        $insertString = "insert" . " into " . $this->tableName . " (" . $this->insertFields . ") values (" . $this->insertValues . ")";
        $this->flush();
        return $this->connection->query($insertString);
    }

    /**
     * 执行update操作
     * @return bool 操作结果
     */
    public function update() {
        $upString = "update " . $this->tableName . " set " . $this->updateString;
        if ($this->whereString) {
            $upString .= " " . $this->whereString;
        }
        $this->flush();
        return $this->connection->query($upString);
    }

    /**
     * 执行delete操作
     * @return bool 操作结果
     */
    public function delete() {
        $delString = "delete from " . $this->tableName . " ";
        if ($this->whereString != NULL) {
            $delString .= $this->whereString;
        }
        $this->flush();
        return $this->connection->query($delString);
    }
}