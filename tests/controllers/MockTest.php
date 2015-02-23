<?php

namespace TestCase\Controller;

use \YafUnit\TestCase;
use \YafUnit\TestCase\Controller;

class MockTest extends Controller {

    public function setUp() {
        parent::setUp();
        $this->setUpDatabase();
    }

    public function setUpDatabase() {
        $this->medoo()->setUp();
        $this->medoo()->import("mock.sql");
    }

    /**
     * @test
     */
    public function IndexAction() {
        $this->createRequest("/mock/index");
        $this->dispatch();

        $this->assertEquals('/mock/api/testing', $this->getView()->list[1]['uri']);
    }

    /**
     * @test
     */
    public function ListAction() {
        $this->createRequest("/mock/list");
        $this->setQuery("id", "1");
        $this->dispatch();

        $this->assertCount(2,       $this->getView()->list);
        $this->assertEquals('1',    $this->getView()->list[1]['uri_id']);
        $this->assertEquals('2000', $this->getView()->list[1]['timeout']);
    }

    /**
     * @test
     */
    public function CreateAction() {
        $this->createRequest("/mock/create");
        $this->setQuery("uri_id", "1");
        $this->dispatch();

        $this->assertEquals('/mock/api/testing', $this->getView()->uri['uri']);
    }

    /**
     * @test
     */
    public function UpdateAction() {
        $this->createRequest("/mock/update");
        $this->setQuery("id", "2");
        $this->dispatch();

        $this->assertEquals('2', $this->getView()->mock['id']);
        $this->assertEquals('text/html', $this->getView()->response_header['Content-Type']);
        $this->assertEquals('{s:3}',     $this->getView()->response_body);
        $this->assertEquals('get',  $this->getView()->request_query['key']);
        $this->assertEquals('post', $this->getView()->request_post['key']);
        $this->assertEquals('/mock/api/testing', $this->getView()->uri['uri']);
    }

    /**
     * @test
     */
    public function CreateMockResponseAction() {
        $this->createRequest("/mock/createmockresponse");
        $this->setPost([
            'uri'                   => '/api/new/testing',
            'timeout'               => 2000,
            'request_query_key'     => ["account", "uid"],
            'request_query_value'   => ["LancerHe", "7"],
            'request_post_key'      => ["action", "module"],
            'request_post_value'    => ["save", "user"],
            'response_header_key'   => ["Content-Type"],
            'response_header_value' => ["text/html"],
            'response_body'         => '{"type":"ajt"}',
        ]);
        $this->dispatch();

        $row = $this->medoo()->select('mock', '*', ["ORDER" => 'id DESC', "LIMIT" => 1])[0];
        $this->assertEquals('{"account":"LancerHe","uid":"7"}', $row['request_query']);
        $this->assertEquals('{"action":"save","module":"user"}', $row['request_post']);
        $this->assertEquals('{"Content-Type":"text\/html"}', $row['response_header']);
        $this->assertEquals('{"type":"ajt"}', $row['response_body']);
    }

    /**
     * @test
     */
    public function SaveMockResponseNotFoundByQuery() {
        $this->setExpectedException('\Core\Exception\NotFoundRecordException');
        $this->createRequest("/mock/savemockresponse");
        $this->setPost([
            'id'                    => '99',
            'uri'                   => '/api/new/testing',
            'timeout'               => 2000,
            'request_query_key'     => ["account", "uid"],
            'request_query_value'   => ["LancerHe", "7"],
            'request_post_key'      => ["action", "module"],
            'request_post_value'    => ["save", "user"],
            'response_header_key'   => ["Content-Type"],
            'response_header_value' => ["text/html"],
            'response_body'         => '{"type":"ajt"}',
        ]);
        $this->dispatch();
    }

    /**
     * @test
     */
    public function SaveMockResponseAction() {
        $this->createRequest("/mock/savemockresponse");
        $this->setPost([
            'id'                    => '2',
            'uri'                   => '/api/new/testing',
            'timeout'               => 2000,
            'request_query_key'     => ["account", "uid"],
            'request_query_value'   => ["LancerHe", "7"],
            'request_post_key'      => ["action", "module"],
            'request_post_value'    => ["save", "user"],
            'response_header_key'   => ["Content-Type"],
            'response_header_value' => ["text/html"],
            'response_body'         => '{"type":"ajt"}',
        ]);
        $this->dispatch();

        $mock = $this->medoo()->select('mock', '*', ["id" => '2'])[0];
        $this->assertEquals('{"account":"LancerHe","uid":"7"}', $mock['request_query']);
        $this->assertEquals('{"action":"save","module":"user"}', $mock['request_post']);
        $this->assertEquals('{"Content-Type":"text\/html"}', $mock['response_header']);
        $this->assertEquals('{"type":"ajt"}', $mock['response_body']);
        $this->assertNotEquals('2', $mock['uri_id']);

        $uri = $this->medoo()->select('uri', '*', ["id" => $mock['uri_id']])[0];
        
        $this->assertEquals('/api/new/testing', $uri['uri']);
    }
}