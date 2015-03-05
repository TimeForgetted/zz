<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/3
 * Time: 上午10:55
 */
namespace app\models;

use\app\components\Model;

class Red_Heart extends Model {
    protected $table    = "red_heart";//todo 表名需要修改
    protected $pk_name  = "sid";

    public function collect($user_id, $type, $type_id) {
        $data = array();
        $data['user_id']    = $user_id;
        $data['type']       = $type;
        $data['type_id']    = $type_id;
        if( !$this->save($data) )
            return false;
        return true;
    }

    public function isCollect($type, $user_id, $type_id) {
        $sql    = 'select count(*) as num from red_heart where stype = ? and user_id = ? and stype_id = ? limit 1';
        $info   = $this->find($sql, array($type, $user_id, $type_id));
        return $info['num']+0>0 ?true:false;
    }

}