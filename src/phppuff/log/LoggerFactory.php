<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 15:31
 */

namespace phppuff\log;


use phppuff\log\simple\IdleLogger;

class LoggerFactory {

    private static $_logger = array();

    const PREFIX = 'prefix_logger';

    const DEFAULT_KEY = 'main';

    /**
     *
     * @param string $key [optional]
     * @return ILogger
     */
    public static function getLogger($key = null){
        $flag = static::getKey($key);
        $logger = false;
        if (isset(static::$_logger[$flag])){
            $logger = static::$_logger[$flag];
        }
        return $logger instanceof ILogger ? $logger : IdleLogger::getLogger();
    }

    /**
     * @param ILogger $logger
     * @param null $key
     */
    public static function setLogger($logger, $key = null){
        $flag = static::getKey($key);
        static::$_logger[$flag] = $logger;
    }

    protected static function getKey($key){
        if (is_null($key)){
            return static::DEFAULT_KEY;
        }
        else{
            return static::PREFIX . $key;
        }
    }
}