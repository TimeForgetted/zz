<?php
/**
 * Created by xuyi
 *
 */

namespace framework\web\urlcreater;


abstract class UrlCreater {
    protected $base_info;
    protected $data;
    public function __construct($base_info, $data) {
        $this->base_info = $base_info;
        $this->data = $data;
    }
    public function setBaseUrl($base_info) {
        $this->base_info = $base_info;
        return $this;
    }
    public function setData($data) {
        $this->data = $data;
    }
    abstract public function createUrl();
} 