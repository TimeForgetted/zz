<?php
/**
 * Created by xuyi
 */

namespace framework\db;


interface IDb {
    public function query($sql);
    public function exec($sql);
    public function delete($table, $where);
    public function getError();
    public function getLastInsertId();
} 