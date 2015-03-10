<?php
/**
 * Created by xuyi
 */

namespace framework\model;


use framework\db\SqlHelper;
use framework\web\App;

abstract class Model implements IModel{
    protected $db;
    protected $table;
    protected $pk_name;
    public function __construct() {
        $this->db = App::$db;
    }
    //todo SqlHelper 尚未完成 只写了接口,使用的时候还是使用Sql语句*:)
    public function query($sql, $params = null)
    {
        if($sql instanceof SqlHelper)
            $sql = $sql->getSql();
        return $this->db->query($sql, $params);
    }

    public function find($sql, $param = null)
    {
        $res = $this->query($sql, $param);
        if($res == false || count($res) == 0) {
            return false;
        }
        return $res[0];
    }

    public function save($datas, $pk_value = null)
    {
        return $this->db->save($this->table, $datas, $this->pk_name, $pk_value);
    }

    public function delete($where = null)
    {
        return $this->db->delete($this->table, $where);
    }

    public function deleteByPk($pk_v)
    {
        return $this->db->delete($this->table, array($this->pk_name=>$pk_v));
    }

    public function  findByPk($pk_v, $fields)
    {
        $sql = sprintf("select %s from %s where %s = ? ", implode(',', $fields), $this->table, $this->pk_name);
        $res = $this->db->query($sql, array($pk_v));
        if($res == null || count($res) == 0)
            return false;
        return $res[0];
    }

    public function getLastInsertId()
    {
        return $this->db->getLastInsertId();
    }
    public function getError() {
        return $this->db->getError();
    }


} 