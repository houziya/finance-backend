<?php

/**
 * Memcache缓存类
 */
class cache_memcache extends cache {

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	function __construct($options = array()) {
		if (!extension_loaded('memcache')) {
			throw_exception(L('_NOT_SUPPERT_') . ':memcache');
		}

		$options = array_merge(array(
			'host' => C('sys_memcache_host') ? C('sys_memcache_host') : '127.0.0.1',
			'port' => C('sys_memcache_port') ? C('sys_memcache_port') : 11211,
			'timeout' => C('sys_memcache_timeout') ? C('sys_memcache_timeout') : false,
			'persistent' => false,), $options);
		$this->options = $options;
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('sys_cache_time');
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('sys_cache_prefix');
		$func = $options['persistent'] ? 'pconnect' : 'connect';
		$this->handler = new Memcache();
		$options['timeout'] === false ?
						$this->handler->$func($options['host'], $options['port']) :
						$this->handler->$func($options['host'], $options['port'], $options['timeout']);
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name) {
		$this->Q(1);
		return $this->handler->get($this->options['prefix'] . $name);
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
		if ($this->handler->set($name, $value, 0, $expire)) {
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
	public function rm($name, $ttl = false) {
		$name = $this->options['prefix'] . $name;
		return $ttl === false ?
				$this->handler->delete($name) :
				$this->handler->delete($name, $ttl);
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return boolen
	 */
	public function clear() {
		return $this->handler->flush();
	}

}

?>