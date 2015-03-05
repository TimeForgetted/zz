<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/2
 * Time: 下午4:39
 */

namespace app\models;

use framework\model\Model;

class Intern extends Model {
    protected $table    = 'intern';
    protected $pk_name  = 'sid';

    public function getInternCount($keyword , $city = null){
        //构建基本的sql
        $base_sql = 'select count(*) a num from '.$this->table.' %s ';
        //查询sql
        $query_sql = '';
        $query_arr = array();
        if( !empty($keyword) ){
            $connect_word = $city==null;
            $query_sql = "where (name like ?or com_name like?) ${connect_word} city like ?";
            $query_arr = array('%'.$keyword.'%', '%'.$keyword.'%', '%'.($city==null?$keyword:$city).'%');
        }
        $sql        = sprintf($base_sql, $query_sql);
        $count_info = $this->find($sql, $query_arr);
        $count      = $count_info['num'] + 0;
        return $count;
    }
    /**
     * @param $keyword 关键字，如果为null表示没有关键字
     * @param $offset
     * @param $limit
     */
    public function getInternList($keyword, $offset, $limit,$city=null) {
        $base_sql   = "select sid, name, com_id, com_name from T_Intern %s order by add_time desc limit ${offset}, ${limit}";
        $query_sql  = "";
        $query_arr  = array();
        if ($keyword != null ) {
            $connect_word   = $city==null?'or':'and';
            $query_sql      = "where (name like ? or com_name like ?) ${connect_word} city like?";
            $query_arr      = array('%'.$keyword.'%', '%'.$keyword.'%', '%'.($city==null?$keyword:$city).'%');
        }
        $sql = sprintf($base_sql,$query_sql);
        $interns = $this->query($sql , $query_arr);
        return $interns;
    }
    public function getInterDetail($intern_id) {
        $sql = "";

    }
    public function getUserInternStatus($user_id, $intern_id) {
        $sql = 'select count(*) as num from resume_deliver where user_id = ? and intern_id = ? limit 1';
        $info = $this->find($sql, array($user_id, $intern_id));
        return $info['num'] + 0 == 0?2:3;
    }
    public function getInterInfoByInterId($inter_id) {
        $sql = "SELECT com_id,com_name,minsalary,maxsalary,dayperweek,city,degree,user_id,name from intern  WHERE intern.sid = ?';";
        $res = $this->find($sql, array($inter_id));
        return $res;
    }

}