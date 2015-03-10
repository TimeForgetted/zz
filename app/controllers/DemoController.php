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
    public $test = 5;
    public function tAction() {
        echo 123;
    }
}