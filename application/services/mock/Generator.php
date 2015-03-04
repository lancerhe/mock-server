<?php
/**
 * Mock Response Generator for Nodejs server config js
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-20
 */
namespace Service\Mock;

use Service\Mock;
use Service\Uri;
use Service\Account;

class Generator {

    public static $mock_path = '/mock';

    protected $_Uri = null;

    protected $_mock = [];

    protected $_output = [];

    public function __construct($uri_id) {
        $this->_Uri = new Uri();
        $this->_Uri->query($uri_id);

        $Model = new \Model_Mock();
        $list = $Model->fetchListByUriId($uri_id);
        foreach ($list as $mock) {
            $this->_mock[$mock['user']][] = $mock;
        }
    }

    public function generate() {
        foreach ($this->_mock as $user => $list) 
            $this->__generate($list, $user);
    }

    private function __generate($list, $user) {
        $this->_output = [];
        foreach ($list as $idx => $row) {
            $Mock = new Mock();
            $Mock->init($row, $this->_Uri);

            $this->__rebuild($Mock);
        }
        $this->__output($user);
    }

    private function __rebuild( Mock $Mock ) {
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

    private function __output($user) {
        $output = "exports.mock = " . json_encode($this->_output, JSON_PRETTY_PRINT);
        $output_file = ROOT_PATH . self::$mock_path . "/" . $user . $this->_Uri->getUri() . ".js";
        $dirname = pathinfo($output_file)['dirname'];
        if ( ! is_dir( $dirname) ) mkdir($dirname, 0775, true);
        file_put_contents($output_file, $output);
    }
}