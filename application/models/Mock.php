<?php
/**
 * Mock Model
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-16
 */
Class Model_Mock extends \Core\Model\Medoo {

    public function fetchListByUriId($uri_id) {
        $rows = $this->medoo()->select('mock', ['id', 'uri_id', 'request_query', 'request_post', 'response_header', 'response_body', 'timeout'], ['uri_id' => $uri_id]);
        $rows = \Util_Array::column($rows, null, 'id');
        return $rows;
    }

    public function fetchRowById($id) {
        $rows = $this->medoo()->select('mock', ['id', 'uri_id', 'request_query', 'request_post', 'response_header', 'response_body', 'timeout'], ['id' => $id]);
        return isset($rows[0]) ? $rows[0] : [];
    }
}