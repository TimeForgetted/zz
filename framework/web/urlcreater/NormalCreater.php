<?php
/**
 * Created by xuyi
 * 
 */

namespace framework\web\urlcreater;


class NormalCreater extends UrlCreater {
    /**
     * 提供参数给父类的初始化
     * @param $b --> base_url_info
     * @param $d --> data url的数据
     */
    public function __construct($b, $d) {
        parent::__construct($b, $d);
    }
    public function createUrl()
    {
        $url = '?r=' . $this->base_info;
        if(is_array($this->data) && count($this->data) > 0)
            $url .= '&' . http_build_query($this->data);
        return $url;
    }
}