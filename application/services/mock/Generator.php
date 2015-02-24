<?php

namespace Service\Mock;

class Generator {

    protected $_uri = [];

    protected $_mock = [];

    protected $_output = [];

    public function __construct($uri_id) {
        $Model = new \Model_Uri();
        $this->_uri   = $Model->fetchRowById($uri_id);

        $Model = new \Model_Mock();
        $this->_mock  = $Model->fetchListByUriId($uri_id);
    }

    public function generate() {
        foreach ($this->_mock as $idx => $row) {
            $Mock = new \Service\Mock();
            $Mock->init($row, $this->_uri);

            $this->__rebuild($Mock);
        }
        $this->__output();
    }

    private function __rebuild( \Service\Mock $Mock ) {
        $this->_output[] = [
            "request" => [
                "query" => $Mock->getRequestQuery(),
                "post"  => $Mock->getRequestPost(),
            ],
            "response" => [
                "delay"  => $Mock->getTimeout(),
                "header" => $Mock->getResponseHeader(),
                "body"   => $Mock->getResponseBody(),
            ],
        ];
    }

    private function __output() {
        $output = "exports.mock = " . json_encode($this->_output);
        $output_file = ROOT_PATH. "/mock" . $this->_uri['uri'] . ".js";
        $dirname = pathinfo($output_file)['dirname'];
        if ( ! is_dir( $dirname) ) mkdir($dirname, 0775, true);
        file_put_contents($output_file, $output);
    }
}