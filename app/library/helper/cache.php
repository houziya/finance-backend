<?php
// +----------------------------------------------------------------------
// | 缓存类
// +----------------------------------------------------------------------
class helper_cache
{
    // 将要清理缓存的名称
    public static $clearType = null;

    /**
     * 强制刷新
     * @return boolean
     */
    public static function forceRefresh()
    {
        self::$clearType = date('Ymd');

        if (C('sys_debug')) { // 如果是调试模式
            return true;
        }
        $cacheName = isset($_REQUEST['_force_refresh']) ? trim($_REQUEST['_force_refresh']) : false;
        return $cacheName !== false && self::$clearType = $cacheName;
    }

    /**
     * 根据参数生成缓存的key
     * @param func_get_args
     * @return string
     */
    public static function makeKey()
    {
        $key = '';
        foreach (func_get_args() as $val) {
            if (is_array($val)) {
                ksort($val);
                $val = serialize($val);
            }
            $key .= $val;
        }
        return md5($key);
    }

    /**
     * 数据获取回调
     * @param string/array $callback 回调方法
     * @param array $params 回调方法使用的参数
     * @return mixed
     */
    public static function callback($callback, array $params)
    {
        return call_user_func_array($callback, $params);
    }

    /**
     * 获取缓存数据，如果缓存中不存在，那么则回调来获取给定方法的返回值并且缓存，之后将数据返回
     * @param string $key 缓存的key
     * @param string $callback 生成缓存的回调方法
     * @param string $lifetime 缓存的生命时长(秒)
     * @param string $params 生成缓存的回调方法所需要的参数
     * @return mixed
     */
    public static function getSmartCache($key, $callback, $lifetime = 60, $params = array())
    {
        // 如果强制刷新缓存或者缓存中没有数据
        if (self::forceRefresh() || ($data = S($key)) === false) {
            // 强制刷新缓存
            if (self::forceRefresh()) {
                // 刷新全部
                if (self::$clearType == date('Ymd')) {
                    $data = self::callback($callback, $params);
                }
                // 刷新局部
                elseif (strpos($key, self::$clearType) !== false) {
                    $data = self::callback($callback, $params);
                }
                // 不存在key指定的cache
                elseif (($data = S($key)) === false) {
                    $data = self::callback($callback, $params);
                }
            }
            // 缓存中没有数据
            else {
                $data = self::callback($callback, $params);
            }
            S($key, $data, $lifetime);
        }

        return $data;
    }
}