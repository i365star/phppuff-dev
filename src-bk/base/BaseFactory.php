<?php

/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/5/31
 * Time: 14:01
 */

namespace base;

use ReflectionClass;
use Exception;

class BaseFactory {

    private $_map = array();

    /**
     * @param string $className
     * @return BaseFactory
     */
    public static function instance($className = __CLASS__){
        static $map = array();
        if (! isset($map[$className])){
            if (! is_subclass_of($className, self)){
                $className = __CLASS__;
            }
            $reflectionObj = new ReflectionClass($className);
            $ins = $reflectionObj->newInstance();
            $map[$className] = $ins;
        }
        return $map[$className];
    }

    public function factory($className, $args = array(), $isSingle = true){
        return $isSingle ? $this->getSingleton($className, $args) : $this->newInstance($className, $args);
    }
    
    public function getSingleton($className, $args = array()){
        if (! isset($this->_map[$className])){
            $this->_map[$className] = $this->newInstance($className, $args);
        }
        return $this->_map[$className];
    }

    public function newInstance($className, $args = array()){
        try{
            $reflectionObj = new ReflectionClass($className);
            $obj = $reflectionObj->newInstanceArgs($args);
        } catch (Exception $e){
            throw $e;
        }
        return $obj;
    }

}