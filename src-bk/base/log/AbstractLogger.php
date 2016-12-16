<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/29
 * Time: 18:44
 */

namespace base\log;


abstract class AbstractLogger implements ILogger{

    const FATAL = 0;
    const ERROR = 1;
    const WARN = 2;
    const INFO = 3;
    const TRACE = 4;
    const DEBUG = 5;

    const DEFAULT_CATEGORY = 'main';

    public static function getLogger(){
        static $logger = null;
        if (empty($logger)){
            $logger = new static();
        }
        return $logger;
    }

    public function __construct() {

    }

    public function debug($msg, $category = null, $backtrace = 0) {
        $this->log(static::DEBUG, $msg, $category, $backtrace + 1);
    }

    public function trace($msg, $category = null, $backtrace = 0) {
        $this->log(static::TRACE, $msg, $category, $backtrace + 1);
    }

    public function info($msg, $category = null, $backtrace = 0) {
        $this->log(static::INFO, $msg, $category, $backtrace + 1);
    }

    public function warn($msg, $category = null, $backtrace = 0) {
        $this->log(static::WARN, $msg, $category, $backtrace + 1);
    }

    public function error($msg, $category = null, $backtrace = 0) {
        $this->log(static::ERROR, $msg, $category, $backtrace + 1);
    }

    public function fatal($msg, $category = null, $backtrace = 0) {
        $this->log(static::FATAL, $msg, $category, $backtrace + 1);
    }

    abstract protected function log($level, $msg, $category = null, $backtrace = 0);
}