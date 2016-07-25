<?php
// +----------------------------------------------------------------------
// | 模型基类
// +----------------------------------------------------------------------

class model_abstruct extends model {

    /**
     * 使用二维数组子节点中的某个字段来当作key（注意：这个值多数情况是唯一的，否则会被覆盖）
     * @param array $array 需要设置的数组
     * @param string $key key
     * @access public
     */
    static public function _setKeyArray(array &$array, $key)
    {
        $arr = array();
        foreach ($array as &$val) {
            $arr[$val[$key]] = $val;
        }
        $array = $arr;
        return $array;
    }

    /**
     * 获取二维数组子节点中的某个字段
     * @param array $array 数组
     * @param string $field 获取的字段
     * @param boolean $unique 是否去重
     * @access public
     */
    static public function _getFieldValue(array $array, $field, $unique = true)
    {
        $data = array_map(create_function('$v', 'return $v["' . $field . '"];'), $array);
        if ($unique) {
            $data = array_unique($data);
        }
        return array_values($data);
    }
	
}
