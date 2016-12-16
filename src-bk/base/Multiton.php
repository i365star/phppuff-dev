<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/11
 * Time: 11:40
 */

namespace base;


abstract class Multiton {

    public function __construct() {
    }

    /**
     * @return static
     */
    public static function factory(){
        $args = func_get_args();
        $className = get_called_class();
        $refl = new \ReflectionClass($className);
        $ins = $refl->newInstanceArgs($args);
        return $ins;
    }
}