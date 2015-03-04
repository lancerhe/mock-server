<?php
/**
 * Account Entity
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-03-04
 */
namespace Service;

class Account {

    public static function getUser() {
        if ( ! APPLICATION_PHPCAS_OPEN )
            return '';

        return \Util_Phpcas::isAuthenticated();
    }
}