<?php

namespace danperron\danmvc\Http;

/**
 * HttpClient is an object oriented wrapper for php_curl.  It allows you to make
 * HTTP calls intuitavely by building a request
 *
 * @author Dan
 */
class HttpClient {

    const AUTHMODE_NONE = 'NONE';
    const AUTHMODE_BASIC = 'BASIC_AUTH';

    private $authMode = self::AUTHMODE_NONE;
    private $authUsername = '';
    private $authPassword = '';

    /**
     * Execute an HttpRequest and return an HttpResponse.
     * 
     * @param \danperron\danmvc\Http\HttpRequest $request
     * @return HttpResponse
     * @throws HttpException
     */
    public function execute(HttpRequest $request) {
        switch ($request->getMethod()) {
            case HttpRequest::METHOD_POST:
                return $this->doPost($request);
            case HttpRequest::METHOD_GET:
                return $this->doGet($request);
            case HttpRequest::METHOD_DELETE:
                return $this->doDelete($request);
            case HttpRequest::METHOD_PUT:
                return $this->doPut($request);
            default:
                throw new HttpException("Method {$request->getMethod()} not recognized.");
        }
    }

    /**
     * 
     * @param HttpRequest $request
     * @return HttpResponse
     */
    private function doGet(HttpRequest $request) {
        $ch = \curl_init($request->getUrl());
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getHeaders());

        return $this->handleResponse($ch);
    }

    /**
     * 
     * @param HttpRequest $request
     * @return HttpResponse
     */
    private function doPost(HttpRequest $request) {
        $ch = \curl_init($request->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getData());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getHeaders());
        return $this->handleResponse($ch);
    }

    private function doDelete(HttpRequest $request) {
        $ch = \curl_init($request->getUrl());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getData());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getHeaders());
        return $this->handleResponse($ch);
    }

    private function doPut(HttpRequest $request) {
        $ch = \curl_init($request->getUrl());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getData());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getHeaders());
        return $this->handleResponse($ch);
    }

    private function handleResponse($ch) {
        $response = \curl_exec($ch);
        $curlInfo = \curl_getinfo($ch);

        if (\curl_errno($ch)) {
            throw new HttpException(\curl_error($ch), \curl_errno($ch));
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        //TODO: parse headers and add to response object
        $body = substr($response, $headerSize);

        return new HttpResponse($curlInfo['http_code'], $curlInfo['content_type'], $body);
    }

    public function setAuthMode($authMode) {
        $this->authMode = $authMode;
    }

    public function setAuthCredentials($username, $password) {
        $this->authUsername = $username;
        $this->authPassword = $password;
    }

}
