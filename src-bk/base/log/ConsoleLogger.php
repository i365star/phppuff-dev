<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/12/14
 * Time: 11:41
 */

namespace base\log;


class ConsoleLogger extends AbstractLogger implements ILogger{

    const FATAL = 'FATAL';
    const ERROR = 'ERROR';
    const WARN = 'WARN';
    const INFO = 'INFO';
    const TRACE = 'TRACE';
    const DEBUG = 'DEBUG';

    protected function log($level, $msg, $category = null, $backtrace = 0) {
        empty($category) && $category = static::DEFAULT_CATEGORY;
        $text = $this->format($msg, $level, $category);

        $stdStream = null;
        switch ($level){
            case static::WARN:
            case static::ERROR:
            case static::FATAL:
                $stdStream = STDERR;
                break;
            default:
                $stdStream = STDOUT;
                break;
        }

        fwrite($stdStream, $text);
    }

    protected function format($message,$level,$category){
        $pid = posix_getpid();
        list($uSecond, $time) = explode(' ', microtime());
        return @date('Y/m/d H:i:s',$time) . "." . str_pad(round($uSecond * 1000), 3, 0, STR_PAD_LEFT) ." [$pid] [$level] [$category] $message\n";
    }

}