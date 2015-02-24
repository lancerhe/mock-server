<?php

namespace Service\Mock;

class Generator {

    public static $mock_path = '/mock';

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
        $mock = [
            "request" => [
                "query" => $Mock->getRequestQuery(),
                "post"  => $Mock->getRequestPost(),
            ],
            "response" => [
                "header" => $Mock->getResponseHeader(),
                "body"   => $Mock->getResponseBody(),
            ],
        ];
        if ( $Mock->getTimeout() ) 
            $mock["response"]["delay"] = $Mock->getTimeout();

        if ( 200 != $status_code = $Mock->getResponseStatusCode() ) 
            $mock["response"]["statusCode"] = $status_code;

        $this->_output[] = $mock;
    }

    private function __output() {
        $output = "exports.mock = " . json_encode($this->_output, JSON_PRETTY_PRINT);
        $output_file = ROOT_PATH. self::$mock_path . $this->_uri['uri'] . ".js";
        $dirname = pathinfo($output_file)['dirname'];
        if ( ! is_dir( $dirname) ) mkdir($dirname, 0775, true);
        file_put_contents($output_file, $output);
    }
}