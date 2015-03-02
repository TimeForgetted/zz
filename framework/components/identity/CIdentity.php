<?php
/**
 * Created by xuyi
 */

namespace framework\components\identity;


abstract class CIdentity {
    protected $username;
    protected $password;
    public function __get($name) {
        return $this->$name;
    }
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    abstract public function  authenticate();
    abstract public function getId();
} 