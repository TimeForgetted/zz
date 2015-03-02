<?php
/**
 * Created by xuyi
 * 这个调度器不做具体调度，而是为其他类提供调度抽象类的方法
 */

namespace framework\web;


class BaseDispatcher extends Dispatcher {

    public function dispatch($data)
    {
        return false;
    }
}