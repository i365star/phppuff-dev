<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 15:28
 */

namespace base\api;


use base\BaseObject;
use base\log\LoggerFactory;
use base\util\Timer;

class ApiProxy extends BaseObject implements IApi{

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
                throw new ApiException('subject is not an object!');
            }
        }catch (\Exception $e){
            $timer->isStop() or $timer->stop();
            throw $e;
        }finally{
            $timeUsed = $timer->getMsTime();
            if (false !== $timeUsed){
                $msg = '[' . $this->getClass() . '::' . $name . '] TimeUsed=[' . $timeUsed . 'ms]';
                LoggerFactory::getLogger()->trace($msg);
            }
        }
    }

}