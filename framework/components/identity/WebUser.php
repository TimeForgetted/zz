<?php
/**
 * Created by xuyi
 * 用户登录相关
 */

namespace framework\components\identity;


use framework\web\App;

class WebUser implements IUser{
    const KEY_PREFIX = 'SXS_';
    public $is_auto_login = false;
    const USER_COOKIE_INFO = 'user_info';
    const AUTO_LOGIN_TIME = 2592000; //一个月
    public function getStateKeyPrefix() {
        if(isset(App::$app_config['identity']) && isset(App::$app_config['identity']['session_key_prefix'])) {
            return App::$app_config['identity']['session_key_prefix'] . '_';
        }
        return self::KEY_PREFIX;
    }
    public function login(CIdentity $identity)
    {
        // TODO: Implement login() method.
        //先删除之前可能有的cookie
        setcookie(self::USER_COOKIE_INFO, null, time()-1);
        $this->setIdentity($identity->getId(), $identity->username);
        if($this->is_auto_login) {
            $this->saveToCookie($identity->username, $identity->password);
        }


    }
    public function saveToCookie($username, $password, $time = self::AUTO_LOGIN_TIME) {
        $cookie_info = serialize(array('username'=>$username, 'password'=>$password));
        setcookie(self::USER_COOKIE_INFO, $cookie_info, time() + $time);
    }
    public function loginFromCookie() {
        if(!array_key_exists(self::USER_COOKIE_INFO, $_COOKIE) || !isset(App::$app_config['identity']) || !isset(App::$app_config['identity']['user_valid_callback'])) {
            return false;
        }
        $callback_info = App::$app_config['identity']['user_valid_callback'];
        try {
            $user_info = unserialize($_COOKIE[self::USER_COOKIE_INFO]);
            if(!is_array($user_info))
                return false;
            $reflect_class = new \ReflectionClass($callback_info[0]);
            if(!$reflect_class->isSubclassOf('framework\components\identity\CIdentity'))
                return false;
            $callback = $reflect_class->newInstanceArgs($user_info);
            if(!$callback->authenticate()) {
                return false;
            }
            $this->is_auto_login = true;
            $this->login($callback);
            return true;

        }
        catch(\Exception $e) {
            return false;
        }

    }
    public function setIdentity($id, $name) {
       $this->setState('id', $id);
       $this->setState('name', $name);
    }

    public function logout($destory = true)
    {
        // TODO: Implement logout() method.
        setcookie(self::USER_COOKIE_INFO, null, time()-1);
        $this->clearStates();
    }

    public function getState($key, $default_value = null)
    {
        // TODO: Implement getState() method.
        $value = $default_value;
        $key = $this->getStateKeyPrefix() . $key;
        if(array_key_exists($key, $_SESSION)) {
            $value = $_SESSION[$key];
        }
        return $value;
    }

    public function setState($key, $value = null)
    {
        // TODO: Implement setState() method.
        $key = $this->getStateKeyPrefix() . $key;
        if($value == null) {
            unset($_SESSION[$key]);
        }
        else {
            $_SESSION[$key] = $value;
        }
    }

    public function hasState($key)
    {
        // TODO: Implement hasState() method.
        $key=$this->getStateKeyPrefix().$key;
        return isset($_SESSION[$key]);
    }

    public function clearStates()
    {
        // TODO: Implement clearStates() method.
        $keys=array_keys($_SESSION);
        $prefix=$this->getStateKeyPrefix();
        $n=strlen($prefix);
        foreach($keys as $key)
        {
            if(!strncmp($key,$prefix,$n))
                unset($_SESSION[$key]);
        }
    }

    public function getId() {
        return $this->getState('id');
    }
    public function getName() {
        return $this->getState('name');
    }

    public function getIsLogin()
    {
        // TODO: Implement getIsLogin() method.
        return $this->hasState('id');
    }

    public function getIsGuest()
    {
        // TODO: Implement getIsGuest() method.
        return !$this->getIsLogin();
    }
}