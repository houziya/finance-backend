<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_pluginSoftware extends model {

	protected $tableName = 'financial_plugin_software';

	/**
	 * 获取单条财务软件信息(带缓存)
	 * @param string $args  pid|store_num组合 下划线连接
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getInfo($id, $field = null, $delcache = false) {
		if (empty($id) || strpos($id, '_') === false) return $field ? '' : array();
		$cachename = 'model_finance_pluginsoftware_' . $id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$arr = explode('_',$id);
			$info = M('financialPluginSoftware')->where(array('pid' => $arr[0], 'store_num' => $arr[1]))->find();
			unset($info['posttime']);
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
	/*
	 * 安装软件
	 * @author liufei
	 * @param int $pid 项目ID
	 * @param int $store_num 店铺编号
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function setInstall($pid, $store_num, $data = array()){
		if(empty($pid) || empty($store_num) || empty($data)) return false;
		$rs = $this->where(array('pid' => $pid, 'store_num' => $store_num))->save($data);
		if($rs){
			$this->getInfo("{$pid}_{$store_num}",'',1);
		}
		return $rs;
	}

	/**
	 * 批量获取财务软件信息
	 * @param int $id
	 * @param int $limit
	 * @return mixed
	 */
	public function getSoftwareList($id = 0, $limit = 100) {
		$info = $this->where("id>$id")->limit($limit)->find();
		return $info;
	}

	/**
	 * 更新最后数据获取的时间
	 * @param int $pid   项目ID
	 * @param int $store_num   店铺编号
	 * @param int $time   当前时间
	 * @param int $ordertime   客户端查询日期
	 * @return bool
	 */
	public function updateSoftwareLastTime($pid, $store_num, $time,$ordertime) {
		return $this->where("pid={$pid} AND store_num = '{$store_num}'")->save(array('posttime' => $time, 'ordertime' => $ordertime));
	}

}
