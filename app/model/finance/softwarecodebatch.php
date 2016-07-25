<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareCodeBatch extends model {

	protected $tableName = 'client_code_batch';
        
        //激活码状态
	static public $mstatus_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '未生成', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '已生成', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );
        
        //激活码制卡状态
	static public $cstatus_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '未制卡', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '已制卡', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );
        
        //激活码生成状态
	static public $sstatus_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '未出售', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '已出售', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );
        
        /**
	 * 获取激活卡批次(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getInfo($id,$field = null,$delcache = false) {
                if(empty($id) || empty($id)) return false;
		$cachename = 'model_finance_client_code_batch'.$id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('client_code_batch')->where(array('id' =>$id))->find();
			if (empty($info)) $info = array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        /*
	 * 修改批次
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id, $data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('client_code_batch')->where(array('id' => $id))->save($data);
                if($status){
                     //处理缓存
                     $info = $this->getInfo($id,null,true);
                }
                return $status;
	}
	 /*
	 * 增加激活码批次
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $rs = $this->add($data);
		return $rs;
	}
}
