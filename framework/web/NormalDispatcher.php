<?php
/**
 * Created by xuyi
 * 待完善
 */

namespace framework\web;
use app\controllers;


class NormalDispatcher extends Dispatcher  {

    public function dispatch($data)
    {

        if(!isset($data['r'])){
            $this->to404();
        }
        $delimit = strpos($data['r'], '/') !== false ?'/':'\\';
        $base_data = explode($delimit, $data['r']);
        unset($data['r']);
        App::setGetData($data);
        if(count($base_data) != 2) {
            $this->to404();
        }
        list($controller, $action) = $base_data;
        $this->exec($controller, $action);
    }
}