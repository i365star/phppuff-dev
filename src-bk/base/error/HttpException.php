<?php

/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/1
 * Time: 19:33
 */

namespace base\error;

use \Exception;

class HttpException extends \base\BaseException{

    public function __construct($httpStatus, $msg = '', Exception $previous = null) {
        parent::__construct($msg, $httpStatus, $previous);
    }

}