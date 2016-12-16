<?php
/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/7/1
 * Time: 19:37
 */

namespace base\error;

use \Exception;

class ErrnoException extends \base\BaseException{

    private $_data = null;

    public function __construct($errno, $error = '', $data = null, Exception $previous = null) {
        parent::__construct($error, $errno, $previous);
        $this->_data = $data;
    }

    public function getData(){
        return $this->_data;
    }

}