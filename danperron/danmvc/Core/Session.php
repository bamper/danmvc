<?php

namespace danperron\danmvc\Core;

/**
 * Description of Session
 *
 * @author dan
 */
class Session {

    /**
     *
     * @var Session
     */
    private static $instance = null;

    private function __construct() {
        
    }

    /**
     * 
     * @return Session
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function startSession() {
        session_start();
    }

    public function get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return null;
        }
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function remove($key){
        unset($_SESSION[$key]);
    }

    public function endSession() {
        session_destroy();
        unset($_SESSION);
    }

}
