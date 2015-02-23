<?php
namespace YafUnit\TestCaseUtil;

class Medoo {

    private $__sql_init = [];

    private $__sql_truncate = [];

    private $__Medoo = null;

    private function __clone() {}

    private function __construct() {
        $this->__buildCreateAndTruncateSQL();
        $this->__connectTestDatabase();
        $this->__createTestDatabase();
    }

    public static function getInstance() {
        if ( $instance = \Yaf\Registry::get('ApplicationInit_Medoo') ) {
            return $instance;
        }

        $instance = new self();
        \Yaf\Registry::set( 'ApplicationInit_Medoo', $instance);
        return $instance;
    }

    public function setUp() {
        self::getInstance()->__setUpTestCaseInit();
    }

    public function __call($func, $parameters){
        self::getInstance();
        return call_user_func_array(array($this->__Medoo, $func), $parameters);
    }

    private function __buildCreateAndTruncateSQL() {
        $Model = new \Core\Model\Medoo();
        $tables = $Model->medoo()->query("SHOW TABLES")->fetchAll();
        foreach ($tables as $table) {
            $table = $table[0];
            $createtable = $Model->medoo()->query("SHOW CREATE TABLE " . $table)->fetch();
            $this->__sql_init[$table]     = "DROP TABLE IF EXISTS \"$table\";" . PHP_EOL . $createtable[1];
            $this->__sql_truncate[$table] = "TRUNCATE TABLE \"$table\";";
        }
    }

    private function __createTestDatabase() {
        foreach ($this->__sql_init as $sql) $this->__Medoo->query($sql);
    }

    private function __truncateTestDatabase() {
        foreach ($this->__sql_truncate as $sql) $this->__Medoo->query($sql);
    }

    private function __connectTestDatabase() {
        $config = new \Yaf\Config\Ini( APPLICATION_CONFIG_PATH . '/mysql.ini', \Yaf\ENVIRON);

        $this->__Medoo = new \Medoo([
            'database_type' => $config->database_type,
            'database_name' => $config->database_name . "_test",
            'server'        => $config->server,
            'username'      => $config->username,
            'password'      => $config->password,
            'port'          => $config->port,
            'charset'       => $config->charset,
        ]);
        \Core\Model\Medoo::$Medoo = $this->__Medoo;
    }

    private function __setUpTestCaseInit() {
        $this->__truncateTestDatabase();
    }
}