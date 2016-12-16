<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 15:28
 */

namespace base\api;


use base\BaseMonitor;
use base\BaseObject;
use base\log\LoggerFactory;
use base\util\Timer;

class SimpleApiProxy extends BaseObject implements IApi{

    private $_subject = null;

    public function __construct($subject = null) {
        parent::__construct();
        is_null($subject) or $this->setSubject($subject);
    }

    protected function setSubject($subject){
        $this->_subject = $subject;
    }

    protected function getSubject(){
        return $this->_subject;
    }

    public function __call($name, $arguments) {
        $result = null;
        try {
            $subject = $this->getSubject();
            if (is_object($subject)){
                $timer = Timer::factory();
                $timer->start();
                $result = call_user_func_array(array($subject, $name), $arguments);
                $timer->stop();
                $method = get_class($subject) . ':' . $name;
                $msg = '[' . $method . '] args=[' . json_encode($arguments) . '] result=[' . json_encode($result) . ']';
                LoggerFactory::getLogger()->debug($msg);

                $timeUsed = $timer->getMsTime();
                $msg = '[' . $method . '] subject=[' . get_class($subject) . '] args=[' . json_encode($arguments) . '] TimeUsed=[' . $timeUsed . 'ms]';
                LoggerFactory::getLogger()->trace($msg);

                BaseMonitor::factory()->attach('api', $method, $timer->getMsTime());

                return $result;
            }
            else{
                throw new ApiException('subject [' . json_encode($subject) . '] is not an object!');
            }
        } catch (\Exception $e){
            $method = get_class($subject) . ':' . $name;
            $msg = '[' . $method . '] subject=[' . get_class($subject) . '] args=[' . json_encode($arguments) . '] failure';
            LoggerFactory::getLogger()->trace($msg);
            BaseMonitor::factory()->attach('api', $method, 'err');
            throw $e;
        }
    }

}