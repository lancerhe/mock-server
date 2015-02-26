<?php
/**
 * Mock Server Manager Ajax Controller
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-26
 */
class Controller_MockHandler extends \Core\Controller\Ajax {

    public function CreateAction() {
        $uri                   = $this->getRequest()->getPost('uri');
        $timeout               = $this->getRequest()->getPost('timeout');
        $response_body         = $this->getRequest()->getPost('response_body');
        $response_status_code  = $this->getRequest()->getPost('response_status_code');
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
        $Mock->setResponseStatusCode($response_status_code);
        $Mock->setTimeout($timeout);
        $Mock->create();

        $ServiceGenerator = new \Service\Mock\Generator($Mock->getUriId());
        $ServiceGenerator->generate();

        $this->getView()->displayAjax("Create Successfully.", ["uri_id" => $Mock->getUriId()]);
    }

    public function SaveAction() {
        $id                    = $this->getRequest()->getPost('id');
        $uri                   = $this->getRequest()->getPost('uri');
        $timeout               = $this->getRequest()->getPost('timeout');
        $response_body         = $this->getRequest()->getPost('response_body');
        $response_status_code  = $this->getRequest()->getPost('response_status_code');
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
        $Mock->setResponseStatusCode($response_status_code);
        $Mock->setTimeout($timeout);
        $Mock->save();

        $ServiceGenerator = new \Service\Mock\Generator($Mock->getUriId());
        $ServiceGenerator->generate();

        $this->getView()->displayAjax("Save Successfully.");
    }
}