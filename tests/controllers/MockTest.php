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
        $this->medoo()->query("INSERT INTO `uri`(`id`, `uri`) VALUES
            ('1', '/mock/api/testing'),
            ('2', '/uri/api/testing');");
        $this->medoo()->query("INSERT INTO `mock`(`id`, `uri_id`, `request_query`, `request_post`, `response_header`, `response_body`, `timeout`) VALUES
            ('1', '1', '{}', '{}', '{}', '{s:2}', '2000'), 
            ('2', '1', '{\"key\":\"get\"}', '{\"key\":\"post\"}', '{\"Content-Type\":\"text\\\/html\"}', '{s:3}', '4000'), 
            ('3', '2', '{\"key\":\"get\"}', '{\"key\":\"post\"}', '{\"Content-Type\":\"text\\\/html\"}', '{s:3}', '4000');");
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
        $this->setQuery("id", "1");
        $this->dispatch();

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
}