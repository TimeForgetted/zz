<?php
/**
 * Created by xuyi
 * 连接MySql数据库
 */

namespace framework\db\mysql;


use framework\db\Db;

class MysqlDb extends Db {
    protected $ins ;
    protected $ins_type = null;
    protected $error ;
    private static $mysqldb;

    private function  __construct() {

    }
    public static function getMysqlDb() {
        if(!self::$mysqldb instanceof MysqlDb) {
            self::$mysqldb = new MysqlDb();
        }
        return self::$mysqldb;
    }

    public static function getReadIns() {
       return MysqlPool::getIns(MysqlPool::READ_TYPE);
    }

    public static function getWriteIns() {
        return MysqlPool::getIns(MysqlPool::WRITE_TYPE);
    }

    public function query($sql, $params = null)
    {
        if($this->ins_type !== MysqlPool::READ_TYPE)
            $this->ins = self::getReadIns();
        $this->ins_type = MysqlPool::READ_TYPE;
        if($params == null || !is_array($params)) {
            $stmt =  $this->ins->query($sql);
            if(!$stmt) {
                return false;
            }
            else {
                $this->error = $this->ins->errorInfo();
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
        else {
            $stmt =  $this->ins->prepare($sql);
            if(!$stmt) {
                return false;
            }
            else {
                $stmt->execute($params);

                $this->error = $stmt->errorInfo();
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
    }

    public function exec($sql, $params = null)
    {
        // TODO: Implement exec() method.
        if($this->ins_type !== MysqlPool::WRITE_TYPE)
            $this->ins = self::getWriteIns();
        $this->ins_type = MysqlPool::WRITE_TYPE;
        if($this->ins == null)
            return false;
        if($params == null || !is_array($params)) {
            $res = $this->ins->exec($sql);
            if($res === false) {
                $this->error = $this->ins->errorInfo();
                return false;
            }
            else
                return true;
        }
        else {
            $stm = $this->ins->prepare($sql);
            $result = false;
            if( $stm && $stm->execute($params) ) {
                $result = $stm->rowCount();
            }
            if($result === false) {
                $this->error = $stm->errorInfo();
            }
            return $result;
        }
    }

    public function delete($table, $where)
    {
        // TODO: Implement delete() method.
        if($this->ins_type !== MysqlPool::WRITE_TYPE)
            $this->ins = self::getWriteIns();
        $this->ins_type = MysqlPool::WRITE_TYPE;
        if($where == null || !is_array($where)) {
            return false;
        }
        $sql = "delete from " . $table . " where %s";
        $where_str = $occ_str = implode("=? and ", array_keys($where)) . "=?";
        $f_sql = sprintf($sql, $where_str);
        return $this->exec($f_sql, array_values($where));
    }

    /**
     *功能：插入或者更新数据，有主键-更新；无主键-插入
     * 参数：$table-表名；datas:要更新或增加的数据（关联数组），pk主键
     * 返回：关联数组数据为空，返回false,成功返回受影响的行数
     **/
    public function save($table, $datas, $pk_name = null, $pk_value=null) {
        if($datas == null || !is_array($datas) )
            return false;
        $keys = array_keys($datas);
        $values = array_values($datas);
        //如果主键为空，则为插入，否则为更新
        if($pk_name == null || $pk_value == null) {
            $insert_sql = 'insert into ' . $table . ' (%s) values (%s) ';
            $key_str =  implode(",", $keys) ;
            $occ_str = implode(',', array_fill(0, count($datas), '?'));
            $final_sql = sprintf($insert_sql, $key_str, $occ_str);
        }
        else {
            $update_sql = 'update ' . $table . ' set %s where ' . $pk_name . ' = ?';
            $occ_str = implode("=?,", $keys) . "=?";
            $final_sql = sprintf($update_sql, $occ_str);
            $values[] = $pk_value;
        }
        return $this->exec($final_sql, $values);
    }

    public function getError()
    {
        // TODO: Implement getError() method.
        return $this->error;
    }

    public function getLastInsertId()
    {
        // TODO: Implement getLastInsertId() method.
        return self::getWriteIns()->lastInsertId();
    }
}