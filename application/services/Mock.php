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

    protected $_id = null;

    protected $_timeout = 0;

    public function getRequestMethod() {
        return $this->_request_method;
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

    public function getTimeout() {
        return $this->_timeout;
    }

    public function getId() {
        return $this->_id;
    }

    public function getUri() {
        return $this->_uri;
    }

    public function getUriId() {
        return $this->_uri_id;
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
        $this->_request_query = [];
        foreach ($request_query_key as $idx => $key)
            if ($key) $this->_request_query[$key] = $request_query_value[$idx];
    }

    public function setRequestPostByKeyAndValue($request_post_key, $request_post_value) {
        $this->_request_post = [];
        foreach ($request_post_key as $idx => $key) 
            if ($key) $this->_request_post[$key] = $request_post_value[$idx];

        if ( ! empty($this->_request_post) ) {
            $this->_request_method = 'POST';
        }
    }

    public function setResponseHeaderByKeyAndValue($response_header_key, $response_header_value) {
        $this->_response_header = [];
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

    public function init($mock, $uri) {
        $this->setResponseHeader($mock['response_header']);
        $this->setResponseBody($mock['response_body']);
        $this->setRequestQuery($mock['request_query']);
        $this->setRequestPost($mock['request_post']);
        $this->setTimeout($mock['timeout']);
        $this->setUri($uri['uri']);
        $this->setUriId($uri['id']);
        $this->_id = $mock['id'];
    }

    public function query($id) {
        $mock  = (new \Model_Mock())->fetchRowById($id);
        if ( empty($mock) ) {
            throw new \Core\Exception\NotFoundRecordException();
        }
        $uri   = (new \Model_Uri())->fetchRowById($mock['uri_id']);
        $this->init($mock, $uri);
    }

    public function create() {
        $this->_uri_id = (new \Model_Uri())->createIfNotExist($this->_uri);
        $mock_id = (new \Model_Mock())->insertRow([
            "uri_id"          => $this->_uri_id,
            "request_query"   => json_encode($this->_request_query),
            "request_post"    => json_encode($this->_request_post),
            "response_header" => json_encode($this->_response_header),
            "response_body"   => $this->_response_body,
            "timeout"         => $this->_timeout,
        ]);
    }

    public function save() {
        $this->_uri_id = (new \Model_Uri())->createIfNotExist($this->_uri);
        $mock_id  = (new \Model_Mock())->updateRowById([
            "uri_id"          => $this->_uri_id,
            "request_query"   => json_encode($this->_request_query),
            "request_post"    => json_encode($this->_request_post),
            "response_header" => json_encode($this->_response_header),
            "response_body"   => $this->_response_body,
            "timeout"         => $this->_timeout,
        ], $this->_id);
    }
}