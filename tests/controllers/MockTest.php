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
    public function ListExceptionWithNotFound() {
        $this->setExpectedException('\Core\Exception\NotFoundRecordException');
        $this->createRequest("/mock/list");
        $this->setQuery("id", "999");
        $this->dispatch();
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
        $this->assertContains('HTTP/1.1', $this->getView()->list[1]['output_http']);
        $this->assertContains('curl',     $this->getView()->list[1]['output_curl']);
    }

    /**
     * @test
     */
    public function CreateAction() {
        $this->createRequest("/mock/create");
        $this->dispatch();
        // $this->assertEquals('/mock/api/testing', $this->getView()->uri['uri']);
    }

    /**
     * @test
     */
    public function UpdateAction() {
        $this->createRequest("/mock/update");
        $this->setQuery("id", "2");
        $this->dispatch();

        $this->assertEquals('2', $this->getView()->id);
        $this->assertEquals('text/html', $this->getView()->response_header['Content-Type']);
        $this->assertEquals('{s:3}',     $this->getView()->response_body);
        $this->assertEquals('get',  $this->getView()->request_query['key']);
        $this->assertEquals('post', $this->getView()->request_post['key']);
        $this->assertEquals('4000', $this->getView()->timeout);
        $this->assertEquals('/mock/api/testing', $this->getView()->uri);
    }
}