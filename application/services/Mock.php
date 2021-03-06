<?php
/**
 * Mock Response Entity
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-23
 */
namespace Service;

use Service\Mock\Validator;
use Service\Uri;
use Service\Account;
use Core\Exception\NotFoundRecordException;
use Core\Exception\RequestPermissionException;

class Mock extends \Core_Entity {

    protected $_request_method = 'GET';

    protected $_request_query = [];

    protected $_request_post = [];

    protected $_response_status_code = 200;

    protected $_response_header = [];

    protected $_response_body = '';

    protected $_user = '';

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

    public function getResponseStatusCode() {
        return $this->_response_status_code;
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

    public function getUserFolder() {
        return $this->_user ? ("/" . $this->_user ) : "";
    }

    public function getUserUri() {
        return $this->getUserFolder() . $this->_uri;
    }

    public function getUser() {
        return $this->_user;
    }

    public function isOwner() {
        if ( ! APPLICATION_PHPCAS_OPEN ) {
            return true;
        }
        return $this->_user == Account::getUser();
    }

    public function getUriId() {
        return $this->_uri_id;
    }

    public function setUser($user='') {
        $this->_user = $user ? $user : 'nobody';
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

    public function setResponseStatusCode($response_status_code) {
        if ( ! $response_status_code = intval($response_status_code) ) {
            return;
        }
        $this->_response_status_code = $response_status_code;
    }

    public function setResponseHeader($response_body) {
        $this->_response_header = json_decode($response_body, true);
    }

    public function setRequestQueryByKeyAndValue($request_query_key, $request_query_value) {
        $this->_request_query = [];
        if ( ! is_array($request_query_key) ) 
            return;

        foreach ($request_query_key as $idx => $key)
            $this->_addRequestQuery($key, $request_query_value[$idx]);
    }

    public function setRequestPostByKeyAndValue($request_post_key, $request_post_value) {
        $this->_request_post = [];
        if ( ! is_array($request_post_key) ) 
            return;

        foreach ($request_post_key as $idx => $key) 
            $this->_addRequestPost($key, $request_post_value[$idx]);

        if ( ! empty($this->_request_post) ) {
            $this->_request_method = 'POST';
        }
    }

    public function setResponseHeaderByKeyAndValue($response_header_key, $response_header_value) {
        $this->_response_header = [];
        if ( ! is_array($response_header_key) ) 
            return;

        foreach ($response_header_key as $idx => $key)
            $this->_addResponseHeader($key, $response_header_value[$idx]);
    }

    protected function _addRequestQuery($key, $value) {
        if ( ! $key = trim($key) )
            return;

        if ( ! isset( $this->_request_query[$key] ) )
            $this->_request_query[$key] = $value;
        elseif ( ! is_array( $this->_request_query[$key] ) ) {
            $this->_request_query[$key] = [$this->_request_query[$key], $value];
        } else {
            $this->_request_query[$key][] = $value;
        }
    }

    protected function _addRequestPost($key, $value) {
        if ( ! $key = trim($key) )
            return;

        if ( ! isset( $this->_request_post[$key] ) )
            $this->_request_post[$key] = $value;
        elseif ( ! is_array( $this->_request_post[$key] ) ) {
            $this->_request_post[$key] = [$this->_request_post[$key], $value];
        } else {
            $this->_request_post[$key][] = $value;
        }
    }

    protected function _addResponseHeader($key, $value) {
        if ( ! $key = trim($key) )
            return;

        if ( ! isset( $this->_response_header[$key] ) )
            $this->_response_header[$key] = $value;
        elseif ( ! is_array( $this->_response_header[$key] ) ) {
            $this->_response_header[$key] = [$this->_response_header[$key], $value];
        } else {
            $this->_response_header[$key][] = $value;
        }
    }

    public function setResponseBody($response_body) {
        $this->_response_body = trim($response_body);
    }

    public function setTimeout($timeout) {
        $this->_timeout = intval($timeout);
    }

    public function setUri($uri) {
        $this->_uri = '/' . trim($uri, '/');
    }

    public function setUriId($uri_id) {
        $this->_uri_id = $uri_id;
    }

    public function init($mock, $Uri) {
        $this->setResponseHeader($mock['response_header']);
        $this->setResponseBody($mock['response_body']);
        $this->setResponseStatusCode($mock['response_status_code']);
        $this->setRequestQuery($mock['request_query']);
        $this->setRequestPost($mock['request_post']);
        $this->setTimeout($mock['timeout']);
        $this->setUser($mock['user']);
        $this->setUri($Uri->getUri());
        $this->setUriId($Uri->getId());
        $this->_id = $mock['id'];
    }

    public function query($id) {
        $mock  = (new \Model_Mock())->fetchRowById($id);
        if ( empty($mock) ) {
            throw new NotFoundRecordException();
        }
        $Uri = new Uri();
        $Uri->query($mock['uri_id']);
        $this->init($mock, $Uri);
    }

    public function filterOwner() {
        if ( ! $this->isOwner() ) {
            throw new RequestPermissionException();
        }
    }

    public function create() {
        ( new Validator($this) )->validate();
        $this->_uri_id = (new \Model_Uri())->createIfNotExist($this->_uri);
        $create_row = [
            "uri_id"               => $this->_uri_id,
            "request_query"        => json_encode($this->_request_query),
            "request_post"         => json_encode($this->_request_post),
            "response_header"      => json_encode($this->_response_header),
            "response_status_code" => $this->_response_status_code,
            "response_body"        => $this->_response_body,
            "timeout"              => $this->_timeout,
            "user"                 => $this->_user,
        ];
        $mock_id = (new \Model_Mock())->insertRow($create_row);
    }

    public function save() {
        ( new Validator($this) )->validate();
        $this->_uri_id = (new \Model_Uri())->createIfNotExist($this->_uri);
        $update_row = [
            "uri_id"               => $this->_uri_id,
            "request_query"        => json_encode($this->_request_query),
            "request_post"         => json_encode($this->_request_post),
            "response_header"      => json_encode($this->_response_header),
            "response_status_code" => $this->_response_status_code,
            "response_body"        => $this->_response_body,
            "timeout"              => $this->_timeout,
        ];
        $mock_id  = (new \Model_Mock())->updateRowById($update_row, $this->_id);
    }
}