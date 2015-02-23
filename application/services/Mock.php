<?php

namespace Service;

class Mock extends \Core_Entity {

    protected $_request_method = 'GET';

    protected $_request_query = [];

    protected $_request_post = [];

    protected $_response_header = [];

    protected $_response_body = '';

    protected $_uri = '';

    protected $_uri_id = null;

    protected $_timeout = 0;

    public function getHttpRequestString() {
        $uri = $this->_uri;
        $request_body_string = '';
        if ( ! empty( $this->_request_query ) ) {
            $uri .= "?" . http_build_query($this->_request_query);
        }
        if ( ! empty( $this->_request_post ) ) {
            $method = "POST";
            $request_body_string = http_build_query($this->_request_post);
        }
        return "{$this->_request_method} $uri HTTP/1.1\r\n".
                "HOST: " . APPLICATION_MOCKSERVER_HOST . "\r\n".
                "\r\n" . 
                $request_body_string;
    }

    public function getHttpResponseString() {
        $response_header_string = '';

        if ( ! empty( $this->_response_header ) ) {
            foreach ($this->_response_header as $key => $value) {
                $response_header_string .= "$key: $value\r\n";
            }
        }

        return "HTTP/1.1 200 OK\r\n" . 
                $response_header_string . "\r\n" . 
                $this->_response_body;
    }

    public function getRequestQuery() {
        return $this->_request_query;
    }

    public function getRequestPost() {
        return $this->_request_post;
    }

    public function getResponseHeader() {
        return $this->_response_header;
    }

    public function getResponseBody() {
        return $this->_response_body;
    }

    public function setRequestQuery($request_query) {
        $this->_request_query = json_decode($request_query, true);
    }

    public function setRequestPost($request_post) {
        $this->_request_post = json_decode($request_post, true);
        if ( ! empty($this->_request_post) &&  ! $request_post ) {
            $this->_request_method = 'POST';
        }
    }

    public function setResponseHeader($response_body) {
        $this->_response_header = json_decode($response_body, true);
    }

    public function setRequestQueryByKeyAndValue($request_query_key, $request_query_value) {
        foreach ($request_query_key as $idx => $key)
            if ($key) $this->_request_query[$key] = $request_query_value[$idx];
    }

    public function setRequestPostByKeyAndValue($request_post_key, $request_post_value) {
        foreach ($request_post_key as $idx => $key) 
            if ($key) $this->_request_post[$key] = $request_post_value[$idx];

        if ( ! empty($this->_request_post) ) {
            $this->_request_method = 'POST';
        }
    }

    public function setResponseHeaderByKeyAndValue($response_header_key, $response_header_value) {
        foreach ($response_header_key as $idx => $key) 
            if ($key) $this->_response_header[$key] = $response_header_value[$idx];
    }

    public function setResponseBody($response_body) {
        $this->_response_body = $response_body;
    }

    public function setTimeout($timeout) {
        $this->_timeout = $timeout;
    }

    public function setUri($uri) {
        $this->_uri = '/' . trim($uri, '/');
    }

    public function setUriId($uri_id) {
        $this->_uri_id = $uri_id;
    }

    public function create() {
        $Model  = new \Model_Uri();
        if ( ! $uri_id = $Model->medoo()->insert('uri', ["uri" => $this->_uri]) ) {
            throw new \Core\Exception\DatabaseWriteException();
        }
        $this->_uri_id = $uri_id;

        $Model = new \Model_Mock();
        $mock_id  = $Model->medoo()->insert('mock', [
            "uri_id"          => $this->_uri_id,
            "request_query"   => json_encode($this->_request_query),
            "request_post"    => json_encode($this->_request_post),
            "response_header" => json_encode($this->_response_header),
            "response_body"   => $this->_response_body,
            "timeout"         => $this->_timeout,
        ]);
    }

    public function save($id) {
        $mock  = (new \Model_Mock())->fetchRowById($id);
        if ( empty($mock) ) {
            throw new \Core\Exception\NotFoundRecordException();
        }

        $uri_id = (new \Model_Uri())->medoo()->select('uri', 'id', ["uri" => $this->_uri]);
        $uri_id = isset($uri_id[0]) ? $uri_id[0] : false;
        if ( ! $uri_id ) {
            if ( ! $uri_id = (new \Model_Uri())->medoo()->insert('uri', ["uri" => $this->_uri]) ) {
                throw new \Core\Exception\DatabaseWriteException();
            }
        }

        $this->_uri_id = $uri_id;
        $Model = new \Model_Mock();
        $mock_id  = $Model->medoo()->update('mock', [
            "uri_id"          => $this->_uri_id,
            "request_query"   => json_encode($this->_request_query),
            "request_post"    => json_encode($this->_request_post),
            "response_header" => json_encode($this->_response_header),
            "response_body"   => $this->_response_body,
            "timeout"         => $this->_timeout,
        ], ["id" => $id] );
    }
}