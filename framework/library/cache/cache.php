<?php
/**
 * 缓存管理入口类
 */

class cache {
	
    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    protected $handler    ;

    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    protected $options = array();
	
	/**
	 * 连接缓存
	 * @access public
	 * @param string $type 缓存类型
	 * @param array $options  配置数组
	 * @return object
	 */
	public function connect($type = '', $options = array()) {
		if (empty($type)) $type = C('sys_cache_type');
		$cachePath = dirname(__FILE__) . '/';
		$cacheClass = 'cache_' . strtolower(trim($type));
		requireCache($cachePath . trim($type) . '.php');
		if (class_exists($cacheClass)) {
			$cache = new $cacheClass($options);
		} else {
			throw_exception(L('_CACHE_TYPE_INVALID_') . ':' . $type);
		}
		return $cache;
	}

	public function __get($name) {
		return $this->get($name);
	}

	public function __set($name, $value) {
		return $this->set($name, $value);
	}

	public function __unset($name) {
		$this->rm($name);
	}

	public function setOptions($name, $value) {
		$this->options[$name] = $value;
	}

	public function getOptions($name) {
		return $this->options[$name];
	}
    
    //取得缓存类实例
	static function getInstance() {
		$param = func_get_args();
		return getInstanceOf(__CLASS__, 'connect', $param);
	}
	
	public function __call($method,$args){
        //调用缓存类型自己的方法
        if(method_exists($this->handler, $method)){
           return call_user_func_array(array($this->handler,$method), $args);
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }

    // 读取缓存次数
    public function Q($times='') {
        static $_times = 0;
        if(empty($times))
            return $_times;
        else
            $_times++;
    }

    // 写入缓存次数
    public  function W($times='') {
        static $_times = 0;
        if(empty($times))
            return $_times;
        else
            $_times++;
    }
}
?>