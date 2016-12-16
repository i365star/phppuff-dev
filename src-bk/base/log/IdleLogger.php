<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 19:01
 */

namespace base\log;

class IdleLogger extends AbstractLogger implements ILogger{

    protected function log($level, $msg, $category = null, $backtrace = 0) {
        return ;
    }

}