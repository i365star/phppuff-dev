<?php

namespace base;

/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/5/31
 * Time: 15:17
 */
class BaseObject {

    public function __construct() {
    }

    public function getClass(){
        return get_class($this);
    }

}
