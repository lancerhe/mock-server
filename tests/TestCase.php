<?php
namespace YafUnit;

define('APPLICATION_NOT_RUN', true);

require("TestCaseUtil.php");

class TestCase extends \PHPUnit_Framework_TestCase {

    protected static $_app;

    protected static $_view;

    public function setUp() {
        if ( ! \Yaf\Registry::get('ApplicationInit') ) {
            $this->__setUpYafApplication();
        } else {
            $this->__setUpApplicationInit();
        }
    }

    public function medoo() {
        return \YafUnit\TestCaseUtil\Medoo::getInstance();
    }

    private function __setUpPHPIniVariables() {

    }

    private function __setUpApplicationInit() {
        self::$_app  = \Yaf\Dispatcher::getInstance()->getApplication();
        self::$_view = View::getInstance();
    }

    private function __setUpYafApplication() {
        $this->__setUpPHPIniVariables();
        // Import application and bootstrap.
        \Yaf\Loader::import( dirname(__DIR__) . '/public/index.php' );

        // Import test case base file.
        \Yaf\Loader::import( __DIR__ . '/YafUnit.php' );

        $this->__setUpApplicationInit();

        \Yaf\Dispatcher::getInstance()->setView( self::$_view );
        \Yaf\Registry::set( 'ApplicationInit', true );
    }
}

namespace YafUnit\TestCase;

class Controller extends \YafUnit\TestCase {

    public function createRequest($uri) {
        \Util_Phpcas::setUser('utest');
        $this->_request = new \YafUnit\Request\Http($uri);
    }

    public function setQuery($name, $value) {
        $this->_request->setQuery($name, $value);
    }

    public function setPost($name, $value = '') {
        if ( ! is_array($name) ) {
            $this->_request->setPost($name, $value);
        } else {
            foreach ($name as $key => $value) {
                $this->_request->setPost($key, $value);
            }
        }
    }

    public function dispatch() {
        self::$_app->getDispatcher()->dispatch( $this->_request );
    }

    public function getView() {
        return self::$_view;
    }
}