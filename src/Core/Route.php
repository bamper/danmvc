<?php

namespace danperron\danmvc\Core;

/**
 * Description of Route
 *
 * @author dan
 */
class Route {

    private $pattern = "";
    private $controllerName = "";
    private $methodName = "";
    private $httpMethods = array();

    const HTTP_METHOD_GET = "GET";
    const HTTP_METHOD_POST = "POST";
    const HTTP_METHOD_PUT = "PUT";
    const HTTP_METHOD_DELETE = "DELETE";
    const HTTP_METHOD_OPTIONS = "OPTIONS";

    function __construct($pattern, $controllerName, $methodName = 'index', $httpMethods = array(Route::HTTP_METHOD_GET, Route::HTTP_METHOD_POST)) {
        $this->pattern = $pattern;
        $this->controllerName = $controllerName;
        $this->methodName = $methodName;
        $this->httpMethods = $httpMethods;
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function getControllerName() {
        return $this->controllerName;
    }

    public function getMethodName() {
        return $this->methodName;
    }

    public function getHttpMethod() {
        return $this->httpMethod;
    }

    /**
     * 
     * @param string $path
     * @param string $httpMethod
     * @return boolean
     */
    public function match($path, $httpMethod) {

        if (!in_array($httpMethod, $this->httpMethods)) {
            return false;
        }
        
        
        //$path = preg_replace('/\/$/', '', $path);
        $regex = '/^' . str_replace('/', '\/', $this->getPattern()) . '$/';
        $regex = preg_replace('/\:[a-zA-Z0-9]+/', '[^\/]+', $regex);
        //$regex = preg_replace('/*/', '.*?', $regex);

        if (preg_match($regex, $path)) {
            return true;
        }
        return false;
    }

}
