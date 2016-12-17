<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/11
 * Time: 11:40
 */

namespace phppuff\core;

use phppuff\Object;

abstract class Multiton extends Object implements IObject {

    /**
     * @return static
     */
    public static function factory(){
        $args = func_get_args();
        $className = get_called_class();
        $reflection = new \ReflectionClass($className);
        $ins = $reflection->newInstanceArgs($args);

        return $ins;
    }
}
