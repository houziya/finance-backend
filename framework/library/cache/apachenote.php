<?php

/**
 * Apachenote缓存驱动
 */
class cache_apachenote extends cache {

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = array()) {
		if (!empty($options)) {
			$this->options = $options;
		}
		if (empty($options)) {
			$options = array(
				'host' => '127.0.0.1',
				'port' => 1042,
				'timeout' => 10,
			);
		}
		$this->options = $options;
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('sys_cache_prefix');
		$this->handler = null;
		$this->open();
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name) {
		$this->open();
		$name = $this->options['prefix'] . $name;
		$s = 'F' . pack('N', strlen($name)) . $name;
		fwrite($this->handler, $s);

		for ($data = ''; !feof($this->handler);) {
			$data .= fread($this->handler, 4096);
		}
		$this->Q(1);
		$this->close();
		return $data === '' ? '' : unserialize($data);
	}

	/**
	 * 写入缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @param mixed $value  存储数据
	 * @return boolen
	 */
	public function set($name, $value) {
		$this->W(1);
		$this->open();
		$value = serialize($value);
		$name = $this->options['prefix'] . $name;
		$s = 'S' . pack('NN', strlen($name), strlen($value)) . $name . $value;
		fwrite($this->handler, $s);
		$ret = fgets($this->handler);
		$this->close();
		if ($ret === "OK\n") {
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
		$this->open();
		$name = $this->options['prefix'] . $name;
		$s = 'D' . pack('N', strlen($name)) . $name;
		fwrite($this->handler, $s);
		$ret = fgets($this->handler);
		$this->close();
		return $ret === "OK\n";
	}

	/**
	 * 关闭缓存
	 * @access private
	 */
	private function close() {
		fclose($this->handler);
		$this->handler = false;
	}

	/**
	 * 打开缓存
	 * @access private
	 */
	private function open() {
		if (!is_resource($this->handler)) {
			$this->handler = fsockopen($this->options['host'], $this->options['port'], $_, $_, $this->options['timeout']);
		}
	}

}

?>