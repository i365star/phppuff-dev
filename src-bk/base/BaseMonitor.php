<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/8/12
 * Time: 12:01
 */

namespace base;

class BaseMonitor extends Singleton{

    private $_list = array();

    public function attach($category, $key, $value){
        $this->_list[] = array(
            $category,
            $key,
            $value,
        );
    }

    public function export(){
        $logArr = array();
        foreach ($this->_list as $arr){
            list($category, $key, $value) = $arr;
            if (! is_scalar($value)){
                $value = json_encode($value);
            }
            $category = empty($category) ? '' : $category . '.';
            $logArr[] = "[$category$key=$value]";
        }
        return implode(' ', $logArr);
    }
}