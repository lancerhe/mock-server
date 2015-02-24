<?php

namespace Service\Mock\Output;

use Service\Mock;

class Curl {

    protected $_Mock = null;

    public function __construct(Mock $Mock) {
        $this->_Mock = $Mock;
    }

    public function output() {
        $request_query = $this->_Mock->getRequestQuery();
        $request_post  = $this->_Mock->getRequestPost();
        $uri = "http://" . APPLICATION_MOCKSERVER_HOST . $this->_Mock->getUri();
        if ( ! empty( $request_query ) ) {
            $uri .= "?" . http_build_query( $request_query );
        }

        $output = "curl -v";
        if ( ! empty( $request_post ) ) {
            $output .= ' -d "' . http_build_query( $request_post ) . '"';
        }
        return $output . ' "' . $uri . '"';
    }
}