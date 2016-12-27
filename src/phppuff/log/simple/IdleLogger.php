<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 19:01
 */

namespace phppuff\log\simple;

use phppuff\log\ILogger;

class IdleLogger extends AbstractLogger implements ILogger {

    protected function write($text, $level = null) {
        return true;
    }

}