<?php
/**
 * Mock Server Manager Controller
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-13
 */
class Controller_Mock extends \Core\Controller\Main {

    public function init() {
        \Yaf\Dispatcher::getInstance()->disableView();
    }

    public function IndexAction() {
        $Model = new Model_Uri();
        $list = $Model->fetchList();
        $this->getView()->assign('list', $list);
        $this->getView()->display('mock/index.html');
    }

    public function ListAction() {
        $uri_id = intval( $this->getRequest()->getQuery('id') );

        $ServiceRender = new \Service\Mock\PageListRender($uri_id);
        $ServiceRender->render();

        $this->getView()->assign('list', $ServiceRender->getMockList());
        $this->getView()->assign('uri',  $ServiceRender->getUri());
        $this->getView()->display('mock/list.html');
    }

    public function CreateAction() {
        $this->getView()->display('mock/create.html');
    }

    public function UpdateAction() {
        $id = intval( $this->getRequest()->getQuery('id') );

        $Mock = new \Service\Mock();
        $Mock->query($id);
        $this->getView()->assign('response_header', $Mock->getResponseHeader() );
        $this->getView()->assign('response_body',   $Mock->getResponseBody() );
        $this->getView()->assign('request_query',   $Mock->getRequestQuery() );
        $this->getView()->assign('request_post',    $Mock->getRequestPost() );
        $this->getView()->assign('id',              $Mock->getId());
        $this->getView()->assign('uri_id',          $Mock->getUriId());
        $this->getView()->assign('uri',             $Mock->getUri());
        $this->getView()->assign('timeout',         $Mock->getTimeout());
        $this->getView()->display('mock/update.html');
    }

    public function CreateMockResponseAction() {
        $uri                   = $this->getRequest()->getPost('uri');
        $timeout               = $this->getRequest()->getPost('timeout');
        $response_body         = $this->getRequest()->getPost('response_body');
        $request_query_key     = $this->getRequest()->getPost('request_query_key');
        $request_query_value   = $this->getRequest()->getPost('request_query_value');
        $request_post_key      = $this->getRequest()->getPost('request_post_key');
        $request_post_value    = $this->getRequest()->getPost('request_post_value');
        $response_header_key   = $this->getRequest()->getPost('response_header_key');
        $response_header_value = $this->getRequest()->getPost('response_header_value');

        $Mock = new \Service\Mock();
        $Mock->setUri($uri);
        $Mock->setRequestQueryByKeyAndValue($request_query_key, $request_query_value);
        $Mock->setRequestPostByKeyAndValue($request_post_key, $request_post_value);
        $Mock->setResponseHeaderByKeyAndValue($response_header_key, $response_header_value);
        $Mock->setResponseBody($response_body);
        $Mock->setTimeout($timeout);
        $Mock->create();

        $ServiceGenerator = new \Service\Mock\Generator($Mock->getUriId());
        $ServiceGenerator->generate();
    }

    public function SaveMockResponseAction() {
        $id                    = $this->getRequest()->getPost('id');
        $uri                   = $this->getRequest()->getPost('uri');
        $timeout               = $this->getRequest()->getPost('timeout');
        $response_body         = $this->getRequest()->getPost('response_body');
        $request_query_key     = $this->getRequest()->getPost('request_query_key');
        $request_query_value   = $this->getRequest()->getPost('request_query_value');
        $request_post_key      = $this->getRequest()->getPost('request_post_key');
        $request_post_value    = $this->getRequest()->getPost('request_post_value');
        $response_header_key   = $this->getRequest()->getPost('response_header_key');
        $response_header_value = $this->getRequest()->getPost('response_header_value');

        $Mock = new \Service\Mock();
        $Mock->query($id);
        $Mock->setUri($uri);
        $Mock->setRequestQueryByKeyAndValue($request_query_key, $request_query_value);
        $Mock->setRequestPostByKeyAndValue($request_post_key, $request_post_value);
        $Mock->setResponseHeaderByKeyAndValue($response_header_key, $response_header_value);
        $Mock->setResponseBody($response_body);
        $Mock->setTimeout($timeout);
        $Mock->save();

        $ServiceGenerator = new \Service\Mock\Generator($Mock->getUriId());
        $ServiceGenerator->generate();
    }
}