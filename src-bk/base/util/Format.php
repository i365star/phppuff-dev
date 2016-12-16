<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/4
 * Time: 17:04
 */

namespace base\util;


abstract class Format {

    public static function int($value){
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public static function number($value){
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    public static function string($value){
        return strval($value);
    }

}