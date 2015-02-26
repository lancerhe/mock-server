<?php

namespace Service\Mock;

class PageListRender {

    protected $_Uri = null;

    protected $_mock = [];

    public function __construct($uri_id) {
        $this->_Uri = new \Service\Uri();
        $this->_Uri->query($uri_id);

        $Model = new \Model_Mock();
        $this->_mock  = $Model->fetchListByUriId($this->_Uri->getId());
    }

    public function render() {
        foreach ($this->_mock as $idx => $row) {
            $Mock = new \Service\Mock();
            $Mock->init($row, $this->_Uri);

            $ServiceHttp = new \Service\Mock\Output\HTTPProtocol($Mock);
            $ServiceCurl = new \Service\Mock\Output\Curl($Mock);

            $this->_mock[$idx]['output_http'] = $ServiceHttp->output();
            $this->_mock[$idx]['output_curl'] = $ServiceCurl->output();
        }
    }

    public function getMockList() {
        return $this->_mock;
    }

    public function getUri() {
        return $this->_Uri;
    }
}