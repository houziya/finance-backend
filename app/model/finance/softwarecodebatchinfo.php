<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareCodeBatchInfo extends model {

	protected $tableName = 'client_code_batchinfo';
        //激活码状态
	static public $status_arr = array(
            // ID -- 名称 -- 显示样式
            -1 => array('id' => -1, 'name' => '作废', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            0 => array('id' => 0, 'name' => '未使用', 'style' => ' class="blue"', 'style2' => ' class="blue"'),
            1 => array('id' => 1, 'name' => '已使用', 'style' => ' class="green"', 'style2' => ' class="green"'),
            2 => array('id' => 2, 'name' => '禁用', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
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
	 * 获取软件激活码(带缓存)
	 * @param string $args  token 用户软件标识码
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getInfoByCode($code, $field = null, $delcache = false) {
		if (empty($code)) return array();
		$cachename = 'model_finance_sortware_client_code_batchinfo' . $code;
		$info = S($cachename);
		if (empty($info) || $delcache) {
                        $map['code'] = $code;
                        $table2 = 'client_code_batch';
                        if(empty($field)){
                          $field = "b.batch_id,a.cstatus,a.sstatus,a.code,a.sort_id,a.uid,a.sort_client_id,a.start_time,a.end_time,a.status";
                        }
			$info = M('client_code_batchinfo')->alias('a')->join("$table2 AS b ON a.bid = b.id")->field($field)->where($map)->find($info);
			if (empty($info)) return array();
			S($cachename, $info);
		}
		return $info;
	}
        
       
        
        /*
	 * 修改激活码状态
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id, $code,$data = array()){
		if(empty($id)) return false;
                $status = M('client_code_batchinfo')->where(array('id' => $id))->save($data);
               // exit;
                if($status){
                     $this->getInfoByCode($code, $field = null, true);
                }
                return $status;
	}
        
         /*
	 * 按批次增加软件激活码
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $rs = $this->add($data);
		return $rs;
	}
}
