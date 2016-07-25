<?php
/**
 * Class model_finance_pluginArchive
 *
 */

class model_finance_pluginTask extends model
{
    protected $tableName = 'financial_plugin_task';
	
	/*
	 * 获取等待执行的任务数据
	 * @author liufei
	 * @param string $var info
	 * @return return
	 */
	public function getWaitTask($pid, $store_num){
		if(empty($pid) || empty($store_num)) return false;
		$count = $this->where(array('pid' => $pid, 'store_num' => $store_num, 'status' => 0))->count();
		$rs = $this->where(array('pid' => $pid, 'store_num' => $store_num, 'status' => 0))->find();
		if(empty($rs) || $rs['begintime'] == '0000-00-00 00:00:00' || $rs['endtime'] == '0000-00-00 00:00:00') return array();
				
		$softinfo = D('finance/pluginSoftware')->getInfo("{$rs['pid']}_{$rs['store_num']}");
		if(empty($softinfo) || $softinfo['status'] == 0) return array();
		
		$configinfo = D('finance/pluginConfig')->getInfo($softinfo['code']);
		if(empty($configinfo)) return array();
		
		$data = array();
		$data['task_id'] = (int)$rs['id'];
		$data['task_num'] = $count > 1 ? $count-1 : 0;
		$data['begintime'] = $rs['begintime'];
		$data['endtime'] = $rs['endtime'];
		$data['dbname'] = $softinfo['dbname'];
		$data['sql'] = D('finance/pluginConfig')->getSqlTpl($configinfo['sqltpl'],$data['begintime'],$data['endtime']);;
		return $data;
	}	
	
	
}
