<?php
namespace danperron\danmvc\Http;

use danperron\danmvc\Core\MvcException;
/**
 * The HttpHeaders class holds descriptions for commonly sent HTTP headers and
 * some basic functionality to build those headers given an HTTP code.
 * 
 */
class HttpHeaders{
    
    private function __construct(){}
    private function __clone(){}
    
    const HTTP_OK = 'HTTP/1.1 200 OK';
    const HTTP_NOT_FOUND = 'HTTP/1.1 404 Not Found';
    const HTTP_SERVER_ERROR = 'HTTP/1.1 500 Internal Server Error';
    const HTTP_TEMPORARILY_UNAVAILABLE = 'HTTP/1.1 503 Service Temporarily Unavailable';
    const HTTP_MOVED_PERMANENTLY = 'HTTP/1.1 301 Moved Permanently';
    const HTTP_FOUND = 'HTTP/1.1 302 Found';
    const HTTP_FORBIDDEN = 'HTTP/1.1 403 Forbidden';
    const HTTP_UNAUTHORIZED = 'HTTP/1.1 401 Unauthorized';
    
    private static $httpCodes = array (
        200 => self::HTTP_OK,
        404 => self::HTTP_NOT_FOUND,
        500 => self::HTTP_SERVER_ERROR,
        503 => self::HTTP_TEMPORARILY_UNAVAILABLE,
        301 => self::HTTP_MOVED_PERMANENTLY,
        302 => self::HTTP_FOUND,
        403 => self::HTTP_FORBIDDEN,
        401 => self::HTTP_UNAUTHORIZED
    );
    
    public static function buildHeader($code){
        if(!isset(self::$httpCodes[$code])){
            throw new MvcException("HttpHeaders: No mapping found for code $code");
        }
        
        return self::$httpCodes[$code];
    }
    
}