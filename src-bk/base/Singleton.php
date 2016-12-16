<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/6/28
 * Time: 20:04
 */

namespace base;


abstract class Singleton {

    public function __construct() {
    }

    /**
     * @return static
     */
    public static function factory(){
        static $instance = null;
        if (empty($instance)){
            $args = func_get_args();
            $className = get_called_class();
            $refl = new \ReflectionClass($className);
            $instance = $refl->newInstanceArgs($args);
        }
        return $instance;
    }

}