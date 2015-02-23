<?php
/**
 * Core_Entity
 *
 * @category Library
 * @package  Core
 * @author   Lancer He <lancer.he@gmail.com>
 * @version  1.0 
 */
class Core_Entity {

    /**
     * 数据单行信息
     * @var array
     */
    protected $_data = [];


    /**
     * 初始化，将数据转化为对象内的一个元素数组信息，类外通过魔术方法调用
     */
    public function __construct($row = []) {
        $this->_data = $row;
    }


    /**
     * 魔术方法直接获取实体内的数据字段值
     * @param  string  $key   字段名
     * @return mixed
     */
    public function __get($key) {
        return isset($this->_data[$key]) ? $this->_data[$key] : false;
    }


    /**
     * 魔术方法直接获取实体内的数据字段值
     * @param  string  $key   字段名
     * @param  string  $value 字段值
     * @return mixed
     */
    public function __set($key, $value) {
        return $this->_data[$key] = $value;
    }


    /**
     * 返回数组信息
     * @return array
     */
    public function getData() {
        return $this->_data;
    }
}
