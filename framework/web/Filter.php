<?php
/**
 * Created by xuyi
 */

namespace framework\web;


abstract class Filter extends Controller {
    protected $successor = null;
    public function setSuccessor(Filter $successor) {
        $this->successor = $successor;
    }
    public function doFilter() {
        if($this->successor != null) {
            return $this->successor->doFilter();
        }
        else {
            return true;
        }

    }
} 