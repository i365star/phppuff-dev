<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/11
 * Time: 11:27
 */

namespace phppuff\web\http;


use phppuff\LoggerFactory;
use phppuff\Multiton;
use phppuff\util\Timer;

class SimpleCurl extends Multiton {

    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_PUT = 'PUT';

    public $options;

    private $_errno;

    private $_error;

    private $_resp;

    private $_status;

    private $_info;

    public function __construct() {
        parent::__construct();
        $this->options = [];
    }

    public function getErrno(){
        return $this->_errno;
    }

    public function getError(){
        return $this->_error;
    }

    protected function setErrno($errno){
        $this->_errno = $errno;
    }

    protected function setError($error){
        $this->_error = $error;
    }

    /**
     * @return mixed
     */
    public function getResponse() {
        return $this->_resp;
    }

    /**
     * @param mixed $resp
     */
    protected function setResponse($resp) {
        $this->_resp = $resp;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->_status;
    }

    /**
     * @param mixed $status
     */
    protected function setStatus($status) {
        $this->_status = $status;
    }

    /**
     * @param string $key [optional]
     * @return mixed
     */
    public function getInfo($key = null) {
        return empty($key) ? $this->_info : $this->_info[$key];
    }

    /**
     * @param mixed $info
     */
    protected function setInfo($info) {
        $this->_info = $info;
    }

    /**
     * @return mixed
     * @throws CurlException
     */
    public function exec(){
        $ch = curl_init();

        curl_setopt_array($ch, $this->options);

        $timer = Timer::factory();
        $timer->start();
        $resp = curl_exec($ch);
        $timer->stop();

        $this->setResponse($resp);

        $this->setErrno(curl_errno($ch));
        $this->setError(curl_error($ch));
        $this->setInfo(curl_getinfo($ch));
        $this->setStatus($this->getInfo('http_code'));
        curl_close($ch);

        $url = $this->options[CURLOPT_URL];
        $host = null;
        if (($parse_arr = parse_url($url)) !== false) {
            $host = $parse_arr['host'];
        }

        $method = static::HTTP_METHOD_GET;
        if (isset($this->options[CURLOPT_POST]) && $this->options[CURLOPT_POST]){
            $method = static::HTTP_METHOD_POST;
        }
        elseif (isset($this->options[CURLOPT_PUT]) && $this->options[CURLOPT_PUT]){
            $method = static::HTTP_METHOD_PUT;
        }
        elseif (isset($this->options[CURLOPT_CUSTOMREQUEST]) && $this->options[CURLOPT_CUSTOMREQUEST]){
            $method = $this->options[CURLOPT_CUSTOMREQUEST];
        }

        if (isset($this->options[CURLOPT_POSTFIELDS])){
            $data = is_string($this->options[CURLOPT_POSTFIELDS]) ? $this->options[CURLOPT_POSTFIELDS] : json_encode($this->options[CURLOPT_POSTFIELDS]);
        }
        else{
            $data = '';
        }

        $headers = '';
        if (isset($this->options[CURLOPT_HTTPHEADER])){
            $headers = json_encode($this->options[CURLOPT_HTTPHEADER]);
        }

        $msg = "Curl Finish: [method=$method] [host=$host] [url=$url] [data=$data] [header=$headers] [status={$this->getStatus()}] [resp={$this->getResponse()}] [cost={$timer->getMsTime()}ms]";
        LoggerFactory::getLogger()->debug($msg);

        if ($this->getErrno() != CURLE_OK){
            $warn = "Curl Error: [method=$method] [host=$host] [url=$url] [data=$data] [header=$headers] [status={$this->getStatus()}] [resp={$this->getResponse()}] [cost={$timer->getMsTime()}ms] [errno={$this->getErrno()}] [error={$this->getError()}]";
            LoggerFactory::getLogger()->warn($warn);

            throw new CurlException($this->getError(), $this->getErrno());
        }

        return $resp;
    }

}