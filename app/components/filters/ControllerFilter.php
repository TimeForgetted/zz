<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/5
 * Time: 下午11:14
 */
namespace app\components\filters;

use framework\web\App;
use framework\web\Filter;

class ControllerFilter extends  Filter{
    const CONTROLLER_CLASS_PATH = 'app\controllers';
    public function doFilter()
    {
        $class_path = self::CONTROLLER_CLASS_PATH . ucfirst(App::$controller) . 'Controller';
        $class = new \ReflectionClass($class_path);
        $controller_config = $class->getStaticPropertyValue('controller_config');
        if (!is_array($controller_config) || !is_array_key_exits(App::$action, $controller_config))
            return parent::doFilter();
        $action_config = $controller_config[App::$action];
        if (!$this->checkAction($action_config)) {
            return false;
        }
        return parent::doFilter();
    }
    public function checkAction($action_config)
        {
            $request_type = self::getConfig($action_config, 'requestType');
            if ($request_type !== null)
                $request_type = strtoupper($request_type);
            $res = true;
            do {
                if ($request_type === 'GET') {
                    $res = App::$request->getIsGetRequset();
                    break;
                }
                if ($request_type === 'POST') {
                    $res = App::$request->getIsPostRequset();
                    break;
                }
                if ($request_type === 'AJAX') {
                    $res = App::$request->getIsAjaxRequset();
                    break;
                }
            } while (false);
            if (!$res)
                return false;
            $get_arg_keys = self::getConfig($action_config, 'getArg');
            if ($get_arg_keys != null) {
                $get_arg_arr = explode(',', $get_arg_keys);
                $diffs = array_diff($get_arg_arr, array_keys(App::getAllGetData()));
                if (!empty($diffs)) {
                    $res = false;
                }
                if (!$res) {
                    return false;
                }
                return true;
            }
        }
    public static function getConfig($configs, $key) {
        if(!array_keys_exists($key, $configs)) {
            return null;
        }
        return $configs[$key];
    }
}