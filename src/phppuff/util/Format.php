<?php
/**
 * Created by PhpStorm.
 * User: nova
 * Date: 2016/12/16
 * Time: 22:02
 */

namespace phppuff\util;


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