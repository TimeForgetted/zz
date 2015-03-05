<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/5
 * Time: 上午9:58
 */

//分页组件

namespace app\components;

class Pagination {
    private $count;         //总个数
    private $pageSize;      //每一页个数
    private $currentPage;   //当前页面
    private $offset;        //每一页个数
    private $limit;         //每页项目书
    private $pageNum;       //总页数
    private $isLast;        //是否最后一页
    private $isFirst;       //是否首页

    public  function  __construct($count) {
        $this->count    = $count;
        $this->isFirst  = false;
        $this->isLast   = false;
        $this->pageSize = null;
    }

    public function  __set($name, $value) {
        do{
            if($name == 'pageSize' && is_int($value)) {
                $this->pageSize = $value;
                $this->calcPage();
            }
        }while(false);
    }
    public function __get($name) {
        if($this->pageSize == null)
            return null;
        return $this->$name;
    }
    private function calcPage() {
        if($this->pageSize == null)
            return false;
        $this->pageNum = ceil($this->count / $this->pageSize);
        if(isset($_GET['page']) && is_numeric($_GET['page']) ) {
            $page = $_GET['page'] + 0;
        }else {
            $page = 1;
        }
        $this->currentPage = $page;

        $this->limit = $this->pageSize;
        $this->offset = ($page - 1 ) * $this->limit;
        if($page >= $this->pageNum) {
            $this->isLast = true;
        }
        if($page == 1) {
            $this->isFirst = true;
        }
    }
}