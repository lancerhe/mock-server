<?php

namespace Service\Mock;

class PageListRender {

    protected $_uri = [];

    protected $_mock = [];

    public function __construct($uri_id) {
        $Model = new \Model_Uri();
        $this->_uri   = $Model->fetchRowById($uri_id);

        $Model = new \Model_Mock();
        $this->_mock  = $Model->fetchListByUriId($uri_id);
    }

    public function render() {
        foreach ($this->_mock as $idx => $row) {
            $Mock = new \Service\Mock();
            $Mock->setResponseHeader($row['response_header']);
            $Mock->setResponseBody($row['response_body']);
            $Mock->setRequestQuery($row['request_query']);
            $Mock->setRequestPost($row['request_post']);
            $Mock->setUri($this->_uri['uri']);

            $this->_mock[$idx]['http_request']  = $Mock->getHttpRequestString();
            $this->_mock[$idx]['http_response'] = $Mock->getHttpResponseString();
        }
    }

    public function getMockList() {
        return $this->_mock;
    }

    public function getUri() {
        return $this->_uri;
    }
}