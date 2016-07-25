<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_software extends model {

	protected $tableName = 'sortware';
        
        //软件状态
	static public $status_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '关闭', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '开启', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );

	/**
	 * 获取软件厂商列表(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getSoftwareFactory($field = null,$delcache = false) {
		$cachename = 'model_finance_sortware';
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware')->where(array("status"=>1))->findAll();
			if (empty($info)) $info = array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
        
        /**
	 * 获取软件列表(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getVersionListById($sort_id,$field = null,$delcache = false) {
		$cachename = 'model_finance_sortware_version_list'.$sort_id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
                        $map['sort_id'] = $sort_id;
                        $map['sortware_version.status'] = 1;
                        $map['q.status'] = array('in','1,2');
                        $table2 = 'sortware_version_queryconfig';
                        $field = "sortware_version.id,sortware_version.version,q.id as qid,q.qu_type,q.qu_frequency,q.db_type,q.db_name,q.db_username,q.db_pwd,q.db_address,q.db_sql,q.status";
			$list = M('sortware_version')->join("$table2 AS q ON sortware_version.id = q.vid")->field($field)->where($map)->findAll();
			if (empty($list)) {
                          $info = array();
                        }else{
                          $info = $list; 
                        }
			S($cachename, $info);
		}
		return $info;
	}
        
          /*
	 * 修改软件
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id, $data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('sortware')->where(array('id' => $id))->save($data);
                if($status){
                     //处理缓存
                     $this->getInfo($id,null,true);
                     $this->getVersionListById($id,null,true);
                     $this->getSoftwareFactory(null,true);
                }
                return $status;
	}
        
        /**
	 * 获取软件详情(带缓存)
	 * @param $delcache 是否删除缓存
	 * @author tianxiang 
	 * @return array|mixed
	 */
	public function getInfo($id,$field = null,$delcache = false) {
                if(empty($id) || empty($id)) return false;
		$cachename = 'model_finance_sortware'.$id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware')->where(array('id' =>$id))->find();
			if (empty($info)) $info = array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
         /*
	 * 添加软件版本
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $rs = $this->add($data);
                if(!empty($rs)){
                    //处理缓存
                    $this->getInfo($id,null,true);
                    $this->getVersionListById($id,null,true);
                    $this->getSoftwareFactory(null,true);
                }
		return $rs;
	}
        
        /*
	 * 获得软件列表
	 * @author tianxiang
	 * @param 无
	 * @return $option
	 */
	public function getSelect(){
                $result = $this->getSoftwareFactory();
                $option = '';
                if(!empty($result)){
                    foreach ($result as $key => $value) {
                        $option.= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
                    }
                }
		return $option;
	}
}
