<?php
/**
 * Created by xuyi
 */

namespace framework\db;


interface ISqlHelper {
    public function getSql();
    public function delete();
    public function select();
    public function update();
    public function insert();
    public function dataToSql();
} 