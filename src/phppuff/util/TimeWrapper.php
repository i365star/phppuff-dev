<?php
/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/16
 * Time: 22:27
 */

namespace phppuff\util;

use phppuff\error\Exception;
use phppuff\LoggerFactory;
use phppuff\Multiton;

class TimeWrapper extends Multiton {

    private $_subject = null;

    protected function setSubject($subject){
        $this->_subject = $subject;
    }

    protected function getSubject(){
        return $this->_subject;
    }

    public function __call($name, $arguments) {
        $timer = Timer::factory();
        $result = null;
        try{
            $subject = $this->getSubject();
            if (is_object($subject)){
                $timer->start();
                $result = call_user_func_array(array($subject, $name), $arguments);
                $timer->stop();
                return $result;
            }
            else{
                throw new Exception('subject is not an object!');
            }
        }catch (\Exception $e){
            $timer->isStop() or $timer->stop();
            throw $e;
        }finally{
            $timeUsed = $timer->getMsTime();
            if (false !== $timeUsed){
                $msg = '[' . get_class($this) . '::' . $name . '] TimeUsed=[' . $timeUsed . 'ms]';
                LoggerFactory::getLogger()->trace($msg);
            }
        }
    }

}