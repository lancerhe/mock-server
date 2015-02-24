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
            $Mock->init($row, $this->_uri);

            $ServiceHttpOutput = new \Service\Mock\Output\HTTPProtocol($Mock);

            $this->_mock[$idx]['output_http'] = $ServiceHttpOutput->output();
        }
    }

    public function getMockList() {
        return $this->_mock;
    }

    public function getUri() {
        return $this->_uri;
    }
}