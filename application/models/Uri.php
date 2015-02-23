<?php
/**
 * Uri Model
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-16
 */
Class Model_Uri extends \Core\Model\Medoo {

    public function fetchList() {
        $rows = $this->medoo()->select('uri', ['id', 'uri']);
        $rows = \Util_Array::column($rows, null, 'id');
        return $rows;
    }

    public function fetchRowById($id) {
        $rows = $this->medoo()->select('uri', ['id', 'uri'], ['id' => $id]);
        return isset($rows[0]) ? $rows[0] : [];
    }

    public function create($uri) {
        if ( ! $uri_id = $this->medoo()->insert('uri', ["uri" => $uri]) ) {
            throw new \Core\Exception\DatabaseWriteException();
        }
        return $uri_id;
    }

    public function createIfNotExist($uri) {
        $row    = $this->medoo()->select('uri', 'id', ["uri" => $uri]);
        $uri_id = isset($row[0]) ? $row[0] : false;
        if ( $uri_id ) 
            return $uri_id;

        return $this->create($uri);
    }
}