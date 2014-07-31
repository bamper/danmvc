<?php
namespace danperron\danmvc\Http;

/**
 * Description of HttpResponse
 *
 * @author Dan
 */
class HttpResponse {
    
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMINANTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_TEMP_REDIRECT = 307;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    
    
    private $body = '';
    private $contentType = '';
    private $responseCode = 0;
    
    public function __construct($code, $contentType, $body) {
        $this->responseCode = $code;
        $this->contentType = $contentType;
        $this->body = $body;
    }
    
    public function getResponseCode(){
        return $this->responseCode;
    }
    
    public function getContentType(){
        return $this->contentType;
    }
    
    public function getResponseBody(){
        return $this->body;
    }
}