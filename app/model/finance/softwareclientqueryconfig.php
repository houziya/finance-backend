<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareClientQueryconfig extends model {

	protected $tableName = 'sortware_client_queryconfig'; 
        
        /**
	 * 获取单条sql查询信息(带缓存)
	 * @param string $args  token 用户软件标识码
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getInfo($id, $field = null, $delcache = false) {
		if (empty($id)) return array();
		$cachename = 'model_finance_sortware_client_queryconfig_' . $id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_client_queryconfig')->where(array('id' => $id))->find();
			if (empty($info)) return array();
			S($cachename, $info);
		}
		return $$info;
	}
        
         /**
	 * 根据客户端ID获取查询语句
	 * @param string $cid  客户端ID
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getInfoByCid($cid, $field = null, $delcache = false) {
		if (empty($cid)) return array();
		$cachename = 'model_finance_sortware_client_queryconfigcid_' . $cid;
		$info = S($cachename);
		if (empty($info) || $delcache) {
		        $info = M('sortware_client_queryconfig')->alias('q')->join("sortware_client c on c.id = q.cid")->where(array('q.cid' => $cid))->field("q.*")->findAll();
			if (empty($info)) return array();
			S($cachename, $info);
		}
		return $info;
	}
        
        /*
	 * 修改sql查询信息
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id,$data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('sortware_client_queryconfig')->where(array('id' => $id))->save($data);
                if($status){
                     $this->getInfo($id,null,true);
                }
                return $status;
	}
        /*
	 * 保存查询配置
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $cid = $data['cid'];
                $row  = M('sortware_client_queryconfig')->where(array('cid' => $cid))->find();
                if(empty($row)){
                   $rs = $this->add($data);
		   return $rs;
                }else{//说明重装
                   unset($data['add_time']);
                   $data['update_time'] = time();
                   $rs = $this->update($row['id'],$data);
                   return $rs;
                }
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
