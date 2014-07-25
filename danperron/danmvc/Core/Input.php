<?php

namespace danperron\danmvc\Core;

/**
 * Handle form input
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
        if (isset($_POST[$key])) {
            return $_POST[$key];
        } else {
            return null;
        }
    }

    public function getQueryParam($key) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        } else {
            return null;
        }
    }
    
    public function getRawInputData(){
        if($this->rawPostData === null){
            $this->rawPostData = file_get_contents('php://input');
        }
        return $this->rawPostData;
    }
    

}
