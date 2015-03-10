<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/2
 * Time: 下午4:38
 */

namespace app\components;
use framework\web\App;
use framework\web\CController;

class Controller extends CController{
    public $layout = null; //首尾分离用
    public $html_title = "a demon for zz framework";
    public $navigation_title = "demon";

    public function getBaseUrl($str =null,$absolute = false){
        $path = App::$request->getBaseUrl($absolute);
        switch($str){
            case 'js'       : $path .= AppConstant::PATH_JS;break;
            case 'css'      : $path .= AppConstant::PATH_CSS;break;
            case 'img'      : $path .= AppConstant::PATH_IMG;break;
            case 'script'   : $path .= '/index.php';break;
        }
        return $path;
    }

}