<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 15:31
 */

namespace base\log;


interface ILogger {

    public function debug($msg, $category = null, $backtrace = 0);

    public function trace($msg, $category = null, $backtrace = 0);

    public function info($msg, $category = null, $backtrace = 0);

    public function warn($msg, $category = null, $backtrace = 0);

    public function error($msg, $category = null, $backtrace = 0);

    public function fatal($msg, $category = null, $backtrace = 0);

}