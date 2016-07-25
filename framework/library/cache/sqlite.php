<?php

/**
 * Sqlite缓存类
 */
class cache_sqlite extends cache {

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = array()) {
		if (!extension_loaded('sqlite')) {
			throw_exception(L('_NOT_SUPPERT_') . ':sqlite');
		}
		if (empty($options)) {
			$options = array(
				'db' => ':memory:',
				'table' => 'sharedmemory',
			);
		}
		$this->options = $options;
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('sys_cache_prefix');
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('sys_cache_time');

		$func = $this->options['persistent'] ? 'sqlite_popen' : 'sqlite_open';
		$this->handler = $func($this->options['db']);
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name) {
		$this->Q(1);
		$name = $this->options['prefix'] . sqlite_escape_string($name);
		$sql = 'SELECT value FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\' AND (expire=0 OR expire >' . time() . ') LIMIT 1';
		$result = sqlite_query($this->handler, $sql);
		if (sqlite_num_rows($result)) {
			$content = sqlite_fetch_single($result);
			if (C('sys_cache_compress') && function_exists('gzcompress')) {
				//启用数据压缩
				$content = gzuncompress($content);
			}
			return unserialize($content);
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
		$name = $this->options['prefix'] . sqlite_escape_string($name);
		$value = sqlite_escape_string(serialize($value));
		if (is_null($expire)) {
			$expire = $this->options['expire'];
		}
		$expire = ($expire == 0) ? 0 : (time() + $expire); //缓存有效期为0表示永久缓存
		if (C('sys_cache_compress') && function_exists('gzcompress')) {
			//数据压缩
			$value = gzcompress($value, 3);
		}
		$sql = 'REPLACE INTO ' . $this->options['table'] . ' (var, value,expire) VALUES (\'' . $name . '\', \'' . $value . '\', \'' . $expire . '\')';
		if (sqlite_query($this->handler, $sql)) {
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
		$name = $this->options['prefix'] . sqlite_escape_string($name);
		$sql = 'DELETE FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\'';
		sqlite_query($this->handler, $sql);
		return true;
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return boolen
	 */
	public function clear() {
		$sql = 'DELETE FROM ' . $this->options['table'];
		sqlite_query($this->handler, $sql);
		return;
	}

}

?>