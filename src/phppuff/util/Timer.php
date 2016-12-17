<?php
/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/16
 * Time: 21:59
 */

namespace phppuff\util;


use phppuff\Multiton;

class Timer extends Multiton {

    const PREFIX = 'prefix_timer';

    const DEFAULT_KEY = 'timer';

    private $_start = array();

    private $_stop = array();

    public function isStart($key = null){
        $flag = $this->getKey($key);
        return isset($this->_start[$flag]);
    }

    public function start($key = null){
        $flag = $this->getKey($key);
        return $this->_start[$flag] = $this->getMicroTime();
    }

    public function isStop($key = null){
        $flag = $this->getKey($key);
        return isset($this->_stop[$flag]);
    }

    public function stop($key = null){
        $flag = $this->getKey($key);
        return $this->_stop[$flag] = $this->getMicroTime();
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

    protected function getMicroTime(){
        return microtime(true);
    }

}