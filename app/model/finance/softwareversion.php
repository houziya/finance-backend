<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareVersion extends model {

	protected $tableName = 'sortware_version';
        
        //软件版本状态
	static public $status_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '禁用', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '开启', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );

	/**
	 * 获取软件厂商列表(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getInfo($id = null,$field = null,$delcache = false) {
		$cachename = 'model_finance_sortware_version'.$id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_version')->where(array('id' =>$id))->find();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /**
	 * 获取软件版本列表(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getVersionListById($sort_id,$field = null,$delcache = false) {
		$cachename = 'model_finance_sortware_version'.$sort_id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_version')->where(array('sort_id' =>$sort_id))->findAll();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /*
	 * 修改软件版本
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id,$data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('sortware_version')->where(array('id' => $id))->save($data);
                if($status){
                     $this->getInfo($id,null,true);
                }
                return $status;
	}
         /*
	 * 增加软件软件版本
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $rs = $this->add($data);
		return $rs;
	}
}
