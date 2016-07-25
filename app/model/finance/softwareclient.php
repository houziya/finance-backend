<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_softwareClient extends model {

	protected $tableName = 'sortware_client';
        
         //软件客户端状态
	static public $status_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '禁用', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '启用', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );
        
        //软件客户端离线在线状态
	static public $online_status_arr = array(
            // ID -- 名称 -- 显示样式
            0 => array('id' => 0, 'name' => '离线', 'style' => ' class="gray"', 'style2' => ' class="gray"'),
            1 => array('id' => 1, 'name' => '在线', 'style' => ' class="green"', 'style2' => ' class="green"'),
        );

	/**
	 * 获取单条财务软件信息(带缓存)
	 * @param string $args  token 用户软件标识码
	 * @param null $field
	 * @param bool $delcache 是否删除缓存
	 * @return array|mixed
	 */
	public function getInfo($id, $field = null, $delcache = false) {
		if (empty($id)) return $field ? '' : array();
		$cachename = 'model_finance_sortware_client_' . $id;
		$info = S($cachename);
		if (empty($info) || $delcache) {
			$info = M('sortware_client')->where(array('id' => $id))->find();
			if (empty($info)) return $field ? '' : array();
			S($cachename, $info);
		}
		return $field ? $info[$field] : $info;
	}
    /*根据识别码进行信息查询
     * $identification识别码
     * $field某个字段值
     * $delcache是否删除缓存
     */
    public function getInfoByIdentification($identification, $field = null, $delcache = false) {
        if (empty($identification)) return $field ? '' : array();
        $cachename = 'model_finance_sortware_client_by_identification_' . $identification;
        $info = S($cachename);
        if (empty($info) || $delcache) {
            $info = M('sortware_client')->where(array('identification' => $identification))->find();
            if (empty($info)) return $field ? '' : array();
            S($cachename, $info);
        }
        return $field ? $info[$field] : $info;
    }

    /*查看客户端软件信息表里的mobile是否存在
     *2016-7-4
     * wangmengmeng
     */
    public function isExpireMobile($mobile, $delcache = false) {
        $cachename = 'model_finance_sortware_client_is_expire_mobile_' . $mobile;
        $count = S($cachename);
        if((empty($count) && $count != 0) || $delcache) {
            $count = M('sortware_client') -> where(array('mobile' => $mobile)) -> count();
            S($cachename, $count);
        }
        return $count > 0;
    }
    /*查看客户端软件信息表里的激活码是否存在
     *2016-7-4
     * wangmengmeng
     */
    public function isExpireCode($code, $delcache = false) {
        $cachename = 'model_finance_sortware_client_is_expire_code_' . $code;
        $count = S($cachename);
        if((empty($count) && $count != 0) || $delcache) {
            $count = M('sortware_client') -> where(array('code' => $code)) -> count();
            S($cachename, $count);
        }
        return $count > 0;
    }
	/*
	 * 修改客户端软件
	 * @author tianxiang
	 * @param Array $data
	 * @return bool
	 */
	public function update($id, $data = array()){
		if(empty($id) || empty($id)) return false;
                $status = M('sortware_client')->where(array('id' => $id))->save($data);
                if($status){
                     $this->getInfo($id,null,true);
                }
                return $status;
	}
        
        /*
	 * 安装软件
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
                $identification = $data['identification'];
                $mobile = $data['mobile'];
                $row  = M('sortware_client')->where(array('identification' => $identification,'mobile' => $mobile))->find();
                //没有记录，说明新安装
                if(empty($row)){
                     $rs = $this->add($data);
		     return $rs; 
                }else{
                     //有记录，说明重装
                     unset($data['identification']);
                     unset($data['mobile']);
                     unset($data['add_time']);
                     $data['update_time'] = time();
                     $status = $this->update($row['id'], $data);
                     if($status){
                         return $row['id'];
                     }
                }
	}
        
         /**
	 * 激活客户端
         * @param int $uid   用户ID
         * @param string $id   客户端ID
	 * @param string $code 激活码
	 * @param Array $data 绑定数据 
	 * @return bool $status
	 */
	public function clientActivation($uid,$id,$code) {
		if(empty($id) || empty($code)) return false;
                //客户端详情
                $info = $this->getInfo($id);
                if(!empty($info)){
                    $data = array();
                    if($info['code'] == $code){
                         //重装
                         $status = M('sortware_client')->where(array('code' => $code,'id' => $id))->setInc('install_num',1);
                    }else{
                        //第一次安装
                         $data['uid'] = $uid; 
                         $data['code'] = $code; 
                         $data['install_num'] = $info['install_num']+1;
                         $data['status'] = 1; 
                         $status = M('sortware_client')->where(array('id' => $id))->save($data);
                    }
                    //更新激活码安装次数
                   M('client_code_batchinfo')->where(array('code' => $code))->setInc('num',1);
                   //更新激活码状态,并绑定客户端
                   M('client_code_batchinfo')->where(array('code' => $code))->save(array("status"=>1,"sort_client_id"=>$id));
                   //删除临时表
                   M('sortware_install_verify')->where(array('code' => $code))->delete();
                }
                
                D('finance/softwareCodeBatchInfo')->getInfoByCode($code, $field = null, true);
                $this->getInfo($id,null,true);
                return $status;
	}
}
