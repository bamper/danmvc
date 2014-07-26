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

    public function getPostField($key) {
        return filter_input(INPUT_POST, $key, FILTER_NULL_ON_FAILURE);
    }

    public function getQueryParam($key) {
        return filter_input(INPUT_GET, $key, FILTER_NULL_ON_FAILURE);
    }

    public function getRawInputData() {
        if ($this->rawPostData === null) {
            $this->rawPostData = file_get_contents('php://input');
        }
        return $this->rawPostData;
    }
}
