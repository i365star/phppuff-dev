<?php
/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/16
 * Time: 21:30
 */

namespace phppuff\log\simple;

use phppuff\log\ILogger;
use phppuff\Multiton;
use phppuff\util\StringUtil;

abstract class AbstractLogger extends Multiton implements ILogger {

    const FATAL = 0;
    const ERROR = 1;
    const WARN = 2;
    const INFO = 3;
    const TRACE = 4;
    const DEBUG = 5;

    const DEFAULT_CATEGORY = 'main';

    private static $_logId = null;

    public static function getLogger(){
        return static::factory();
    }

    public function getLogId($refresh = false){
        if (empty(self::$_logId) || $refresh){
            self::$_logId = md5(StringUtil::uuid('logger'));
        }
        return self::$_logId;
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

    protected function log($level, $msg, $category = null, $backtrace = 0){
        empty($category) && $category = static::DEFAULT_CATEGORY;
        $backtraceMsg = $this->getBacktrace($backtrace + 1);
        $msg = $this->format($backtraceMsg, $msg, $level, $category);
        return $this->write($msg, $level);
    }

    abstract protected function write($text, $level = null);

    protected function format($backtraceMsg, $message, $level, $category){
        list($uSecond, $time) = explode(' ', microtime());
        $logId = $this->getLogId();
        return @date('Y/m/d H:i:s',$time) . "." . str_pad(round($uSecond * 1000), 3, 0, STR_PAD_LEFT) ." [$logId] [$level] [$category] [$backtraceMsg] $message\n";
    }

    protected function getBacktrace($backtrace = 0){
        $backtraceInfoList = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $backtraceInfo = $backtraceInfoList[$backtrace];
        $prefix = '';
        do{
            if (isset($backtraceInfoList[$backtrace + 1])){
                $backtraceInfoCallled = $backtraceInfoList[$backtrace + 1];
                if (isset($backtraceInfoCallled['class'])){
                    $prefix .= $backtraceInfoCallled['class'] . '(' . $backtraceInfo['line'] . ')';
                    break;
                }
                elseif (isset($backtraceInfoCallled['function'])){
                    $prefix .= $backtraceInfoCallled['function'] . '(' . $backtraceInfo['line'] . ')';
                    break;
                }
            }
            if (isset($backtraceInfo['file'])){
                $prefix .= basename($backtraceInfo['file']) . '(' . $backtraceInfo['line'] . ')';
            }
        }while(false);

        return empty($prefix) ? $prefix : $prefix;
    }
}