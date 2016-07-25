<?php
/**
 * Class model_finance_pluginConfig
 * 获取财务插件的配置信息
 */

class model_finance_pluginConfig extends model
{

    protected $tableName = 'financial_plugin_config';

    /**
     * 根据code获取一条配置信息
     * @param string $code 软件标识符
     * @param string $field
	 * @param bool $delcache 是否删除缓存
     * @return array|mixed
     */
	public function getInfo($code, $field = null, $delcache = false) {
		if (empty($code)) return $field ? '' : array();
		$cachename = 'model_finance_pluginconfig_' . $code;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = $this->where(array('code' => $code))->find();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
	
	/*
	 * 获取替换过的sql语句
	 * @author liufei
	 * @param string $sql sql模版
	 * @param string $t1 开始时间
	 * @param string $t2 结束时间
	 * @return return
	 */
	public function getSqlTpl($sql, $t1, $t2){
		$sql = str_replace(array("\r","\n"), ' ', $sql);
		$sql = preg_replace('/[ ]+/', ' ', $sql);
		$sql = str_replace(array('{#begintime#}','{#endtime#}'), array($t1,$t2), $sql);
		return $sql;
	}

}
