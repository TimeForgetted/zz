<?php

namespace framework\web;


class Request {


    private $_scriptUrl;

    public function __construct() {
        $this->getRequestUri();
    }
    private $_baseUrl;
    private $_requestUri;
    private $_scriptFile;
    private $_port;
    private $_hostInfo;


    public function __get($name) {
        return $this->$name;
    }
    public function getRequestUri()
    {

        if(isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
            $this->_requestUri=$_SERVER['HTTP_X_REWRITE_URL'];
        elseif(isset($_SERVER['REQUEST_URI']))
        {
            $this->_requestUri=$_SERVER['REQUEST_URI'];
            if(!empty($_SERVER['HTTP_HOST']))
            {
                if(strpos($this->_requestUri,$_SERVER['HTTP_HOST'])!==false)
                    $this->_requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$this->_requestUri);
            }
            else
                $this->_requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$this->_requestUri);
        }
        elseif(isset($_SERVER['ORIG_PATH_INFO']))  // IIS 5.0 CGI
        {
            $this->_requestUri=$_SERVER['ORIG_PATH_INFO'];
            if(!empty($_SERVER['QUERY_STRING']))
                $this->_requestUri.='?'.$_SERVER['QUERY_STRING'];
        }
        //echo $this->_requestUri;
        return $this->_requestUri;
    }
    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'';
    }

    public function getIsGetRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'GET');
    }

    public function getIsPostRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST');
    }

    public function getIsAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }
    /**
     * Returns the server name.
     * @return string server name
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the server port number.
     * @return integer server port number
     */
    public function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Returns the URL referrer, null if not present
     * @return string URL referrer, null if not present
     */
    public function getUrlReferrer()
    {
        return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    }

    /**
     * Returns the user agent, null if not present.
     * @return string user agent, null if not present
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
    }

    /**
     * Returns the user IP address.
     * @return string user IP address
     */
    public function getUserHostAddress()
    {
        return isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
    }

    /**
     * Returns the user host name, null if it cannot be determined.
     * @return string user host name, null if cannot be determined
     */
    public function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:null;
    }

    /**
     * Returns entry script file path.
     * @return string entry script file path (processed w/ realpath())
     */
    public function getScriptFile()
    {
        if($this->_scriptFile!==null)
            return $this->_scriptFile;
        else
            return $this->_scriptFile=realpath($_SERVER['SCRIPT_FILENAME']);
    }

    /**
     * Returns user browser accept types, null if not present.
     * @return string user browser accept types, null if not present
     */
    public function getAcceptTypes()
    {
        return isset($_SERVER['HTTP_ACCEPT'])?$_SERVER['HTTP_ACCEPT']:null;
    }
    /**
     * Returns the port to use for insecure requests.
     * Defaults to 80, or the port specified by the server if the current
     * request is insecure.
     * You may explicitly specify it by setting the {@link setPort port} property.
     * @return integer port number for insecure requests.
     * @see setPort
     * @since 1.1.3
     */
    public function getPort()
    {
        if($this->_port===null)
            $this->_port= isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
        return $this->_port;
    }



    /**
     * Redirects the browser to the specified URL.
     * @param string $url URL to be redirected to. Note that when URL is not
     * absolute (not starting with "/") it will be relative to current request URL.
     * @param boolean $terminate whether to terminate the current application
     * @param integer $statusCode the HTTP status code. Defaults to 302. See {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
     * for details about HTTP status code.
     */
    public function redirect($url,$terminate=true,$statusCode=302)
    {
        if(strpos($url,'/')===0 && strpos($url,'//')!==0)
            $url=$this->getHostInfo().$url;
        header('Location: '.$url, true, $statusCode);
        if($terminate)
           App::end();
    }

    /**
     * Returns the schema and host part of the application URL.
     * The returned URL does not have an ending slash.
     * By default this is determined based on the user request information.
     * You may explicitly specify it by setting the {@link setHostInfo hostInfo} property.
     * @param string $schema schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
     * @return string schema and hostname part (with port number if needed) of the request URL (e.g. http://www.yiiframework.com)
     * @see setHostInfo
     */
    public function getHostInfo($schema='')
    {
        if($this->_hostInfo===null)
        {
            if($secure=$this->getIsSecureConnection())
                $http='https';
            else
                $http='http';
            if(isset($_SERVER['HTTP_HOST']))
                $this->_hostInfo=$http.'://'.$_SERVER['HTTP_HOST'];
            else
            {
                $this->_hostInfo=$http.'://'.$_SERVER['SERVER_NAME'];
                $port=$secure ? $this->getSecurePort() : $this->getPort();
                if(($port!==80 && !$secure) || ($port!==443 && $secure))
                    $this->_hostInfo.=':'.$port;
            }
        }
        if($schema!=='')
        {
            $secure=$this->getIsSecureConnection();
            if($secure && $schema==='https' || !$secure && $schema==='http')
                return $this->_hostInfo;

            $port=$schema==='https' ? $this->getSecurePort() : $this->getPort();
            if($port!==80 && $schema==='http' || $port!==443 && $schema==='https')
                $port=':'.$port;
            else
                $port='';

            $pos=strpos($this->_hostInfo,':');
            return $schema.substr($this->_hostInfo,$pos,strcspn($this->_hostInfo,':',$pos+1)+1).$port;
        }
        else
            return $this->_hostInfo;
    }
    /**
     * Return if the request is sent via secure channel (https).
     * @return boolean if the request is sent via secure channel (https)
     */
    public function getIsSecureConnection()
    {
        return !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off');
    }

    private $_securePort;

    /**
     * Returns the port to use for secure requests.
     * Defaults to 443, or the port specified by the server if the current
     * request is secure.
     * You may explicitly specify it by setting the {@link setSecurePort securePort} property.
     * @return integer port number for secure requests.
     * @see setSecurePort
     * @since 1.1.3
     */
    public function getSecurePort()
    {
        if($this->_securePort===null)
            $this->_securePort=$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
        return $this->_securePort;
    }

    public function getBaseUrl($absolute=false)
    {
        if($this->_baseUrl===null)
            $this->_baseUrl=rtrim(dirname($this->getScriptUrl()),'\\/');
        return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
    }

    public function getAbsoluteBaseUrl()
    {
        return $this->getBaseUrl(true);
    }

    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     */
    public function getScriptUrl()
    {
        if($this->_scriptUrl===null)
        {
            $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
            if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
                $this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
            elseif(basename($_SERVER['PHP_SELF'])===$scriptName)
                $this->_scriptUrl=$_SERVER['PHP_SELF'];
            elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
                $this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
            elseif(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
                $this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
            elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
                $this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));

        }
        return $this->_scriptUrl;
    }

} 