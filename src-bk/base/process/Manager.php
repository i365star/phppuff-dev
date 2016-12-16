<?php

/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/12/15
 * Time: 11:02
 */

namespace base\process;

use base\Singleton;

class Manager extends Singleton{

    private $_configure = [];

    public function run(){

    }

    public function configure($configure = []){
        $this->_configure = $configure;
        return $this;
    }

}