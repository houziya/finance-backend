<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareVersionQueryConfig extends model {

	protected $tableName = 'sortware_version_queryconfig';
        
        //软件版本查询状态
	static public $status_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '禁用', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '开启', 'style' => ' class="green"', 'style2' => ' class="green"'),
            2 => array('id' => 2, 'name' => '默认', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );

	/**
	 * 根据获取软件版本查询配置(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getInfo($id = null,$field = null,$delcache = false) {
		$cachename = 'model_finance_sortware_version_queryconfig'.$id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_version_queryconfig')->where(array('id' =>$id))->find();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /**
	 * 获取软件版本查询列表(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getQueryConfigListById($vid,$field = null,$delcache = false) {
		$cachename = 'model_finance_sortware_version_queryconfig_list'.$vid;
		$info = S($cachename);
		if (empty($info) || $delcache) {
                        $map['vid'] = $vid;
                        $map['status'] = array('in','1,2');
			$info = M('sortware_version_queryconfig')->where($map)->findAll();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /*
	 * 修改软件版本查询配置
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id,$data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('sortware_version_queryconfig')->where(array('id' => $id))->save($data);
                if($status){
                    $this->getInfo($id,null,true);
                }
                return $status;
	}
         /*
	 * 增加软件软件版本查询配置
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $rs = $this->add($data);
		return $rs;
	}
        
        /*
	 * 获取替换过的sql语句
	 * @author tianxiang
	 * @param string $sql sql模版
	 * @param string $t1 开始时间
	 * @param string $t2 结束时间
	 * @return return $sql;
	 */
	public function getSqlTpl($sql, $t1, $t2,$default = false){
                if(!empty($default)){
                   $sqlArr = spliti("WHERE", $sql);
                   $sql = $sqlArr[0];
                }else{
                   $sql = str_replace(array("\r","\n"), ' ', $sql);
                   $sql = preg_replace('/[ ]+/', ' ', $sql);
                   $sql = str_replace(array('{#begin_time#}','{#end_time#}'), array($t1,$t2), $sql);
                }
		return $sql;
	}
}
