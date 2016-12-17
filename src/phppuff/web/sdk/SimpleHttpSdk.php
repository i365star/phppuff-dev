<?php
/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/17
 * Time: 0:38
 */

namespace phppuff\web\sdk;


use phppuff\LoggerFactory;
use phppuff\Singleton;
use phppuff\web\http\CurlException;
use phppuff\web\http\SimpleCurl;

class SimpleHttpSdk extends Singleton implements ISdk {

    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    const FORMAT_JSON   = 'json';
    const FORMAT_XML    = 'xml';
    const FORMAT_QUERY  = 'query';
    const FORMAT_TEXT   = 'text';

    const OPT_HOST              = 1;
    const OPT_TIMEOUT           = 2;
    const OPT_BODY_FORMAT       = 3;
    const OPT_RESPONSE_FORMAT   = 4;
    const OPT_RAW_RESPONSE      = 5;
    const OPT_HTTP_STATUS       = 6;
    const OPT_AJAX              = 7;
    const OPT_RAW               = 8;
    const OPT_URL               = 9;
    const OPT_METHOD            = 10;
    const OPT_ERRNO             = 11;
    const OPT_ERROR             = 12;

    protected $option = [
        self::OPT_RAW => [],
    ];

    protected $gOption = [
        self::OPT_RAW => [],
        self::OPT_TIMEOUT => 3,
    ];

    public function __construct() {
        parent::__construct();

        $this->init();
    }

    protected function init(){

    }

    /**
     * @param bool $isAjax
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setAjax($isAjax = true, $isGlobal = true){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        $option[self::OPT_AJAX] = $isAjax;
        return $this;
    }

    /**
     * @param string $host
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setHost($host, $isGlobal = true){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        $option[self::OPT_HOST] = $host;
        return $this;
    }

    /**
     * @param int $timeout
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setTimeout($timeout, $isGlobal = true){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        $option[self::OPT_TIMEOUT] = $timeout;
        return $this;
    }

    /**
     * @param string $bodyFormat
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setBodyFormat($bodyFormat, $isGlobal = true){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        $option[self::OPT_BODY_FORMAT] = $bodyFormat;
        return $this;
    }

    /**
     * @param string $respFormat
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setResponseFormat($respFormat, $isGlobal = true){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        $option[self::OPT_RESPONSE_FORMAT] = $respFormat;
        return $this;
    }

    /**
     * @param int $key
     * @param mixed $value
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setRawOption($key, $value, $isGlobal = true){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        if (is_array($value) && isset($option[self::OPT_RAW][$key])){
            $option[self::OPT_RAW][$key] = array_merge([], $option[self::OPT_RAW][$key], $value);
        }
        else{
            $option[self::OPT_RAW][$key] = $value;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $isGlobal [optional]
     *
     * @return $this
     */
    public function setHeader($key, $value, $isGlobal = true){
        $this->setRawOption(CURLOPT_HTTPHEADER, array("$key: $value"), $isGlobal);
        return $this;
    }

    public function setUrl($url, $query, $isGlobal = false){
        $isGlobal ? $option = &$this->gOption : $option = &$this->option;
        $option[self::OPT_URL] = $this->makeUrl($url, $query);
        return $this;
    }

    /**
     * @param $body
     *
     * @return bool|string
     */
    protected function makeBody($body){
        $result = false;
        switch ($this->option[self::OPT_BODY_FORMAT]){
            case self::FORMAT_QUERY:
                $result = http_build_query($body);
                break;
            case self::FORMAT_JSON:
                $result = json_encode($body);
                $this->setHeader('Content-Type', 'application/json', false);
                break;
            case self::FORMAT_XML:
                $this->setHeader('Content-Type', 'text/xml', false);
            case self::FORMAT_TEXT:
                $result = $body;
                break;
            default:
                // @todo
        }
        return $result;
    }

    /**
     * @param string $url
     * @param array $query
     *
     * @return string
     */
    protected function makeUrl($url, $query){
        if (empty($query)){
            return $url;
        }
        $queryString = $this->makeQueryString($url, $query);
        if (false === strpos($url, '?')){
            $url = $url . '?' . $queryString;
        }
        else{
            $url = $url . '&' . $queryString;
        }

        return $url;
    }

    protected function makeQueryString($url, $query){
        return http_build_query($query);
    }

    public function get($data = []){
        return $this->doRequest(self::METHOD_GET, $data);
    }

    public function put($data = []){
        return $this->doRequest(self::METHOD_PUT, $data);
    }

    public function delete($data = []){
        return $this->doRequest(self::METHOD_DELETE, $data);
    }

    public function post($data = []){
        return $this->doRequest(self::METHOD_POST, $data);
    }

    /**
     * @param string $method
     * @param array $data
     *
     * @return bool|mixed|\SimpleXMLElement
     * @throws SdkException
     */
    public function doRequest($method, $data = []){
        $this->option[self::OPT_METHOD] = $method;

        $curl = $this->initCurl($data);

        try{
            $this->option[self::OPT_RAW_RESPONSE] = $curl->exec();

            $this->option[self::OPT_HTTP_STATUS] = $curl->getStatus();
            $this->option[self::OPT_ERRNO] = $curl->getErrno();
            $this->option[self::OPT_ERROR] = $curl->getError();

        }catch (CurlException $e){

            $this->option[self::OPT_HTTP_STATUS] = $curl->getStatus();
            $this->option[self::OPT_ERRNO] = $curl->getErrno();
            $this->option[self::OPT_ERROR] = $curl->getError();
            throw new SdkException($e->getMessage(), $e->getCode(), $e);
        }

        $result = $this->parseResponse($this->option[self::OPT_RAW_RESPONSE]);

        return $result;
    }

    /**
     * @param $data
     * @return SimpleCurl
     */
    protected function initCurl($data = []){

        $option = array_merge([], $this->gOption, $this->option);

        $curl = SimpleCurl::factory();

        $host = $this->option[self::OPT_HOST];
        substr($host, -1) === '/' and $host = substr($host, 0, -1);

        $path = $this->option[self::OPT_URL];
        $path{0} === '/' or $path = '/' . $path;
        $url = $host . $path;

        $curl->options[CURLOPT_URL] = $url;

        $curl->options[CURLOPT_TIMEOUT] = $option[self::OPT_TIMEOUT];

        $curl->options[CURLOPT_RETURNTRANSFER] = true;
        $curl->options[CURLOPT_HEADER] = false;
        $curl->options[CURLOPT_FOLLOWLOCATION] = true;
        $curl->options[CURLOPT_MAXREDIRS] = 5;

        $method = $this->option[self::OPT_METHOD];

        switch ($method){
            case self::METHOD_GET:
                $path = $this->makeUrl($path, $data);
                $path{0} === '/' or $path = '/' . $path;
                $url = $host . $path;
                $curl->options[CURLOPT_URL] = $url;
                break;
            case self::METHOD_POST:
                $curl->options[CURLOPT_POST] = true;
                $curl->options[CURLOPT_POSTFIELDS] = $this->makeBody($data);
                break;
            case self::METHOD_PUT:
                $curl->options[CURLOPT_PUT] = true;
                $curl->options[CURLOPT_POSTFIELDS] = $this->makeBody($data);
                break;
            case self::METHOD_DELETE:
            default:
                $curl->options[CURLOPT_CUSTOMREQUEST] = $method;
                $curl->options[CURLOPT_POSTFIELDS] = $this->makeBody($data);
                break;
        }

        $raw = [];

        foreach($this->gOption[self::OPT_RAW] as $key => $value){
            if ($key != CURLOPT_HTTPHEADER || $key != CURLOPT_COOKIE){
                $raw[$key] = $value;
            }
        }
        foreach($this->option[self::OPT_RAW] as $key => $value){
            if ($key != CURLOPT_HTTPHEADER || $key != CURLOPT_COOKIE){
                $raw[$key] = $value;
            }
        }

        if (isset($this->gOption[self::OPT_RAW][CURLOPT_COOKIE]) || isset($this->option[self::OPT_RAW][CURLOPT_COOKIE])){
            $cookie = isset($this->gOption[self::OPT_RAW][CURLOPT_COOKIE]) ? $this->gOption[self::OPT_RAW][CURLOPT_COOKIE] : '';
            $cookie .= ';' . isset($this->option[self::OPT_RAW][CURLOPT_COOKIE]) ? $this->option[self::OPT_RAW][CURLOPT_COOKIE] : '';
            $raw[CURLOPT_COOKIE] = $cookie;
            unset($cookie);
        }

        if (isset($this->gOption[self::OPT_RAW][CURLOPT_HTTPHEADER]) || isset($this->option[self::OPT_RAW][CURLOPT_HTTPHEADER])){
            $header1 = isset($this->gOption[self::OPT_RAW][CURLOPT_HTTPHEADER]) ? $this->gOption[self::OPT_RAW][CURLOPT_HTTPHEADER] : [];
            $header2 = ';' . isset($this->option[self::OPT_RAW][CURLOPT_HTTPHEADER]) ? $this->option[self::OPT_RAW][CURLOPT_HTTPHEADER] : [];
            $raw[CURLOPT_HTTPHEADER] = array_merge([], $header1, $header2);
            unset($header1, $header2);
        }

        foreach ($raw as $key => $value){
            $curl->options[$key] = $value;
        }

        return $curl;
    }

    /**
     * @param string $response
     *
     * @return bool|array|string|\SimpleXMLElement
     */
    protected function parseResponse($response){
        $result = false;

        if (empty($response)){
            return false;
        }

        switch ($this->option[self::OPT_RESPONSE_FORMAT]){
            case self::FORMAT_JSON:
                $result = json_decode($response, true);
                break;
            case self::FORMAT_XML:
                $result = simplexml_load_string($response);
                break;
            case self::FORMAT_TEXT:
                $result = $response;
                break;
            default:
                // @todo
                break;
        }

        if (empty($result)){
            $errMsg = "[{$this->option[self::OPT_HTTP_STATUS]}][result=$result][response={$response}]";
            LoggerFactory::getLogger()->warn($errMsg);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getRawResponse(){
        return $this->option[self::OPT_RAW_RESPONSE];
    }

    /**
     * @return int
     */
    public function getHttpStatus(){
        return $this->option[self::OPT_HTTP_STATUS];
    }


}