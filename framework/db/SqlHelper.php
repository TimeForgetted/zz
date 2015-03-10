<?php
/**
 * Created by xuyi
 */

namespace framework\db;


//设计用于实现Active Record功能
abstract class SqlHelper implements ISqlHelper{
    public $table;
    public $where;
    public $select;
    public $limit;
    public $offset;
    public $order;
    public $join_type;
    public $join;
    public $data;
}