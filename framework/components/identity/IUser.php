<?php
/**
 * Created by xuyi
 */

namespace framework\components\identity;


interface IUser {
    public function login(CIdentity $identity);
    public function logout();
    public function getState($key, $default_value = null);
    public function setState($key, $value = null);
    public function hasState($key);
    public function clearStates();
    public function getIsLogin();
    public function getIsGuest();
} 