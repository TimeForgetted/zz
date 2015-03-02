<?php
/**
 * Created by xuyi
 */

namespace framework\web;


class App {
    const DEFAULT_VIEW_SUFFIX = '.html';
    public static $app_config ; //全局的配置文件
    public static $runtime_config; //用户自定义的配置
    public static $request;  //请求信息
    private static $get_data = array(); //get方法来的变量，主要为特殊路由而提供的
    public static $db;
    public static $user;
    public static $controller;
    public static $action;
    public static  function addGetData($key, $value) {
        self::$get_data[$key] = $value;
    }
    public static  function setGetData($data) {
        self::$get_data = $data;
    }
    public static function getGetData($key) {
        if(!isset(self::$get_data[$key]))
            return false;
        return self::$get_data[$key];
    }
    public static function getAllGetData() {
        return self::$get_data;
    }
    public static function end() {
        exit;
    }
    //得到文件路径
    public static function getBasePath() {
        return ABSOLUTE_ROUTE . '/' . APP_PATH;
    }
} 