<?php

namespace Service\Mock\Output;

use Service\Mock;

class HTTPProtocol {

    protected $_Mock = null;

    protected $_output = '';

    public function __construct(Mock $Mock) {
        $this->_Mock = $Mock;
    }

    public function output() {
        $this->_buildRequestString();
        $this->_buildResponseString();
        return $this->_output;
    }

    protected function _buildRequestString() {
        $uri = $this->_Mock->getUri();
        $request_body_string = '';
        $method        = $this->_Mock->getRequestMethod();
        $request_query = $this->_Mock->getRequestQuery();
        $request_post  = $this->_Mock->getRequestPost();
        if ( ! empty( $request_query ) ) {
            $uri .= "?" . http_build_query( $request_query );
        }
        if ( ! empty( $request_post ) ) {
            $method = "POST";
            $request_body_string = http_build_query( $request_post );
        }
        $this->_output .= 
            "> $method $uri HTTP/1.1\r\n".
            "> HOST: " . APPLICATION_MOCKSERVER_HOST . "\r\n".
            "> Accept: */*\r\n".
            "> Content-Length: " . strlen($request_body_string) . "\r\n" . 
            "> \r\n" . 
            "$request_body_string\r\n";
    }

    protected function _buildResponseString() {
        $this->_output .= 
            "< HTTP/1.1 200 OK\r\n";

        foreach ($this->_Mock->getResponseHeader() as $key => $mixed) {
            if ( ! is_array($mixed) )
                $mixed = [$mixed];

            foreach ($mixed as $value)
                $this->_output .= "< $key: $value\r\n";
        }
        $this->_output .= 
            "< \r\n" . 
            $this->_Mock->getResponseBody();
    }
}