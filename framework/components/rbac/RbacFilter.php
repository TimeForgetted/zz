<?php
/**
 * Created by xuyi
 */

namespace framework\components\rbac;


use framework\web\App;
use framework\web\Filter;

abstract class RbacFilter extends Filter {
    public function doFilter() {
        $config = App::$app_config['rbac'];
        $confighandler = new RbacConfigHandler($config);
        $concrete_level = $confighandler->getLevel();
        if($concrete_level !== -1 && $concrete_level !=  $this->getRoleLevel()){
            return false;
        }
        return parent::doFilter();
    }

    //得到当前用户的级别
    abstract function getRoleLevel();
} 