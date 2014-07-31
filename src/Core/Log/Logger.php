<?php

namespace danperron\danmvc\Core\Log;

use danperron\danmvc\Core\Application;
use danperron\danmvc\Core\MvcException;

/**
 * The Logger class is responsible for writing messages and exceptions to the
 * log files.
 *
 * @author dan
 */
class Logger {

    
    const LEVEL_ALL = 10;
    const LEVEL_ERROR = 5;
    const LEVEL_WARN = 3;
    const LEVEL_INFO = 2;    
    const LEVEL_DEBUG = 1;
    
    private $logLevel = self::LEVEL_ALL;
    
    /**
     *
     * @var Application 
     */
    private $application = null;

    /**
     *
     * @var Logger 
     */
    private static $instance = null;

    /**
     *
     * @var bool 
     */
    private static $isInitialized = false;

    /**
     *
     * @var type 
     */
    private $logDirectory = '';
    
    /**
     * 
     * @param Application $app
     */
    private function __construct(Application $app) {
        $this->application = $app;
        $this->logDirectory = $app->getConfig()->logDir;
    }

    /**
     * 
     * @param Application $app
     */
    public static function initialize(Application $app) {
        if (!self::$isInitialized) {
            self::$instance = new Logger($app);
            self::$isInitialized = true;
        }
    }
    
    /**
     * Set the level of logging.
     * 
     * 
     * @param int $level
     */
    public function setLogLevel($level){
        $this->logLevel = $level;
    }
    
    /**
     * 
     * @return int
     */
    public function getLogLevel(){
        return $this->logLevel;
    }

    /**
     * 
     * @return Logger
     * @throws MvcException
     */
    public static function getInstance() {
        if (!self::$isInitialized) {
            throw new MvcException('Logger::getInstance called before Logger::initialize');
        }
        return self::$instance;
    }

    /**
     * 
     * @param string $message
     */
    public function log($message, $level = self::LEVEL_INFO) {
        
        if(!is_writable($this->logDirectory)){
            throw new MvcException("Log directory is not writable.");
        }
        
        
        if($this->logLevel < $level){
            return;
        }
        
        $logFileName = realpath($this->application->getConfig()->logDir) . DIRECTORY_SEPARATOR . date('Y-m-d') . '.txt';

        $handle = fopen($logFileName, 'a+');

        if ($handle) {
            fwrite($handle, "-------------------------------------\n");
            fwrite($handle, date('Y-m-d H:m:s') . ': ' . $message . "\n");
            fwrite($handle, "-------------------------------------\n");
            fclose($handle);
        } else {
            throw new MvcException("Unable to write to log");
        }
    }

    /**
     * 
     * @param \Exception $e
     */
    public function logException(\Exception $e, $level = self::LEVEL_INFO) {
        $logMessage = '';
        $exception = $e;
        while($exception != null){
            
            $logMessage .= get_class($exception) . ": " . $exception->getMessage() . "\n";
            $logMessage .= $exception->getTraceAsString();
            $logMessage .= "\n";
            
            $exception = $exception->getPrevious();
        }
        
        $this->log($logMessage, $level);
    }

}
