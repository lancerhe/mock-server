<?php
/**
 * User Plugin
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-03-04
 */
Class Plugin_User extends \Yaf\Plugin_Abstract {

    public function dispatchLoopStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {
        $user = Util_Phpcas::isAuthenticated();
    }
}