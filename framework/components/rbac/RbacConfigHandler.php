<?php
/**
 * Created by xuyi
 */

namespace framework\components\rbac;


use framework\web\App;

class RbacConfigHandler {
    private $config ;
    public function __construct($config) {
       $this->config = $config;
    }
    //根据配置和请求的控制器和方法得到该控制器和方法的优先级
    public function getLevel() {
        $get_level = null;
        foreach($this->config as $level => $value) {
            foreach($value as $controller => $actions) {
                if(self::equalController($controller)) {
                    if(self::equalAction($actions)) {
                        $get_level = $level;
                    }
                }
            }
        }
        if($get_level === null) {
            $get_level = -1;
        }
        return $get_level;
    }
    //判断控制器是否符合
    private static function equalController($controller) {
        if($controller === '*' || strtolower($controller) === strtolower(App::$controller))
            return true;
        return false;
    }
    //判断方法是否符合
    private static function equalAction($action) {
        if($action === '*')
            return true;
        $actions = explode(',', $action);
        if(in_array(App::$action, $actions))
            return true;
        return false;
    }

} 