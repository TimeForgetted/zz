<?php
/**
 * Created by xuyi
 */

namespace framework\db;



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