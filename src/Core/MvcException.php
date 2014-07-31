<?php

namespace danperron\danmvc\Core;

use Exception;

/**
 * Description of MvcException
 *
 * @author dan
 */
class MvcException extends Exception {

    const ERROR_CODE_NO_ROUTE = 404;
    const ERROR_CODE_UNAUTHORIZED = 401;
    
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    

}
