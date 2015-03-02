<?php
/**
 * Created by xuyi
 */

namespace framework\web;


use framework\web\urlcreater\NormalCreater;

class Controller {
    /**
     * 跳转到其他控制器
     * @param string $base_info  包含控制器和方法的基本信息
     * @param null $data 数据
     * @param bool $absolute 是否需要绝对的路径
     * @param bool $terminate 跳转后立即停止
     * @param int $statusCode 状态码
     */
    public function redirect($base_info, $data = null,$absolute = true, $terminate = true, $statusCode = 302 ) {
        if($absolute)
            $url = $this->createAbsoluteUrl($base_info, $data);
        else {
            $url = $this->createUrl($base_info, $data);
        }
        header('Location: ' . $url, true, $statusCode);
        if($terminate) {
            App::end();
        }
    }

    /**
     * controller -->控制器
     * action -->方法
     * 向另一个控制器和方法请求资源
     */
    public function forward($controller, $action) {
        $dispatcher = new BaseDispatcher();
        $dispatcher->exec($controller, $action);
    }

    /**
     * base_info指的是控制器和方法 eg: index/index
     * data代表数据用数组来传递
     * @param $base_info
     * @param $data
     */
    public function createUrl($base_info, $data = null) {
        $urlcreater = new NormalCreater($base_info, $data);
        $url = App::$request->getScriptUrl();
        $url .= $urlcreater->createUrl();
        return $url;
    }

    /**
     * 参数和createUrl一样
     * @param $base_info
     * @param null $data
     */
    public function createAbsoluteUrl($base_info, $data = null) {
        $url = $this->createUrl($base_info, $data);
        $url = App::$request->getHostInfo() . $url;
        return $url;
    }
} 