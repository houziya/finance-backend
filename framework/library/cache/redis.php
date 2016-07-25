<?php

/**
 * Redis缓存驱动 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 */
class cache_redis extends cache {

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = array()) {
		if (!extension_loaded('redis')) {
			throw_exception(L('_NOT_SUPPERT_') . ':redis');
		}
		if (empty($options)) {
			$options = array(
				'host' => C('sys_redis_host') ? C('sys_redis_host') : '127.0.0.1',
				'port' => C('sys_redis_port') ? C('sys_redis_port') : 6379,
				'timeout' => C('sys_redis_timeout') ? C('sys_redis_timeout') : false,
				'persistent' => false,
			);
		}
		$this->options = $options;
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('sys_cache_time');
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('sys_cache_prefix');
		$func = $options['persistent'] ? 'pconnect' : 'connect';
		$this->handler = new Redis();
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
		$value = $this->handler->get($this->options['prefix'] . $name);
		$jsonData = json_decode($value, true);
		return ($jsonData === NULL) ? $value : $jsonData; //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
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
		//对数组/对象数据进行缓存处理，保证数据完整性
		$value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
		if (is_int($expire)) {
			$result = $this->handler->setex($name, $expire, $value);
		} else {
			$result = $this->handler->set($name, $value);
		}
		return $result;
	}

	/**
	 * 删除缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return boolen
	 */
	public function rm($name) {
		return $this->handler->delete($this->options['prefix'] . $name);
	}
	
	/**
     * 返回满足给定pattern的所有key
     * @param string $name
     * @return array
     */
    public function keys($name) {
        return $this->handler->keys($this->options['prefix'] . $name);
    }

	/**
	 * 左入对
	 * @param string $name 队列名
	 * @param string $value 队列数据
	 * @return mixed
	 */
	public function lpush($name, $value) {
		$value = json_encode($value);
		return $this->handler->lpush($this->options['prefix'] . $name, $value);
	}
	
	/**
	 * 右入对
	 * @param string $name 队列名
	 * @param string $value 队列数据
	 * @return mixed
	 */
	public function rpush($name, $value) {
		$value = json_encode($value);
		return $this->handler->rpush($this->options['prefix'] . $name, $value);
	}
	
	/**
	 * 左出对
	 * @param string $name 队列名
	 * @return mixed
	 */
	public function lpop($name) {
		$data = $this->handler->lpop($this->options['prefix'] . $name);
		$data = json_decode($data, true);
		return $data;
	}
	
	/**
	 * 右出对
	 * @param string $name 队列名
	 * @return mixed
	 */
	public function rpop($name) {
		$data = $this->handler->rpop($this->options['prefix'] . $name);
		$data = json_decode($data, true);
		return $data;
	}
	
	/**
	 * 得到一个key的生存时间
	 * @param string $name 键名
	 * @return mixed
	 */
	public function ttl($name) {
		return $this->handler->ttl($this->options['prefix'] . $name);
	}
	
	/**
	 * 判断key是否存在
	 * @param string $name 键名
	 * @return mixed
	 */
	public function exists($name) {
		return $this->handler->exists($this->options['prefix'] . $name);
	}
	
	/**
	 * 自增加法
	 * @param string $name 键名
	 * @param string $value 递增值
	 * @return mixed
	 */
	public function incr($name, $value = 1) {
		return $this->handler->incrBy($this->options['prefix'] . $name, $value);
	}
	
	/**
	 * 开启事务
	 * @return mixed
	 */
	public function multi() {
		return $this->handler->multi();
	}
	
	/**
	 * 执行事务
	 * @return mixed
	 */
	public function exec() {
		return $this->handler->exec();
	}
	
	/**
	 * 设置key的过期时间
	 * @param string $name 键名
	 * @param string $time 过期时间
	 * @return mixed
	 */
	public function setTimeout($name, $time = 0) {
		return $this->handler->setTimeout($this->options['prefix'] . $name, $time);
	}
	
	/**
	 * 清除缓存
	 * @access public
	 * @return boolen
	 */
	public function clear() {
		return false;
		//return $this->handler->flushDB();
	}
        
     /**
	 * 取出所有队列的值
	 * @access public
	 * @return boolen
	 */
	public function lrange($name,$start=0,$stop=-1) {
		return $this->handler->lrange($this->options['prefix'].$name,$start,$stop);
	}
	
	/**
	 * 取出list类型的总数
	 * @access public
	 * @return boolen
	 */
	public function llen($name) {
		return $this->handler->llen($this->options['prefix'].$name);
	}

}