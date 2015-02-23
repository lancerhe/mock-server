<?php
/**
 * Util_Array
 *
 * @category Library
 * @package  Util
 * @author   Lancer He <lancer.he@gmail.com>
 * @version  1.0 
 */
class Util_Array{

    /**
     * See PHP5.5 array_column
     *
     * Returns the values from a single column of the input array, identified by
     * the $column_key.
     *
     * Optionally, you may provide an $index_key to index the values in the returned
     * array by the values from the $index_key column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $column_key The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $index_key (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    public static function column($input, $column_key, $index_key=null){
        if ( ! function_exists('array_column')) {
            // Using func_get_args() in order to check for proper number of
            // parameters and trigger errors exactly as the built-in array_column()
            // does in PHP 5.5.
            $argc = func_num_args();
            $params = func_get_args();

            if (!is_array($params[0])) 
                trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);

            if (!is_int($params[1])
                && !is_float($params[1])
                && !is_string($params[1])
                && $params[1] !== null
                && !(is_object($params[1]) && method_exists($params[1], '__toString'))
            ) 
                trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);

            if (isset($params[2])
                && !is_int($params[2])
                && !is_float($params[2])
                && !is_string($params[2])
                && !(is_object($params[2]) && method_exists($params[2], '__toString'))
            ) 
                trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);

            $params_input = $params[0];
            $params_column_key = ($params[1] !== null) ? (string) $params[1] : null;

            $params_index_key = null;
            if (isset($params[2])) {
                if (is_float($params[2]) || is_int($params[2])) {
                    $params_index_key = (int) $params[2];
                } else {
                    $params_index_key = (string) $params[2];
                }
            }

            $result_array = array();

            foreach ($params_input as $row) {

                $key = $value = null;
                $keySet = $value_set = false;

                if ($params_index_key !== null && array_key_exists($params_index_key, $row)) {
                    $keySet = true;
                    $key = (string) $row[$params_index_key];
                }

                if ($params_column_key === null) {
                    $value_set = true;
                    $value = $row;
                } elseif (is_array($row) && array_key_exists($params_column_key, $row)) {
                    $value_set = true;
                    $value = $row[$params_column_key];
                }

                if ($value_set) {
                    if ($keySet) {
                        $result_array[$key] = $value;
                    } else {
                        $result_array[] = $value;
                    }
                }

            }

            return $result_array;
        } else {
            return array_column($input, $column_key, $index_key);
        }
    }

    /**
     * sort 二维数组排序
     * 
     * @access public
     * @static
     * @param $array  二维数组
     * @param $key    排序键名
     * @param $type   升序降序
     * @example $array = array(
     *              array('id' => 2, 'name'=>'lancer', 'age'=>18),
     *              array('id' => 3, 'name'=>'chart', 'age'=>17),
     *          );
     *          $result = Util_Array::sort($array, 'id', 'desc');
     */
    public static function mutisort($multi_array, $sort_field, $sort_type = SORT_ASC){
        if ( ! is_array($multi_array) ) 
            trigger_error('mutisort(): The first parameter should be array', E_USER_WARNING);

        foreach ($multi_array as $row){
            if ( ! is_array($row) ) return $multi_array;
            $arr_field[] = $row[$sort_field];
        }
        array_multisort($arr_field,$sort_type,$multi_array);
        return $multi_array;
    }
}