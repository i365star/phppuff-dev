<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 15:31
 */

namespace base\log;


class LoggerFactory {

    private static $_logger = array();

    const PREFIX = 'prefix_logger';

    const DEFAULT_KEY = 'main';

    /**
     * @return ILogger
     */
    public static function getLogger($key = null){
        $flag = self::getKey($key);
        $logger = false;
        if (isset(self::$_logger[$flag])){
            $logger = self::$_logger[$flag];
        }
        return $logger instanceof ILogger ? $logger : IdleLogger::getLogger();
    }

    public static function setLogger($logger, $key = null){
        $flag = self::getKey($key);
        self::$_logger[$flag] = $logger;
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