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
     * @param type $config
     */
    function __construct(IConfiguration $config) {
        $this->session = Session::getInstance();
        
        $this->config = Application::configDefaults();

        foreach ($config->getAttributes() as $key => $value) {
            $this->config->$key = $value;
        }

    }

    /**
     * Execute the current application.
     */
    public function run() {
        try {
            ob_start();
            $this->init();
            //echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            $this->doRouting();
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
     * Try to find the route that best matches the current path and execute it.
     * 
     * @throws MvcException
     */
    private function doRouting() {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $queryString = '?' . $_SERVER['QUERY_STRING'];
        $matchURI = str_replace($queryString, '', $uri);
        $matchURI = preg_replace('/^https?:\/\//', '', str_replace($this->config->baseURI, '', $uri));
        //$matchURI = preg_replace('/\/$/', '', $matchURI);
        $this->path = $matchURI;



        foreach ($this->routes as $route) {
            /** @var Route $route * */
            if ($route->match($matchURI, $httpMethod)) {
                $args = $this->getNamedRouteParams($route, $matchURI);
                $this->executeRoute($route, $args);
                return;
            }
        }

        throw new MvcException("No Route found for path '$matchURI'", MvcException::ERROR_CODE_NO_ROUTE);
    }

    /**
     * 
     * Pull the named parameters and their values out of the current path using
     * the matching Route.
     * 
     * @param Route $route
     * @param type $matchURI
     * @return type
     * @throws Exception
     */
    private function getNamedRouteParams(Route $route, $matchURI) {
        $uriSplit = explode('/', $matchURI);
        $patternSplit = explode('/', $route->getPattern());

        if (count($uriSplit) != count($patternSplit)) {
            throw new MvcException("uri segments do not match pattern segments");
        }
        $args = array();
        for ($i = 0; $i < count($patternSplit); $i++) {
            if (preg_match('/^:/', $patternSplit[$i])) {
                $key = str_replace(':', '', $patternSplit[$i]);
                $args[$key] = $uriSplit[$i];
            }
        }
        return $args;
    }

    /**
     * 
     * execute a route.
     * 
     * @param Route $route
     * @param type $namedArguments
     * @throws MvcException
     * @throws MvcException
     */
    private function executeRoute(Route $route, $namedArguments) {
        $controllerName = $route->getControllerName();
        $controllerMethod = $route->getMethodName();

        $fileName = $this->config->controllerDir . DIRECTORY_SEPARATOR . $controllerName . '.php';

        if (!file_exists($fileName)) {
            throw new MvcException("Controller file does not exist: $fileName");
        }

        require_once $fileName;

        if (!class_exists($controllerName)) {
            throw new MvcException("Class not found: $controllerName");
        }

        $controller = new $controllerName($this);

        if (!($controller instanceof Controller)) {
            throw new MvcException("$controllerName is not of type Controller");
        }

        if (!method_exists($controller, $controllerMethod)) {
            throw new MvcException("method $controllerMethod does not exist in $controllerName");
        }


        try {
            $reflection = new ReflectionClass($controllerName);
            $reflectionMethod = $reflection->getMethod($controllerMethod);
            $arguments = array();
            //match named uri parameters to method arugument names
            foreach ($reflectionMethod->getParameters() as $key => $param) {
                /* @var \ReflectionParameter $param */
                $arguments[$key] = $namedArguments[$param->getName()];
            }
            call_user_method_array($controllerMethod, $controller, $arguments);
        } catch (MvcException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new MvcException("Uncaught exception calling $controllerName::$controllerMethod", 0, $e);
        }
    }

    /**
     * Return the base URI for the existing web application.
     * 
     * @return type
     */
    public function getBaseUri() {
        return $this->config->baseURI;
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
        $this->routes[] = $route;
    }

    /**
     * Register the framework auto loader.
     */
    public static function registerAutoloader() {
        spl_autoload_register(function($className) {
            $dir = dirname(__DIR__);
            if (preg_match('/^danmvc2/', $className)) {
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

                $this->routes[] = $newRoute;
            }
        }
    }

    private static function configDefaults() {
        $configDefaults = array();
        $configDefaults['timezone'] = "America/New_York";
        $configDefaults['debug'] = false;
        $configDefaults['baseURI'] = '';
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
