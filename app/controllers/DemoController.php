<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/5
 * Time: 上午11:43
 */

namespace app\controllers;


use app\components\Controller;

class DemoController extends Controller{
    public $layout = null;
    public $test = 5;
    public function tAction() {
        $this->render('index');
    }
    public function iAction() {
        $this->render('iphone');
    }
}