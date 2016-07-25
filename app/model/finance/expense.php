<?php

/**
 * Class model_finance_pluginSoftware
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */
class model_finance_expense extends model {

    protected $tableName = 'expense';
	/*
	 * 保存数据
	 * @author tianxiang
	 * @param array $data 数据库相关信息
	 * @return bool
	 */
	public function save($data = array()){
           $sort_client_id = $data['sort_client_id'];
           $qid = $data['qid'];
           $qu_type = $data['qu_type'];
           $start_time = $data['start_time'];
           $end_time = $data['end_time'];
           
           $map['start_time'] = array(array('egt',$start_time));
           $map['end_time'] = array(array('elt',$end_time));
           $map['sort_client_id'] = $sort_client_id;
           //删除重复数据
           M('expense')->where($map)->delete(); 
           $rs =  M('expense')->add($data);
           if(!empty($rs)){
                M('sortware_client')->where(array("id"=>$sort_client_id))->save(array("last_online_time"=>time(),"online_status"=>1));
           }
           return $rs;
	}
}
