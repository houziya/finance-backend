<?php

/**
 * Apc缓存类
 */
class cache_apc extends cache {

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = array()) {
		if (!function_exists('apc_cache_info')) {
			throw_exception(L('_NOT_SUPPERT_') . ':Apc');
		}
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('sys_cache_prefix');
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('sys_cache_time');
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name) {
		$this->Q(1);
		return apc_fetch($this->options['prefix'] . $name);
	}

	/**
	 * 写入缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @param mixed $value  存储数据
	 * @param integer $expire  有效时间（秒）
	 * @return boolen
	 */
	public function set($name, $value, $expire = null) {
		$this->W(1);
		if (is_null($expire)) {
			$expire = $this->options['expire'];
		}
		$name = $this->options['prefix'] . $name;
		$result = apc_store($name, $value, $expire);
		return $result;
	}

	/**
	 * 删除缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return boolen
	 */
	public function rm($name) {
		return apc_delete($this->options['prefix'] . $name);
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return boolen
	 */
	public function clear() {
		return apc_clear_cache();
	}

}

?>