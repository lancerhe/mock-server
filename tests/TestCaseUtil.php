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
        $this->__truncateTestDatabase();
    }

    public function import($file) {
        $sqls = $this->__parseSetupFile2SQL($file);
        foreach ($sqls as $sql) 
            $this->__Medoo->query($sql);
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

    private function __parseSetupFile2SQL($file) {
        $setup_folder = ROOT_PATH . '/tests/setup/';
        $sql_file     = $setup_folder . $file;
        if ( ! file_exists($sql_file) ) {
            trigger_error("Setup sql file not found. file: $sql_file");
        }

        $setup = file_get_contents( $sql_file );
        $setup = str_replace(array("\n", "\r", PHP_EOL), '', $setup);
        $lines = explode(";", $setup);

        $sqls = array();
        foreach ($lines as $sql) {
            
            if ( ! trim($sql) ) continue;
            if ( '#' === substr($sql, 0, 1) ) continue;

            $sqls[] = $sql; 
        }
        return $sqls;
    }
}