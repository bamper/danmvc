<?php

namespace danperron\danmvc\Core;

use danperron\danmvc\Core\Config\IConfiguration;
use danperron\danmvc\Core\Log\Logger;
use danperron\danmvc\Http\HttpHeaders;
use Exception;
use ReflectionClass;
use stdClass;

/**
 * Description of Application
 *
 * @author dan
 */
class Application {

    /**
     *
     * @var stdClass 
     */
    private $config = array();

    /**
     *
     * @var array 
     */
    private $routes = array();

    /**
     *
     * @var Session
     */
    private $session = null;

    /**
     * @var Logger
     */
    private $logger = null;

    /**
     *
     * @var array
     */
    private $initHooks = array();

    /**
     * @var array
     */
    private $exitHooks = array();

    /**
     *
     * @var array
     */
    private $properties = array();

    /**
     *
     * @var string 
     */
    private $path = '';

    /**
     *
     * @var Router
     */
    private $router = null;
    
    /**
     * 
     * @param type $config
     */
    function __construct(IConfiguration $config) {
        $this->session = Session::getInstance();
        $this->config = Application::configDefaults();
        $this->router = new Router($this);

        $this->addConfig($config);
        
    }

    /**
     * Execute the current application.
     */
    public function run() {
        try {
            ob_start();
            $this->init();
            $this->router->doRouting();
            $this->finish();
            ob_end_flush();
        } catch (MvcException $e) {
            $this->logger->logException($e, Logger::LEVEL_ERROR);
            ob_end_clean();
            if ($e->getCode() == MvcException::ERROR_CODE_NO_ROUTE) {
                $this->showNotFound();
            } else {
                $this->showError($e);
            }
        } catch (\Exception $e) {
            $this->logger->logException($e, Logger::LEVEL_ERROR);
            ob_end_clean();
            $this->showError($e);
        }
    }

    public function registerLoaders() {
        $this->registerModelAutoloader();
        $this->registerLibraryAutoLoader();
        $this->registerControllerAutoloader();
    }

    public function addConfig(IConfiguration $config){
        foreach($config->getAttributes() as $key => $value){
            $this->config->$key = $value;
        }
        
        if(!empty($this->config->properties)){
            foreach($this->config->properties as $key => $value){
                $this->setProperty($key, $value);
            }
            unset($this->config->properties);
        }
        
    }
    
    /**
     * initialize the application.
     */
    private function init() {
        //set default timezone
        date_default_timezone_set($this->config->timezone);


        $this->registerModelAutoloader();
        $this->registerLibraryAutoLoader();
        $this->registerControllerAutoloader();
        $this->addRoutesFromConfig();

        $this->session->startSession();

        if (empty($this->config->baseURI)) {
            $this->config->baseURI = $_SERVER['SERVER_NAME'];
        }

        //Setup Logger
        Logger::initialize($this);
        $this->logger = Logger::getInstance();

        //Run Init Hooks
        if (count($this->initHooks > 0)) {
            foreach ($this->initHooks as $initHook) {
                call_user_func($initHook, $this);
            }
        }
    }

    /**
     * called after routing.
     */
    private function finish() {
        if (count($this->exitHooks > 0)) {
            foreach ($this->exitHooks as $exitHook) {
                call_user_func($exitHook, $this);
            }
        }
    }

    /**
     * Execute a redirect.
     * 
     * @param type $url
     * @param type $code
     */
    public function redirect($url, $code = 302) {
        header(HttpHeaders::buildHeader($code));
        header("Location: $url");
        exit();
    }

    /**
     * And a callable function to be executed on the application's 
     * initialization.
     * 
     * @param \callable $initHook
     */
    public function addInitHook(callable $initHook) {
        $this->initHooks[] = $initHook;
    }

    /**
     * And a callable function to be executed on application 
     * end.
     * 
     * @param \callable $exitHook
     */
    public function addExitHook(callable $exitHook) {
        $this->exitHooks[] = $exitHook;
    }

    /**
     * return the current executed path.
     * 
     * @return string
     */
    public function getPath() {
        return $this->path;
    }




    /**
     * Return the base URI for the existing web application.
     * 
     * @return type
     */
    public function getBaseUri() {
        return 'http://' . $_SERVER['SERVER_NAME'] . $this->config->basePath;
    }

    /**
     * Return a standard object containing the configuration parameters for the
     * current Application.
     * 
     * @return type
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Add a new route to the application.
     * 
     * @param Route $route
     */
    public function addRoute(Route $route) {
        $this->router->addRoute($route);
    }

    /**
     * Register the framework auto loader.
     */
    public static function registerAutoloader() {
        spl_autoload_register(function($className) {
            $dir = dirname(__DIR__);
            if (preg_match('/^danperron\\\\danmvc/', $className)) {
                $className = str_replace('danperron\danmvc\\', '', $className);
                $className = str_replace('\\', '/', $className);
                $fileName = $dir . DIRECTORY_SEPARATOR . $className . '.php';
                require_once $fileName;
            }
        });
    }

    private function registerModelAutoloader() {
        spl_autoload_register(function($className) {
            $fileName = $this->config->modelDir . DIRECTORY_SEPARATOR . $className . '.php';
            if (file_exists($fileName)) {
                require_once $fileName;
            }
        });
    }

    private function registerLibraryAutoLoader() {
        spl_autoload_register(function($className) {
            $fileName = $this->config->libDir . DIRECTORY_SEPARATOR . $className . '.php';
            if (file_exists($fileName)) {
                require_once $fileName;
            }
        });
    }

    private function registerControllerAutoloader() {
        spl_autoload_register(function($className) {
            $fileName = $this->config->controllerDir . DIRECTORY_SEPARATOR . $className . '.php';
            if (file_exists($fileName)) {
                require_once $fileName;
            }
        });
    }

    private function addRoutesFromConfig() {
        if (isset($this->config->routes) && !empty($this->config->routes)) {
            foreach ($this->config->routes as $route) {

                $controller = '';
                $method = '';

                if (strpos($route['action'], '@') !== false) {
                    $action = explode('@', $route['action']);
                    $controller = $action[0];
                    $method = $action[1];
                } else {
                    $controller = $route['action'];
                    $method = 'index';
                }

                $newRoute = new Route($route['pattern'], $controller, $method, $route['methods']);

                $this->router->addRoute($newRoute);
            }
        }
        unset($this->config->routes);
    }

    private static function configDefaults() {
        $configDefaults = array();
        $configDefaults['timezone'] = "America/New_York";
        $configDefaults['debug'] = false;
        $configDefaults['baseURI'] = '';
        $configDefaults['basePath'] = '/';
        $configDefaults['controllerDir'] = '';
        $configDefaults['modelDir'] = '';
        $configDefaults['libDir'] = '';
        $configDefaults['viewDir'] = '';
        $configDefaults['logDir'] = '';
        $configDefaults['isProd'] = false;
        $configDefaults['notFoundView'] = 'notFoundView';
        $configDefaults['errorView'] = 'errorView';
        $configDefaults['useMasterView'] = false;
        $configDefaults['masterView'] = '';
        $configDefaults['logLevel'] = Logger::LEVEL_ALL;


        return (object)$configDefaults;
    }

    private function showNotFound() {
        header(HttpHeaders::HTTP_NOT_FOUND);
        $view = new View($this);
        $view->render($this->config->notFoundView);
    }

    private function showError($error) {
        header(HttpHeaders::HTTP_SERVER_ERROR);
        $view = new View($this);
        $view->assign('error', $error);
        $view->render($this->config->errorView);
    }

    public function setProperty($key, $value) {
        $this->properties[$key] = $value;
    }

    public function getProperty($key) {
        return $this->properties[$key];
    }

}
