<?php
/**
 * Created by xuyi
 * 初始化项目
 */

class Init {

    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
        \framework\web\App::$request = new \framework\web\Request();
    }

    /**
     * 加载配置文件：
     * 1.得到数据库连接
     * 2.开启session和用户组件
     * @param $config
     * @return $this
     */
    public function loadConfig($config) {
        \framework\web\App::$app_config = $config;
        \framework\web\App::$runtime_config = is_array($config['runtime_config'])?$config['runtime_config']:null;
        //数据库连接处理
        if(isset($config['db_type'])) {
            switch($config['db_type']) {
                case 'mysql':
                    \framework\web\App::$db = \framework\db\mysql\MysqlDb::getMysqlDb();
                    break;
                default:
                    break;
            }
        }

        //开启用户组件
        session_start();
        \framework\web\App::$user = new \framework\components\identity\WebUser();
        if(!\framework\web\App::$user->getIsLogin()) {
            \framework\web\App::$user->loginFromCookie();
        }


        return $this;
    }
    public function run() {
        $dispatcher = null;
        //GET有参数，表示为普通的路由
        if(!empty($_GET)) {
            $route_data = $_GET;
            $dispatcher = new \framework\web\NormalDispatcher();
            $dispatcher->dispatch($route_data);
        }
        else if(strlen($_SERVER['REQUEST_URI']) != 0 && strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['SCRIPT_NAME'])){//否则为特殊的路由方式
            //尚未编写
        }
        else { //如果两者都不满足，则加载默认的控制器
            $dispatcher = new \framework\web\NormalDispatcher();
            if(isset(\framework\web\App::$app_config['defaultController'])) {
                $controller = \framework\web\App::$app_config['defaultController'];
                if(strlen($controller) == 0) {
                    $controller = 'Index';
                }
            }
            else {
                $controller = 'Index';
            }
            $action = 'index'; //默认的方法强制为index
            $dispatcher->exec($controller, $action);
        }

    }

    private function loader($className) {
        include str_replace('\\', '/', $className) . '.php';
    }
} 