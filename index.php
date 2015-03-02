<?php
//欢迎使用zz框架
//app的入口文件
header('Content-Type:text/html;charset=UTF-8');
date_default_timezone_set('PRC');
define('ABSOLUTE_ROUTE', str_replace('\\', '/', dirname(__FILE__)));
define('APP_PATH', 'app');
include ABSOLUTE_ROUTE.'/framework/lib/Init.php';

$app_config = include ABSOLUTE_ROUTE.'/app/config/main.php';
$initer = new Init();
$initer->loadConfig($app_config)->run();