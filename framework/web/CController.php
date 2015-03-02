<?php
/**
 * Created by xuyi
 */

namespace framework\web;




class CController extends Controller {
    public static $controller_config = null;
    public static $filter = null;
    protected $layout = false;
    public function beforeAction() {
        return true;
    }
    public function render($_view, $data = null, $return = false) {
        $view_suffix = isset(App::$app_config['view_suffix'])?App::$app_config['view_suffix']:App::DEFAULT_VIEW_SUFFIX;
        if(is_array($_view)) {
            $controller_path = $_view[0];
            $_view = $_view[1];
        }
        else {
            $controller_path = App::$controller;
        }
        $result = false;
        if(is_string($_view)) {
            $view_path = App::getBasePath() . '/views/' . $controller_path . '/' .$_view . $view_suffix;
            $layout_path = App::getBasePath() . '/views/layouts/' . $this->layout . $view_suffix;
            if($this->layout == false) {
                $result =  $this->renderView($view_path, $data, $return);
            }
            else {
                $content = $this->renderView($view_path, $data, true);
                $result = $this->renderView($layout_path, array('content'=>$content), $return);
            }
        }
        return $result;
    }
    public function renderView($view, $_data=null, $return = false) {
        if(is_array($_data))
            extract($_data,EXTR_PREFIX_SAME,'data');
        else
            $data=$_data;
        if($return)
        {
            ob_start();
            ob_implicit_flush(false);
            require($view);
            return ob_get_clean();
        }
        else
            require($view);
        return true;
    }
} 