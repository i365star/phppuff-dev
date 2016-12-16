<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 15:42
 */

namespace base\util;


use base\BaseObject;

class Timer extends BaseObject{

    const PREFIX = 'prefix_timer';

    const DEFAULT_KEY = 'timer';

    private $_start = array();

    private $_stop = array();

    /**
     * @return Timer
     */
    public static function factory(){
        return new self();
    }

    public function isStart($key = null){
        $flag = $this->getKey($key);
        return isset($this->_start[$flag]);
    }

    public function start($key = null){
        $flag = $this->getKey($key);
        return $this->_start[$flag] = $this->getMicrotime();
    }

    public function isStop($key = null){
        $flag = $this->getKey($key);
        return isset($this->_stop[$flag]);
    }

    public function stop($key = null){
        $flag = $this->getKey($key);
        return $this->_stop[$flag] = $this->getMicrotime();
    }

    public function getTime($key = null, $isWhole = false){
        $flag = $this->getKey($key);
        if (isset($this->_start[$flag]) && isset($this->_stop[$flag])){
            $time = $this->_stop[$flag] - $this->_start[$flag];
            if ($isWhole){
                return $time;
            }
            else{
                return (round($time * 100) / 100);
            }
        }
        else{
            return false;
        }
    }

    public function getMsTime($key = null){
        $time = $this->getTime($key, true);
        return (round($time * 1000 * 100) / 100);
    }

    public function getUsTime($key = null){
        $time = $this->getTime($key, true);
        return ($time * 1000 * 1000);
    }

    protected function getKey($key){
        if (is_null($key)){
            return self::DEFAULT_KEY;
        }
        else{
            return self::PREFIX . $key;
        }
    }

    protected function getMicrotime(){
        return microtime(true);
    }

}