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
    public function CreateMockResponseWithNewUri() {
        $this->createRequest("/ajax/mockhandler/create");
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
        $this->createRequest("/ajax/mockhandler/create");
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
        $this->createRequest("/ajax/mockhandler/create");
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
        $this->createRequest("/ajax/mockhandler/create");
        $this->setPost([
            'uri'                   => "/index/responseheadercookie",
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
        $content = $this->getMockContent("/index/responseheadercookie");

        $this->assertEquals('{"Set-Cookie":["a=1; expires=Tue, 24-Feb-15 09:32:13 GMT; domain=www.baidu.com; path=\/","b=17; path=\/","PHPSESSID=9kdmhcn8vi2aaujc99l7o5poi3; path=\/"]}', $mock['response_header']);

        $this->assertContains('"a=1; expires=Tue, 24-Feb-15 09:32:13 GMT; domain=www.baidu.com; path=\/"', $content);
        $this->assertContains('"b=17; path=\/"', $content);
        $this->assertContains('"PHPSESSID=9kdmhcn8vi2aaujc99l7o5poi3; path=\/"', $content);
    }

    /**
     * @test
     */
    public function CreateMockResponseHeaderLocation() {
        $this->createRequest("/ajax/mockhandler/create");
        $this->setPost([
            'uri'                   => "/index/responseheaderlocation",
            'response_header_key'   => [
                'Location',
            ],
            'response_header_value' => [
                'http://192.168.156.124/?login={$request.get.username}',
            ],
            'response_status_code' => '302',
        ]);
        $this->dispatch();

        $mock    = $this->fetchCreateMock();
        $content = $this->getMockContent("/index/responseheaderlocation");

        $this->assertEquals("302", $mock['response_status_code']);
        $this->assertEquals('{"Location":"http:\/\/192.168.156.124\/?login={$request.get.username}"}', $mock['response_header']);
        $this->assertContains('"statusCode": 302', $content);
        $this->assertContains('"Location": "http:\/\/192.168.156.124\/?login={$request.get.username}"', $content);
    }

    /**
     * @test
     */
    public function CreateMockRequestPost() {
        $this->createRequest("/ajax/mockhandler/create");
        $this->setPost([
            'uri'                   => "/index/requestpost",
            'request_post_key'   => [
                'username',
                'avatar',
                'avatar',
            ],
            'request_post_value' => [
                'LancerHe',
                '1.jpg',
                '2.jpg',
            ],
        ]);
        $this->dispatch();

        $mock    = $this->fetchCreateMock();
        $content = $this->getMockContent("/index/requestpost");

        $this->assertEquals('{"username":"LancerHe","avatar":["1.jpg","2.jpg"]}', $mock['request_post']);

        $this->assertContains('"username": "LancerHe"', $content);
        $this->assertContains('"1.jpg"', $content);
        $this->assertContains('"2.jpg"', $content);
    }

    /**
     * @test
     */
    public function CreateMockRequestQuery() {
        $this->createRequest("/ajax/mockhandler/create");
        $this->setPost([
            'uri'                   => "/index/requestquery",
            'request_query_key'   => [
                'username',
                'pid',
                'pid',
            ],
            'request_query_value' => [
                'LancerHe',
                '12323',
                '23322',
            ],
        ]);
        $this->dispatch();

        $mock    = $this->fetchCreateMock();
        $content = $this->getMockContent("/index/requestquery");

        $this->assertEquals('{"username":"LancerHe","pid":["12323","23322"]}', $mock['request_query']);

        $this->assertContains('"username": "LancerHe"', $content);
        $this->assertContains('"12323"', $content);
        $this->assertContains('"23322"', $content);
    }

    public function tearDown() {
        parent::tearDown();
        shell_exec("rm -rf " . ROOT_PATH . "/mocktest");
    }
}