<?php
/**
 * Created by xuyi
 */

namespace framework\components\validate;


class Validater {
    /*
        config形如：
        array(key1, rule, msg)
    */
    protected $config = array();
    public function __construct($config = null) {
        if(is_array($config)) {
            $this->config = $config;
        }
    }

    public function __set($name, $value) {
        if($name == 'config') {
            if(is_array($value)) {
                $this->config = $value;
            }
        }
    }
    /*
        $data 需要验证的数据，形如
        array(
            k1=>v1,
            k2=>v2
            ....
        )
    */
    public function valid($data) {
        foreach($this->config as $v) {
            if(array_key_exists($v[0], $data)) {
                $rule_info = explode(',', $v[1]);
                //反射类
                $rule_class = new \ReflectionClass($rule_info[0]);
                array_shift($rule_info); //删除数组中第1个元素
                $rule = $rule_class->newInstanceArgs($rule_info);//索引数组传值
                if(!$rule->valid($data[$v[0]])) {
                    return $v[2];  //返回错误信息
                }
            }
        }
        return true;
    }

}