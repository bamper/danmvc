<?php

namespace danperron\danmvc\Core;

/**
 * Handle input from request
 *
 * @author dan
 */
class Input {

    private static $instance = null;
    private $rawPostData = null;

    private function __construct() {
        
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Input();
        }
        return self::$instance;
    }

    /**
     * Fetch a value of a post form field.  If key does not exist, returns null.
     * 
     * @param string $key
     * @return string
     */
    public function getPostField($key) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        } else {
            return null;
        }
    }

    /**
     * retrieve url query parameter.  Returns null if doesn't exist.
     * 
     * @param string $key
     * @return string
     */
    public function getQueryParam($key) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        } else {
            return null;
        }
    }

    /**
     * return raw request body.
     * 
     * @return type
     */
    public function getRawInputData() {
        if ($this->rawPostData === null) {
            $this->rawPostData = file_get_contents('php://input');
        }
        return $this->rawPostData;
    }
}
