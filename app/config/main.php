<?php
/**
 * Created by xuyi
 * App所有的配置文件
 */

return array(
    'defaultController'=>'Index',
    'default404'=>array('controller'=>'', 'action'=>''),
    'debug'=>true,
    /**
     * 目的是在控制器的方法执行前执行过滤
     * eg: array('controller'=>'action'=>'filter')
     *   'action': string means a action , '*' means all , xxx,xxx,xxx means some action
     *  'controller' : same as action
     *
     * array('controller'=>array('action'=>array('\app\components\filtersNormalFilter')))
     *
     */
    'filter'=>array(
        /* '*'=>
            //角色控制过滤器 还未添加
             array('*'=>'app\components\filters\RbacFilter'
             ),*/
        '*'=>array(
            '*'=>'app\components\filters\NormalFilter'
          )
    ),
    /**
     * 特殊路由
     * eg:array('controller', 'action', )
     */
    'route'=>array(
        array()
    ),
    /**
     * 用户自定义的配置
     */
    'runtime_config'=>array(

    ),
    /*
     * db是数据库的配置
     * eg: type=>mysql,connectString=>xxxxxx
     */
    'db_type'=>'mysql',
    'db'=>array(
        array(
            'connectionString' => 'mysql:host=localhost;dbname=demon',
            'username' => 'xuyi',
            'password' => 'xu123yi',
            'charset' => 'utf8',
        )
    ),

    'identity'=>array(
        'user_valid_callback'=>array('app\components\identity\UserIdentity', 'validUser')
        //'session_key_prefix'=>''
    ),
    'rbac'=>array(
        /*1=>array('index'=>'r1,r2,r3,t5')*/
    ),
    'view_suffix'=>'.php'
);