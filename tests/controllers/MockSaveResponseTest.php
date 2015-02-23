<?php

namespace TestCase\Controller;

use \YafUnit\TestCase;
use \YafUnit\TestCase\Controller;

class MockSaveResponseTest extends Controller {

    public function setUp() {
        parent::setUp();
        $this->setUpDatabase();
    }

    public function setUpDatabase() {
        $this->medoo()->setUp();
        $this->medoo()->import("mock.sql");
    }

    public function buildPost($id, $uri) {
        $this->setPost([
            'id'                    => $id,
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
    public function SaveMockResponseNotFoundByQuery() {
        $this->setExpectedException('\Core\Exception\NotFoundRecordException');
        $this->createRequest("/mock/savemockresponse");
        $this->buildPost(99, '/api/new/testing');
        $this->dispatch();
    }

    /**
     * @test
     */
    public function SaveMockResponseWithCreateNewUri() {
        $this->createRequest("/mock/savemockresponse");
        $this->buildPost(2, '/api/new/testing');
        $this->dispatch();

        $mock = $this->medoo()->select('mock', '*', ["id" => '2'])[0];
        $this->assertDatabaseMock($mock);
        $this->assertNotEquals('2', $mock['uri_id']);

        $uri = $this->medoo()->select('uri', '*', ["id" => $mock['uri_id']])[0];
        $this->assertEquals('/api/new/testing', $uri['uri']);
    }

    /**
     * @test
     */
    public function SaveMockResponseWithUseOldUri() {
        $this->createRequest("/mock/savemockresponse");
        $this->buildPost(2, '/uri/api/testing');
        $this->dispatch();

        $mock = $this->medoo()->select('mock', '*', ["id" => '2'])[0];
        $this->assertDatabaseMock($mock);
        $this->assertEquals('2', $mock['uri_id']);
    }
}