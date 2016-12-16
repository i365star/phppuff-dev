<?php

namespace base;

/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/5/31
 * Time: 10:30
 */

abstract class BaseModel implements IBaseModel{

    private $_instance = null;

    public function __construct(IBaseModel $instance = null) {
        is_null($instance) && $instance = $this;
        $this->_instance = $this;
    }

}