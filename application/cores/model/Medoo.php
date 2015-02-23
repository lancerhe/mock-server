<?php
/**
 * MySQL Model
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2014-11-01
 */

namespace Core\Model;

use Core\Exception\DatabaseWriteException;

class Medoo extends \Core\Model {

    public static $Medoo = null;

    public function medoo() {
        if ( ! is_null( self::$Medoo ) ) {
            return self::$Medoo;
        }

        $config = new \Yaf\Config\Ini( APPLICATION_CONFIG_PATH . '/mysql.ini', \Yaf\ENVIRON);

        self::$Medoo = new \Medoo([
            'database_type' => $config->database_type,
            'database_name' => $config->database_name,
            'server'        => $config->server,
            'username'      => $config->username,
            'password'      => $config->password,
            'port'          => $config->port,
            'charset'       => $config->charset,
        ]);
        return self::$Medoo;
    }
}