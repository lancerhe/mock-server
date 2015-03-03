<?php
/**
 * Mock Server Manager Controller
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-13
 */
class Controller_Mock extends \Core\Controller\Main {

    public function IndexAction() {
        $Model = new Model_Uri();
        $list = $Model->fetchList();

        $this->getView()->assign('list', $list);
        $this->getView()->assign('service_status', ( new \Service\Mock\Console() )->status());
        $this->getView()->display('mock/index.html');
    }

    public function ListAction() {
        $uri_id = intval( $this->getRequest()->getQuery('id') );

        $ServiceRender = new \Service\Mock\PageListRender($uri_id);
        $ServiceRender->render();

        $this->getView()->assign('list',    $ServiceRender->getMockList());
        $this->getView()->assign('uri_id',  $ServiceRender->getUri()->getId());
        $this->getView()->assign('uri',     $ServiceRender->getUri()->getUri());
        $this->getView()->display('mock/list.html');
    }

    public function CreateAction() {
        $this->getView()->display('mock/create.html');
    }

    public function UpdateAction() {
        $id = intval( $this->getRequest()->getQuery('id') );

        $Mock = new \Service\Mock();
        $Mock->query($id);
        $this->getView()->assign('response_header',      $Mock->getResponseHeader() );
        $this->getView()->assign('response_body',        $Mock->getResponseBody() );
        $this->getView()->assign('response_status_code', $Mock->getResponseStatusCode() );
        $this->getView()->assign('request_query',        $Mock->getRequestQuery() );
        $this->getView()->assign('request_post',         $Mock->getRequestPost() );
        $this->getView()->assign('id',              $Mock->getId());
        $this->getView()->assign('uri_id',          $Mock->getUriId());
        $this->getView()->assign('uri',             $Mock->getUri());
        $this->getView()->assign('timeout',         $Mock->getTimeout());
        $this->getView()->display('mock/update.html');
    }
}