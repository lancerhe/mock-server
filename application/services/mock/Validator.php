<?php
/**
 * Mock Response validator
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-28
 */
namespace Service\Mock;

use Service\Mock;
use Core\Exception\RequestValidateException;

class Validator {

    protected $_Mock = null;

    public function __construct(Mock $Mock) {
        $this->_Mock = $Mock;
    }

    public function validate() {
        if ( "/" == $uri = $this->_Mock->getUri() )
            throw new RequestValidateException("Requset uri empty!");
    }
}