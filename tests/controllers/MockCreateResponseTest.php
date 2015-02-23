<?php

namespace TestCase\Controller;

use \YafUnit\TestCase;
use \YafUnit\TestCase\Controller;

class MockCreateResponseTest extends Controller {

    public function setUp() {
        parent::setUp();
        $this->setUpDatabase();
    }

    public function setUpDatabase() {
        $this->medoo()->setUp();
        $this->medoo()->import("mock.sql");
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

        $mock = $this->medoo()->select('mock', '*', ["ORDER" => 'id DESC', "LIMIT" => 1])[0];
        $this->assertDatabaseMock($mock);

        $uri = $this->medoo()->select('uri', '*', ["ORDER" => "id DESC", "LIMIT" => 1])[0];
        $this->assertEquals('/api/new/testing-1-1-1', $uri['uri']);
    }

    /**
     * @test
     */
    public function SaveMockResponseWithUseOldUri() {
        $this->createRequest("/mock/createmockresponse");
        $this->buildPost('/mock/api/testing');
        $this->dispatch();

        $mock = $this->medoo()->select('mock', '*', ["ORDER" => 'id DESC', "LIMIT" => 1])[0];
        $this->assertDatabaseMock($mock);
        $this->assertEquals('1', $mock['uri_id']);
    }
}