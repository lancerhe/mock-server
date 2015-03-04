<?php
/**
 * Mock Response Output Curl Demo
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-20
 */
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
        $uri = "http://" . APPLICATION_MOCKSERVER_HOST . $this->_Mock->getUserUri();
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