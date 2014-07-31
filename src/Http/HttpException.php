<?php
namespace danperron\danmvc\Http;

use danperron\danmvc\Core\MvcException;
/**
 * Description of HttpException
 *
 * @author Dan
 */
class HttpException extends MvcException {
    public function __construct($message, $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}