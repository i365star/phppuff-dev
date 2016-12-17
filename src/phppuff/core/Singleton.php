<?php
/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/16
 * Time: 21:35
 */

namespace phppuff\core;

abstract class Singleton implements IObject {

    protected function __construct() { }

    protected function __clone() { }

    /**
     * @return static
     */
    public static function factory(){
        static $instance = null;
        if (empty($instance)){
            $args = func_get_args();
            $className = get_called_class();
            $reflection = new \ReflectionClass($className);
            $instance = $reflection->newInstanceArgs($args);
        }

        return $instance;
    }


}