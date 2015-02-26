<?php

namespace Service;

class Uri extends \Core_Entity {

    protected $_id = null;

    protected $_uri = '';

    public function getId() {
        return $this->_id;
    }

    public function getUri() {
        return $this->_uri;
    }

    public function query($id) {
        $uri = (new \Model_Uri())->fetchRowById($id);
        if ( empty($uri) ) {
            throw new \Core\Exception\NotFoundRecordException();
        }
        $this->init($uri);
    }

    public function init($uri) {
        $this->_id  = $uri['id'];
        $this->_uri = $uri['uri'];
    }
}