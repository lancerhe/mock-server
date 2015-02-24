<?php

namespace TestCase\Controller;

use \YafUnit\TestCase;
use \YafUnit\TestCase\Controller;

class MockCreateResponseTest extends Controller {

    public function setUp() {
        parent::setUp();
        $this->setUpDatabase();
        $this->setUpGenerateMockFolder();
    }

    public function setUpDatabase() {
        $this->medoo()->setUp();
        $this->medoo()->import("mock.sql");
    }

    public function setUpGenerateMockFolder() {
        \Service\Mock\Generator::$mock_path = '/mocktest';
    }

    public function buildPost($uri) {
        $this->setPost([
            'uri'                   => $uri,
            'timeout'               => 2000,
            'request_query_key'     => ["account", "uid"],
            'request_query_value'   => ["LancerHe", "7"],
            'request_post_key'      => ["action", "module"],
            'request_post_value'    => ["save", "user"],
            'response_header_key'   => ["Content-Type"],
            'response_header_value' => ["text/html"],
            'response_body'         => '{"type":"ajt"}',
        ]);
    }

    public function getMockContent($uri) {
        return file_get_contents(ROOT_PATH . \Service\Mock\Generator::$mock_path . $uri . ".js");
    }

    public function fetchCreateMock() {
        return $this->medoo()->select('mock', '*', ["ORDER" => 'id DESC', "LIMIT" => 1])[0];
    }

    public function assertDatabaseMock($mock) {
        $this->assertEquals('{"account":"LancerHe","uid":"7"}', $mock['request_query']);
        $this->assertEquals('{"action":"save","module":"user"}', $mock['request_post']);
        $this->assertEquals('{"Content-Type":"text\/html"}', $mock['response_header']);
        $this->assertEquals('{"type":"ajt"}', $mock['response_body']);
    }

    /**
     * @test
     */
    public function CreateMockResponse() {
        $this->createRequest("/mock/createmockresponse");
        $this->buildPost('/api/new/testing-1-1-1');
        $this->dispatch();

        $mock = $this->fetchCreateMock();
        $this->assertDatabaseMock($mock);

        $uri = $this->medoo()->select('uri', '*', ["ORDER" => "id DESC", "LIMIT" => 1])[0];
        $this->assertEquals('/api/new/testing-1-1-1', $uri['uri']);
    }

    /**
     * @test
     */
    public function CreateMockResponseWithUseOldUri() {
        $this->createRequest("/mock/createmockresponse");
        $this->buildPost('/mock/api/testing');
        $this->dispatch();

        $mock = $this->fetchCreateMock();
        $this->assertDatabaseMock($mock);
        $this->assertEquals('1', $mock['uri_id']);
    }

    /**
     * @test
     */
    public function CreateMockResponseHeader() {
        $this->createRequest("/mock/createmockresponse");
        $this->setPost([
            'uri'                   => "/index/responseheader",
            'response_header_key'   => ["Content-Type"],
            'response_header_value' => ['application/octet-stream'],
        ]);
        $this->dispatch();

        $mock    = $this->fetchCreateMock();
        $content = $this->getMockContent("/index/responseheader");
        $this->assertEquals('{"Content-Type":"application\/octet-stream"}', $mock['response_header']);
        $this->assertContains('"Content-Type": "application\/octet-stream"', $content);
    }

    /**
     * @test
     */
    public function CreateMockResponseHeaderCookie() {
        $this->createRequest("/mock/createmockresponse");
        $this->setPost([
            'uri'                   => "/index/responseheader",
            'response_header_key'   => [
                'Set-Cookie',
                'Set-Cookie',
                'Set-Cookie',
            ],
            'response_header_value' => [
                'a=1; expires=Tue, 24-Feb-15 09:32:13 GMT; domain=www.baidu.com; path=/',
                'b=17; path=/',
                'PHPSESSID=9kdmhcn8vi2aaujc99l7o5poi3; path=/'
            ],
        ]);
        $this->dispatch();

        $mock    = $this->fetchCreateMock();
        $content = $this->getMockContent("/index/responseheader");

        $this->assertEquals('{"Set-Cookie":["a=1; expires=Tue, 24-Feb-15 09:32:13 GMT; domain=www.baidu.com; path=\/","b=17; path=\/","PHPSESSID=9kdmhcn8vi2aaujc99l7o5poi3; path=\/"]}', $mock['response_header']);

        $this->assertContains('"a=1; expires=Tue, 24-Feb-15 09:32:13 GMT; domain=www.baidu.com; path=\/"', $content);
        $this->assertContains('"b=17; path=\/"', $content);
        $this->assertContains('"PHPSESSID=9kdmhcn8vi2aaujc99l7o5poi3; path=\/"', $content);
    }

    public function tearDown() {
        parent::tearDown();
        shell_exec("rm -rf " . ROOT_PATH . "/mocktest");
    }
}