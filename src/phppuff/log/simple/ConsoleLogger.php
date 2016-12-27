<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/12/14
 * Time: 11:41
 */

namespace phppuff\log\simple;


use phppuff\log\ILogger;

class ConsoleLogger extends AbstractLogger implements ILogger {

    const FATAL = 'FATAL';
    const ERROR = 'ERROR';
    const WARN = 'WARN';
    const INFO = 'INFO';
    const TRACE = 'TRACE';
    const DEBUG = 'DEBUG';

    protected function write($text, $level = null) {
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

    public function getLogId($refresh = false) {
        return posix_getpid();
    }

}