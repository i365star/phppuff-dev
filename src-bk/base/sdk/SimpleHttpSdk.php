<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/1
 * Time: 17:51
 */

namespace base\sdk;


use base\BaseMonitor;
use base\error\ErrnoException;
use base\error\HttpException;
use base\http\Curl;
use base\log\LoggerFactory;
use base\Singleton;
use base\util\Timer;

abstract class SimpleHttpSdk extends Singleton implements ISdk{

    const METHOD_GET = 'GET';
    const METHOD_DELETE = 'DELETE';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';

    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_QUERY = 'query';
    const FORMAT_TEXT = 'text';

    private $_host = null;

    private $_timeout = 0;

    private $_error = '';

    private $_errno = 0;

    private $_httpStatus = 0;

    private $_bodyFormat = self::FORMAT_QUERY;

    private $_respFormat = self::FORMAT_TEXT;

    private $_opts = array();

    private $_rawResp = false;

    private $_multiNumber = 20;

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    protected function init(){

    }

    protected function setHost($host){
        $this->_host = $host;
    }

    protected function getHost(){
        return $this->_host;
    }

    protected function setTimeout($timeout){
        $this->_timeout = $timeout;
    }

    protected function getTimeout(){
        return $this->_timeout;
    }

    protected function setBodyFormat($bodyFormat){
        $this->_bodyFormat = $bodyFormat;
    }

    protected function getBodyFormat(){
        return $this->_bodyFormat;
    }

    protected function setResponseFormat($respFormat){
        $this->_respFormat = $respFormat;
    }

    protected function getResponseFormat(){
        return $this->_respFormat;
    }

    protected function makeQueryString($api, $query){
        return http_build_query($query);
    }

    public function setMultiNumber($number){
        $this->_multiNumber = $number;
    }

    public function getMultiNumber(){
        return $this->_multiNumber;
    }

    protected function makeBody($api, $body){
        $result = false;
        switch ($this->getBodyFormat()){
            case self::FORMAT_QUERY:
                $result = http_build_query($body);
                break;
            case self::FORMAT_JSON:
                $result = json_encode($body);
                break;
            case self::FORMAT_XML:
            case self::FORMAT_TEXT:
                $result = $body;
                break;
            default:
                // @todo
        }
        return $result;
    }

    protected function makeUrl($api, $query){
        if (empty($query)){
            return $api;
        }
        $queryString = $this->makeQueryString($api, $query);
        if (false === strpos($api, '?')){
            $url = $api . '?' . $queryString;
        }
        else{
            $url = $api . '&' . $queryString;
        }

        return $url;
    }

    protected function setOption($key, $value){
        if (is_array($value) && isset($this->_opts[$key])){
            $this->_opts[$key] = array_merge($this->_opts[$key], $value);
        }
        else{
            $this->_opts[$key] = $value;
        }
    }

    protected function setHeader($key, $value){
        $this->setOption(CURLOPT_HTTPHEADER, array("$key: $value"));
    }

    protected function getOptions(){
        return $this->_opts;
    }

    protected function request($method, $api, $query = array(), $body = array(), $headers = array(), $opts = array()){
        $this->_rawResp = false;

        $url = $this->getHost() . $this->makeUrl($api, $query);
        $bodyData = is_null($body) ? null : $this->makeBody($api, $body);
        $curl = $this->initCurl($method, $api, $query, $body, $headers, $opts);

        $timer = Timer::factory();
        $timer->start();
        $this->_rawResp = $response = $curl->exec();
        $timer->stop();

        $this->_httpStatus = $httpStatus = $curl->getStatus();
        $this->_errno = $curl->getErrno();
        $this->_error = $curl->getError();

        $traceLog = '[' . get_class($this) . '][api=' . $api . '][method=' . $method . '][url=' . $url . '][body=' . $bodyData . '][status=' . $httpStatus . '][timeUsed=' . $timer->getMsTime() . 'ms]';
        LoggerFactory::getLogger()->trace($traceLog);

        BaseMonitor::factory()->attach('sdk', get_class($this) . ':' . $api, $timer->getMsTime());

        return $this->parseResponse($api, $response);
    }

    /**
     * @param $method
     * @param $api
     * @param array $query
     * @param array $body
     * @param array $headers
     * @param array $opts
     * @return Curl
     */
    protected function initCurl($method, $api, $query = array(), $body = array(), $headers = array(), $opts = array()){
        $curl = Curl::factory();

        $host = $this->getHost();
        substr($host, -1) === '/' and $host = substr($host, 0, -1);

        $path = $this->makeUrl($api, $query);
        $path{0} === '/' or $path = '/' . $path;

        $url = $host . $path;

        $curl->options[CURLOPT_URL] = $url;

        $curl->options[CURLOPT_TIMEOUT] = $this->getTimeout();

        $curl->options[CURLOPT_RETURNTRANSFER] = true;

        $curl->options[CURLOPT_HEADER] = false;

        $curl->options[CURLOPT_FOLLOWLOCATION] = true;

        $curl->options[CURLOPT_MAXREDIRS] = 5;

        switch ($method){
            case self::METHOD_GET:
                break;
            case self::METHOD_POST:
                $curl->options[CURLOPT_POST] = true;
                $curl->options[CURLOPT_POSTFIELDS] = $this->makeBody($api, $body);
                break;
            case self::METHOD_PUT:
                $curl->options[CURLOPT_PUT] = true;
                $curl->options[CURLOPT_POSTFIELDS] = $this->makeBody($api, $body);
                break;
            case self::METHOD_DELETE:
            default:
                $curl->options[CURLOPT_CUSTOMREQUEST] = $method;
                $curl->options[CURLOPT_POSTFIELDS] = $this->makeBody($api, $body);
                break;
        }

        $options = $this->getOptions();
        foreach ($opts as $key => $val){
            $options[$key] = $val;
        }
        $commonHeaders = isset($options[CURLOPT_HTTPHEADER]) ? $options[CURLOPT_HTTPHEADER] : array();
        foreach($headers as $key => $val){
            $commonHeaders[] = "$key: $val";
        }
        $options[CURLOPT_HTTPHEADER] = $commonHeaders;

        foreach ($options as $k => $v){
            $curl->options[$k] = $v;
        }

        return $curl;
    }

    protected function multi($method, $api, $query = array(), $body = array(), $headers = array(), $opts = array()){
    }

    protected function exceptionHandler($exception){
        
    }

    protected function parseResponse($api, $response){
        $result = false;
        if (empty($response)){
            return $result;
        }

        switch ($this->getStatus()){
            case HTTP_STATUS_OK:
                break;
            default:
                $errMsg = "[{$this->getStatus()}][err:{$this->getErrno()}:{$this->getError()}]";
                throw new HttpException(HTTP_STATUS_BAD_GATEWAY, $errMsg);
                break;
        }

        switch ($this->getResponseFormat()){
            case self::FORMAT_JSON:
                $result = json_decode($response, true);
                break;
            case self::FORMAT_TEXT:
                $result = $response;
                break;
            case self::FORMAT_XML:
                $result = simplexml_load_string($response);
                break;
            default:
                // @todo
                break;
        }

        if (empty($result)){
            $errMsg = "[{$this->getStatus()}][err:{$this->getErrno()}:{$this->getError()}]";
            throw new ErrnoException(ERRNO_BETA_API_ERR, $errMsg);
        }

        return $result;
    }

    public function getError(){
        return $this->_error;
    }

    public function getErrno(){
        return $this->_errno;
    }

    public function getStatus(){
        return $this->_httpStatus;
    }

    public function getRawResponse(){
        return $this->_rawResp;
    }
    
}