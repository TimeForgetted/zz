<?php
/**
 * Created by xuyi
 */

namespace framework\model;


interface IModel {
    public function query($sql, $params = null);
    public function find($sql, $param = null);
    public function save($datas, $pk_value = null);
    public function delete($where=null);
    public function deleteByPk($pk_v);
    public function findByPk($pk_v, $fields);
    public function getLastInsertId();
} 