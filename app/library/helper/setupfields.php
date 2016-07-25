<?php
/**
 * 设置字段显示类
 * @author szy
 date 2016/4/13
 */
 
class helper_setupfields
{  
	 /*
	     $arr[表名][字段名] = 字段描述
		 字段描述
		 字段意义|is_show(0-不显示 1-显示)|checked(0-不选 1-必选)
		 注意：必选的一定是显示的
	 */
     private $seup = array(  
	   'ts_project'=>array(
		 'id'=>'项目ID|0|0', 
		 'sn'=>'项目编号|1|1',
		 'create_time'=>'创建编号时间|1|1',
		 'name'=>'项目名称|1|1',
		 'source'=>'项目来源|1|1',
		 'headmanager'=>'总站经理|1|0',
		 'subarea'=>'分站|1|0',
		 'submamanager'=>'分站经理|1|0',
	     'trade'=>'项目行业|1|0',
	     'type'=>'项目类型|1|0',
		 'status'=>'管道状态|1|0',
		 'jinjian_date'=>'进件日期|1|0',
	     'pre_preheat_begin_time'=>'预计上线预热日期|1|0',
	     'preheat_begin_time'=>'实际上线预热日期|1|0',
	     'yuyue_user_count'=>'累计总预约人数|1|0',
	     'yuyue_fuser_count'=>'累计首次预约人数|1|0',
	     'avg_yuyue_user_count'=>'日均总预约人数|1|0',
	     'avg_yuyue_fuser_count'=>'日均首次预约人数|1|0',
		 'yuyue_total'=>'预约金额|1|0',
	     'yuyue_rz_rate'=>'预约与融资金额占比|1|0',
		 'pre_amount_begin_time'=>'预计上线融资日期|1|0',
		 'amount_begin_time'=>'实际上线融资日期|1|0',
		 'invest_user_count'=>'累计总认购人数|1|0',
		 'invest_fuser_count'=>'累计首次认购|1|0',
		 'avg_invest_user_count'=>'日均总认购|1|0',
		 'avg_invest_fuser_count'=>'日均首次认购|1|0',
		 'invest_total'=>'累计认购金额|1|0',
		 'invest_rate'=>'累计认购比例|1|0',
		 'finsh_time'=>'融资结束日期|1|0',
	     'hits_count'=>'项目点击量|1|0',
	     'duration'=>'存续期|1|0',
	     'pre_year_return'=>'预期年化收益率|1|0',
	     'pre_sharefee_frequency1'=>'预期基础分红频次|1|0',
		 'pre_sharefee_frequency2'=>'预期浮动分红频次|1|0',
		 'fact_year_return'=>'实际预期年化率|1|0',
		 'founder_pre_finance_total'=>'项目方预期融资|1|0',
		 'pre_servicefee_rate'=>'融资服务费比例|1|0',
		 'pre_service_fee'=>'预期融资服务费|1|0',
		 'pre_share_fee'=>'预期分红收入|1|0',
		 'succ_rate'=>'成功比例|1|0',
		 'change_rate_pre_total'=>'调整后预期融资金额|1|0',
		 'change_rate_pre_servicefee'=>'调整后预期融资服务费|1|0',
		 'change_rate_pre_sharefee'=>'调整后预期分红收入|1|0',
		 'service_fee'=>'实际融资服务费|1|0',
		 'share_fee'=>'实际分红|1|0',
		 'memo'=>'备注|1|0',
	    ),
	 );
	  private $chkarr = array();

	  private $tb; //对应的数据表名称

	  private $tsfields =array();

	  private $uid = 0;


	 public function __construct($tb='')
	 {
		 $this->tb = $tb;
		 
		 
	 }

	 public function setChkarr($chkarr = array())
	 {
           $this->chkarr = $chkarr;
	 }

	 public function setUid($uid=0)
	 {
		 $this->uid = $uid;
	 }

    public  function gettsfields() //获取必须选中的字段
	{
		 $darr = array();
         $tb = $this->tb;
		 $arr = $this->seup[$tb];
		 foreach($arr as $k=>$v)
		 {
             $attr = explode("|",$v);
			 $name = $attr[0];
			 $show = $attr[1];
			 $ischeck = $attr[2];
			 if($ischeck==1) 
			 {
                 array_push($darr,$k);
			 } 
		 } 
		 return $darr;
	}

	 public function showHTML() //获取显示的html
	 {
		 $html = "";
		 $tb = $this->tb;
		 $arr = $this->seup[$tb];
		 foreach($arr as $k=>$v)
		 {
			 $attr = explode("|",$v);
			 $name = $attr[0];
			 $show = $attr[1];
			 $ischk = $attr[2];
			 if($show==0) continue;
			 if(in_array($k,$this->chkarr))
			 {
				 if($ischk==1)
				 {   
                    // array_push($this->tsfields,$k);
                     $html.="<input type='checkbox' name='".$tb."[]' value='{$k}' checked disabled=true/>".$name."&nbsp;&nbsp;";
				 } 
				 else
				 {
                      $html.="<input type='checkbox' name='".$tb."[]' value='{$k}' checked ".$checked."/>".$name."&nbsp;&nbsp;";
				 }
                
			 }
			 else
			 {
				 if($ischk==1)
				 {
					 //array_push($this->tsfields,$k);
                     $html.="<input type='checkbox' name='".$tb."[]' value='{$k}' checked disabled=true />".$name."&nbsp;&nbsp;";
				 }
				 else
				 {
                     $html.="<input type='checkbox' name='".$tb."[]' value='{$k}'  />".$name."&nbsp;&nbsp;";
				 }

                 
			 }
			 
		 }
		 return $html;
	 }
     
     
	 public function getfields() //获取显示字段
	 {
		  $uid = $this->uid +0; 
		  if($uid==0) return false;
		  $key = "setfield_".$this->tb."_".$uid;
		  $cache = S($key);
		  if(!empty($cache)) return $cache;
		  $w = array('uid'=>$uid,'tb'=>$this->tb);
		  $res  =  M('conf_tb')->where($w)->find();
		  //echo M('conf_tb')->sql();
		  $arr = array();
		  if(!empty($res))
		  { 
             $arr = unserialize($res['fields']);
		  } 
		  $data = array();
		  foreach($arr as $k=>$v)
		  {
              $s = $this->seup[$this->tb][$v];
			  $attr = explode("|",$s);
			  $name = $attr[0];
			  $data[$v] = $name;
		  }
		  if(empty($data))
		  {
			  $arr = $this->seup[$this->tb];
			  foreach($arr as $kk=>$vv)
			  {
				  $ts = explode("|",$vv);
				  if($ts[2]==1)
				  {
					  $data[$kk] = $ts[0];
				  }

			  }
		  }
		  S($key,$data);
		  return $data; 
	 }
  
    
  
}  

 