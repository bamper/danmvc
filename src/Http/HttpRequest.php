<?php
namespace danperron\danmvc\Http;
/**
 * Description of HttpRequest
 *
 * @author Dan
 */
class HttpRequest {
    
    private $url = '';
    private $data = null;
    private $method = 'GET';
    private $headers = array();
    
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    
    
    public function __construct($url = '') {
        $this->url = $url;
    }
    
    public function getUrl(){
        return $this->url;
    }
    
    public function setUrl($url){
        $this->url = $url;
    }
    
    public function getData(){
        return $this->data;
    }
    
    public function setData($data){
        $this->data = $data;
    }
    
    public function addHeader($header){
        $this->headers[] = $header;
    }
    
    public function getHeaders(){
        return $this->headers;
    }
    
    public function setHeaders($array){
        $this->headers = $array;
    }
    
    public function getMethod(){
        return $this->method;
    }
    
    public function setMethod($method){
        $this->method = $method;
    }
    
}