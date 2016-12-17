<?php

/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/16
 * Time: 21:28
 */

namespace phppuff\log;

interface ILogger {

    /**
     * @param string $msg
     * @param null $category [optional]
     * @param int  $backtrace [optional]
     *
     * @return boolean
     */
    public function debug($msg, $category = null, $backtrace = 0);

    /**
     * @param string $msg
     * @param null $category [optional]
     * @param int  $backtrace [optional]
     *
     * @return boolean
     */
    public function trace($msg, $category = null, $backtrace = 0);

    /**
     * @param string $msg
     * @param null $category [optional]
     * @param int  $backtrace [optional]
     *
     * @return boolean
     */
    public function info($msg, $category = null, $backtrace = 0);

    /**
     * @param string $msg
     * @param null $category [optional]
     * @param int  $backtrace [optional]
     *
     * @return boolean
     */
    public function warn($msg, $category = null, $backtrace = 0);

    /**
     * @param string $msg
     * @param null $category [optional]
     * @param int  $backtrace [optional]
     *
     * @return boolean
     */
    public function error($msg, $category = null, $backtrace = 0);

    /**
     * @param string $msg
     * @param null $category [optional]
     * @param int  $backtrace [optional]
     *
     * @return boolean
     */
    public function fatal($msg, $category = null, $backtrace = 0);

}