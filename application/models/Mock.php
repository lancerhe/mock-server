<?php
/**
 * Mock Model
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-16
 */
Class Model_Mock extends \Core\Model\Medoo {

    public function fetchListByUriId($uri_id) {
        $rows = $this->medoo()->select('mock', '*', ['uri_id' => $uri_id]);
        $rows = \Util_Array::column($rows, null, 'id');
        return $rows;
    }

    public function fetchListByUriIdAndUser($uri_id, $user = '') {
        $rows = $this->medoo()->select('mock', '*', ['AND' => ['uri_id' => $uri_id, 'user' => $user] ]);
        $rows = \Util_Array::column($rows, null, 'id');
        return $rows;
    }

    public function fetchRowById($id) {
        $rows = $this->medoo()->select('mock', '*', ['id' => $id]);
        return isset($rows[0]) ? $rows[0] : [];
    }

    public function insertRow($row) {
        if ( ! $mock_id = $this->medoo()->insert('mock', $row) ) {
            throw new \Core\Exception\DatabaseWriteException();
        }
        return $mock_id;
    }

    public function updateRowById($row, $id) {
        if ( false === $affect = $this->medoo()->update('mock', $row, ["id" => $id]) ) {
            throw new \Core\Exception\DatabaseWriteException();
        }
        return $affect;
    }
}