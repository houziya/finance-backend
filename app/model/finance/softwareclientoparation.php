<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareClientOparation extends model {

	protected $tableName = 'sortware_client_oparation';
        
         //操作列表的状态
	static public $status_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '关闭', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '启用', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );

	/**
	 * 根据动作获取单条操作软件信息(带缓存)
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getInfoById($id, $field = null, $delcache = false) {
		if (empty($id)) return $field ? '' : array();
		$cachename = 'model_finance_sortware_client_oparation_' . $id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_client_oparation')->where(array('id' => $id))->find();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /**
	 * 获取软件操作列表(带缓存)
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getSoftwareOparationList($field = null, $delcache = false) {
		$cachename = 'model_finance_sortware_client_oparation';
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_client_oparation')->where(array("status"=>1))->findAll();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /*
	 * 修改操作
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id, $data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('sortware_client_oparation')->where(array('id' => $id))->save($data);
                if($status){
                     $this->getInfoById($id,null,true);
                }
                return $status;
	}
        
        /*
	 * 添加操作
	 * @author tianxiang
	 * @param array $data 数据库相关信息
         * @param array $options 表达式
	 * @return bool
	 */
	public function save($data = array(),$options = array()){
                $rs = $this->add($data);
		return $rs;
	}
}
