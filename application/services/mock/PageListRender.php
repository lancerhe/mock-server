<?php
/**
 * Mock Response render for page.
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-19
 */
namespace Service\Mock;

use Service\Mock;
use Service\Uri;
use Service\Mock\Output\HTTPProtocol;
use Service\Mock\Output\Curl;
use Service\Mock\Output\PHP;

class PageListRender {

    protected $_Uri = null;

    protected $_mock = [];

    public function __construct($uri_id) {
        $this->_Uri = new Uri();
        $this->_Uri->query($uri_id);

        $Model = new \Model_Mock();
        $this->_mock  = $Model->fetchListByUriId($this->_Uri->getId());
    }

    public function render() {
        foreach ($this->_mock as $idx => $row) {
            $Mock = new Mock();
            $Mock->init($row, $this->_Uri);

            $ServiceHttp = new HTTPProtocol($Mock);
            $ServiceCurl = new Curl($Mock);
            $ServicePHP  = new PHP($Mock);

            $this->_mock[$idx]['output_http'] = $ServiceHttp->output();
            $this->_mock[$idx]['output_curl'] = $ServiceCurl->output();
            $this->_mock[$idx]['output_php']  = $ServicePHP->output();
        }
    }

    public function getMockList() {
        return $this->_mock;
    }

    public function getUri() {
        return $this->_Uri;
    }
}