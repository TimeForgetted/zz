<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/5
 * Time: 下午11:12
 */
namespace app\components\filters;

use framework\web\Filter;

class NormalFilter extends  Filter {
    public function  doFilter() {
        if(1 == 1) {
            return parent::doFilter();
        }
        else {
            return false;
        }
    }
}