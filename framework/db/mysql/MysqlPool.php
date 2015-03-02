<?php
/**
 * Created by xuyi
 */

namespace framework\db\mysql;


use framework\web\App;

class MysqlPool {
    const WRITE_TYPE = 0;
    const READ_TYPE = 1;
    const ALL_TYPE = 2;
    private static $ins_pool = null;
    public static function getIns($type = self::ALL_TYPE) {
        if(self::$ins_pool == null) {
            if(!self::loadConfig())
                return false;
        }
        if(array_key_exists(self::ALL_TYPE, self::$ins_pool)) {
            return self::$ins_pool[self::ALL_TYPE][0];
        }
        else if(array_key_exists($type, self::$ins_pool)){
            return self::$ins_pool[$type][0];
        }
        return false;
    }
    private static function loadConfig() {
        if(!isset(App::$app_config['db']) || !is_array(App::$app_config['db'])) {
            return false;
        }
        self::$ins_pool = array();
        foreach(App::$app_config['db'] as $k => $v) {
            try{
            $pdo = new \PDO($v['connectionString'], $v['username'], $v['password']);
            }
            catch(\Exception $e) {
                echo $e->getMessage();
                return false;
            }
            $pdo->query('set names ' . $v['charset']);
            do{
                if($k === 'write') {
                    $type = self::WRITE_TYPE;
                    break;
                }
                if($k === 'read') {
                    $type = self::READ_TYPE;
                    break;
                }
                $type = self::ALL_TYPE;
            }while(false);
            self::$ins_pool[$type][] = $pdo;
        }
        return true;
    }
} 