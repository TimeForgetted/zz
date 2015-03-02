<?php
/**
 * Created by xuyi
 */

namespace framework\web;


abstract class Dispatcher {
    protected $controller;
    abstract public function dispatch($data);
    protected function doAction($action) {
        if($this->controller->beforeAction()) {
            $this->controller->$action();
        }
    }
    //通过配置信息生成具体的过滤器
    public function generateFilter($filter) {
        if(is_string($filter)) {
            $argv = null;
        }
        else {
            if(count($filter) == 2) {
                $argv = end($filter);
            }
            else {
                $argv = null;
            }
            $filter = $filter[0];
        }
        $class = new \ReflectionClass($filter);
        if($argv == null)
            $con_filter = $class->newInstance();
        else {
            $con_filter = $class->newInstanceArgs($argv);
        }
        return $con_filter;
    }

    /**
     * 执行具体的控制器的行为方法
     * @param $controller
     * @param $action
     */
    public  function exec($controller, $action) {
        //先将控制器和方法转为小写
        $controller = strtolower($controller);
        $action = strtolower($action);
        //将controller和action保存至全局信息
        App::$controller = $controller;
        App::$action = $action;
        $controller = ucfirst($controller) .'Controller';
        $action .= 'Action';
        //生成具体的控制器
        $controller_class = new \ReflectionClass(APP_PATH.'\controllers\\'.$controller);
        if(!$controller_class->isSubclassOf('framework\web\CController')) {
            $this->to404('wrong contrlloer');
        }
        if(!$controller_class->hasMethod($action)) {
            $this->to404('no this method');
        }
        $this->controller = $controller_class->newInstance();

        //执行filter
        //查看有没有该控制器下的filter,有则执行过滤器
        $filters = $this->getActionFilters();
        //如果$filters数组不为空则执行具体的filter
        if(count($filters) > 0) {
            $filter = array_shift($filters);
            try{
                 $first_filter = $this->generateFilter($filter);
            }
            catch(\Exception $e) {
                $this->to404('no such filter');
            }
            $before_filter = $first_filter;
            foreach($filters as $filter) {
                try{
                    $con_filter = $this->generateFilter($filter);
                    $before_filter->setSuccessor($con_filter);
                    $before_filter = $con_filter;
                }
                catch(\Exception $e) {
                    $this->to404('no such filter');
                }
            }
            if(!$first_filter->doFilter()) {
                $this->to404('can not access filter');
            }
        }


        $this->doAction($action);
    }

    private function getActionFilters() {
        //得到具体的控制器和行为
        $controller = APP::$controller;
        $action = App::$action;
        $filters = array();
        try {
            $controller_class = new \ReflectionClass(APP_PATH.'\controllers\\'.ucfirst($controller).'Controller');
            $controller_local_filter_config = $controller_class->getStaticPropertyValue('filter');
            //得到控制器filter配置中*表示的filter
            if($controller_local_filter_config != null) {
                if(array_key_exists('*', $controller_local_filter_config))
                   $filters = self::getFiltersByActionConfig($controller_local_filter_config['*']);
                //得到控制器filter配置中确定action表示的filter
                if(array_key_exists($action, $controller_local_filter_config)) {
                    $filters = array_merge($filters, self::getFiltersByActionConfig($controller_local_filter_config[$action]));
                    $filters = array_unique($filters, SORT_REGULAR);
                }
            }
            //得到配置文件filter配置中*表示的控制器的filter
            if(array_key_exists('*', App::$app_config['filter'])) {
                $filter_config = App::$app_config['filter']['*'];
                if(array_key_exists('*', $filter_config)) {
                    $filters = array_merge($filters, self::getFiltersByActionConfig($filter_config['*']));
                    $filters = array_unique($filters, SORT_REGULAR);
                }
                if(array_key_exists($action, $filter_config)) {
                    $filters = array_merge($filters, self::getFiltersByActionConfig($filter_config[$action]));
                    $filters = array_unique($filters, SORT_REGULAR);
                }
            }
            //得到配置文件filter配置中controller表示的控制器的filter
            if(array_key_exists($controller, App::$app_config['filter'])) {
                $filter_config = App::$app_config['filter'][$controller];
                if(array_key_exists('*', $filter_config)) {
                    $filters = array_merge($filters, self::getFiltersByActionConfig($filter_config['*']));
                    $filters = array_unique($filters, SORT_REGULAR);
                }
                if(array_key_exists($action, $filter_config)) {
                    $filters = array_merge($filters, self::getFiltersByActionConfig($filter_config[$action]));
                    $filters = array_unique($filters, SORT_REGULAR);
                }
            }

           return $filters;


        }
        catch(\Exception $e) {
            $this->to404($e->getMessage());
        }
    }
    //得到行为级别的过滤器
    private static function  getFiltersByActionConfig($config) {
        $filters = array();
        do{
            if(is_string($config)) {
                $filters[] = array($config);
                break;
            }
            if(is_array($config)) {
                if(!self::hasFilters($config)) { //如果数组是单个过滤器
                    $filters[] = self::getFilterOfHasArg($config);
                }
                else { //如果数组由多个过滤器组成
                    foreach($config as $v)
                        $filters[] = self::getFilterOfHasArg($v);
                }
            }
        }while(false);
        return $filters;
    }

    /**
     * 判断 一个行为级别的过滤数组有没有多个过滤器
     * eg:array('xxxFilter', 1,2,3)这样的表示为单个filter
     * array(array('xxxFilter'), array('xxxxFilter', 1,2 3))为多个filter
     */
    private static function hasFilters($config) {
        //当config不是数组是字符串的时候，理解为一个过滤器 eg: 'xxxxFilter'
        if(!is_array($config))
            return false;
        //当$config的长度为2 的时候且[0]为字符串,[1]为一个不带filter的数组，则理解为[0]的参数,如 array('xxxFilter', array(1,2,3,4))
        if(count($config) == 2) {
            if(is_string($config[0])) {
                if(is_array($config[1])) {
                    try{
                        @$reflectclass = new \ReflectionClass($config[1][0]);
                    }catch (\Exception $e) {
                        return false;
                    }
                }
            }
        }
        foreach($config as $v) {
            if(is_array($v))
                return true;
        }
        return false;
    }

    private static function getFilterOfHasArg($config) {
        if(is_string($config)){
            return array($config);
        }
        $filter_name = array_shift($config);
        $temp_config = array();
        $temp_config[] = $filter_name;
        if(!empty($config))
            $temp_config[] = $config;
        return $temp_config;
    }



    public  function redirect($controller, $action,$data=null, $terminate = true, $statusCode = 302) {
        header('Location: ' . $this->createUrl($controller.'/' . $action, $data), true, $statusCode);
        if($terminate == true) {
            App::end();
        }
    }
    public function to404($info = null) {
        if(isset(App::$app_config['default404']) && is_array(App::$app_config['default404'])) {
            $controller = !empty(App::$app_config['default404']['controller'])?App::$app_config['default404']['controller']:'site';
            $action = !empty(App::$app_config['default404']['action'])?App::$app_config['default404']['action']:'error';
        }
        else {
            $controller = 'site';
            $action = 'error';
        }
        $this->redirect($controller, $action, $info == null?null:array('info'=>$info));
    }
    public function createUrl($route_info, $data = null) {
        $base_url = $_SERVER['SCRIPT_NAME'] . '?r=' . $route_info;
        if(is_array($data)) {
            $base_url .= '&' . http_build_query($data);
        }

        return $base_url;
    }
} 