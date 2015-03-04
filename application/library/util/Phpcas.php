<?php
/**
 * Util_Phpcas
 *
 * @category Library
 * @package  Util
 * @author   Lancer He <lancer.he@gmail.com>
 * @version  1.0 
 */
class Util_Phpcas {

    protected static $_init = false;

    protected static $_user = false;

    private function __construct() {}

    public static function init() {
        if ( self::$_init ) {
            return true;
        }

        $config = new \Yaf\Config\Ini( APPLICATION_CONFIG_PATH . '/phpcas.ini', \Yaf\ENVIRON);
        phpCAS::setDebug('');
        phpCAS::client($config->cas_version , $config->cas_host, intval($config->cas_port), $config->cas_context);
        phpCAS::setNoCasServerValidation();
        phpCAS::handleLogoutRequests(false);
        self::$_init = true;
        return true;
    }

    public static function isAuthenticated() {
        if ( self::$_user ) {
            return self::$_user;
        }
        self::init();
        if ( true === $result = phpCAS::isAuthenticated() ) {
            $result = phpCAS::getUser();
        }
        session_write_close();
        return $result;
    }

    public static function login() {
        if ( self::$_user ) {
            return self::$_user;
        }
        self::init();
        phpCAS::forceAuthentication();
        session_write_close();
        return phpCAS::getUser(); 
    }

    public static function logout() {
        self::init();
        phpCAS::logout();
        return true;
    }

    public static function setUser($user) {
        self::$_user = $user;
    }
}