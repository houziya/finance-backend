<?php

/**
 * Xcache缓存类
 */
class cache_xcache extends cache {

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = array()) {
		if (!function_exists('xcache_info')) {
			throw_exception(L('_NOT_SUPPERT_') . ':Xcache');
		}
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('sys_cache_time');
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('sys_cache_prefix');
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name) {
		$this->Q(1);
		$name = $this->options['prefix'] . $name;
		if (xcache_isset($name)) {
			return xcache_get($name);
		}
		return false;
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
		if (xcache_set($name, $value, $expire)) {
			return true;
		}
		return false;
	}

	/**
	 * 删除缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return boolen
	 */
	public function rm($name) {
		return xcache_unset($this->options['prefix'] . $name);
	}

}

?>