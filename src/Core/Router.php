<?php

namespace danperron\danmvc\Core;

use Exception;
use ReflectionClass;

/**
 * Description of Router
 *
 * @author dan
 */
class Router {

    /**
     *
     * @var array
     */
    private $routes = array();

    /**
     *
     * @var Application
     */
    private $application = null;

    function __construct(Application $application) {
        $this->application = $application;
    }

    public function addRoute(Route $route) {
        $this->routes[] = $route;
    }

    /**
     * Try to find the route that best matches the current path and execute it.
     * 
     * @throws MvcException
     */
    public function doRouting() {
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $queryString = '?' . $_SERVER['QUERY_STRING'];
        $matchPath = str_replace($queryString, '', $_SERVER['REQUEST_URI']);
        $matchPath = str_replace($this->application->getConfig()->basePath, '', $matchPath);
        $this->path = $matchPath;

        foreach ($this->routes as $route) {
            /** @var Route $route * */
            if ($route->match($matchPath, $httpMethod)) {
                $args = $this->getNamedRouteParams($route, $matchPath);
                $this->executeRoute($route, $args);
                return;
            }
        }

        throw new MvcException("No Route found for path '$matchPath'", MvcException::ERROR_CODE_NO_ROUTE);
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

        $fileName = $this->application->getConfig()->controllerDir . DIRECTORY_SEPARATOR . $controllerName . '.php';

        if (!file_exists($fileName)) {
            throw new MvcException("Controller file does not exist: $fileName");
        }

        require_once $fileName;

        if (!class_exists($controllerName)) {
            throw new MvcException("Class not found: $controllerName");
        }

        $controller = new $controllerName($this->application);

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
        } catch (Exception$e) {
            throw new MvcException("Uncaught exception calling $controllerName::$controllerMethod", 0, $e);
        }
    }

}
