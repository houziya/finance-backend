<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareFault extends model {

	protected $tableName = 'sortware_fault';
        /**
	 * 软件故障
	 * @author tianxiang 
	 * @return $rs
	 */
	public function returnFault($data = array()) {
	     $rs = M('sortware_fault')->add($data);
             return $rs;
	}
}
