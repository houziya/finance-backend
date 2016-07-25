<?php
/**
 * 用户信息统计
 * -------------------------------------------------------------------------------------------
 * 功能描述
 * -------------------------------------------------------------------------------------------
 * Date: 2016/3/14
 * @author shazhiyu
 *   
 */
class model_admin_statisUser extends model_abstruct
{
	private $start_time='';
	private $end_time='';
	private $qd_limit = 15; //渠道统计数目少于5时，记作其他
	private $source = array('2'=>'web', '3'=>'wap', '4'=>'ios', '5'=>'android'); //来源类别
	private $platform = array();
    //-------------------------------------------------------
	public function setparam($stime='',$etime='')
	{
        $this->start_time =$stime; 
		$this->end_time = $etime;
	}

	public function getplatform() //获取联盟分类
	{
		$sql = "select id,name,is_show from lianmeng_type where is_show=1 order by id asc"; //查询分类
		$arr = $this->query($sql);
		foreach($arr as $k=>$v)
		{
			$id = $v['id'];
			$name = $v['name'];
			$this->platform[$id] = $name;
		}
	}

	public function structwhere($t=0,$field)
	{
		 $w = "";
		 if(!empty($this->start_time))
		 {
               $start_time = $this->start_time." 00:00:00";
			   if($t!=0)
		       {
			      $start_time = strtotime($start_time);
		       }
			   $w .= " and ".$field." >='{$start_time}'";


		 }
		 if(!empty($this->end_time))
		 {
              $end_time = $this->end_time." 23:59:59";
			  if($t!=0)
		      {
			      $end_time = strtotime($end_time);
		      }
			  $w .=" and ".$field." <='{$end_time}'";

		 }
		 if($t!=0)
		 {
			 if(empty($w))
			 {
                  $w .= " and ".$field.">0 ";
			 }
		 }

		 
		 return $w;
	}

	public function statiscz() //充值统计
	{
	   $data =  array();
	   $tt = array();
       $w = $this->structwhere(0,"update_time");
	   $sql = "select count(id) as cc,source from account_recharge_log where status=1 and source>=2".$w." group by source order by source asc";
	   $arr =  $this->query($sql);
	   $total = 0;
	   foreach($arr as $k=>$v)
	   {
		    $cc = $v['cc'];
			$total += $cc;
			$s = $v['source'];
            $sname = "cz_".$this->source[$s];
			$data[] = array('value'=>$cc,'name'=>$this->source[$s]."充值量");
			//$data[$sname] = $cc;
			$tt[] = $this->source[$s]."充值量";
	   }
	   $res = array();
	   $res['cz_data'] = json_encode($data);
	   $res['cz_label'] = json_encode($tt);
	   $res['cz_total'] = $total;
	   //$data['cz_total'] = $total;
	   return $res;
	}
    //update_time
	public function statistz() //投资统计
	{
        $data =  array();
		 $tt = array();
        $w = $this->structwhere(1,"update_time");
		$sql = "select count(id) as cc ,source from project_investment where source>=2 and (status=1 or status=3)".$w." group by source";
		$arr =  $this->query($sql);
	    $total = 0;
	    foreach($arr as $k=>$v)
	    {
		    $cc = $v['cc'];
			$total += $cc;
			$s = $v['source'];
            $sname = "tz_".$this->source[$s];
			$data[] = array('value'=>$cc,'name'=>$this->source[$s]."投资量");
			$tt[] = $this->source[$s]."投资量";
			//$data[$sname] = $cc;
	    }
		$res = array();
		$res['tz_data'] = json_encode($data);
		$res['tz_label'] = json_encode($tt);
		$res['tz_total'] = $total;
	     
	    return $res;

	}


	public function statissex() //按性别统计
	{
	    $w = $this->structwhere(1,"authentication_time");
		$sarr = array(0=>'保密',1=>'男',2=>'女');
        $sql ="select count(id) as cc,sex from statis_user_count where 1 ".$w." group by sex";
		$arr = $this->query($sql);
		$total = 0;
		$data = array();
		$tt=  array();
		//var_dump($arr);
		foreach($arr as  $k=>$v)
		{
              $cc = $v['cc'];
			  $s = $v['sex'];
			  $sname = $sarr[$s];
			  $data[] = array('value'=>$cc,'name'=>$sname);
			  $tt[] = $sname;
              $total +=$cc;
		}
		$res = array();
		$res['sex_data'] = json_encode($data);
		$res['sex_lable'] = json_encode($tt);
		$res['sex_total'] = $total;
		//die;
		return $res;

	}

	public function statisuseragecount()//统计年龄
	{ 
	   $w = $this->structwhere(0,"birthday");
	   $dt1 = array();
	   $dt2 = array(); //
	   $sql = "select count(*)as cc,date_format(from_days(to_days(now()) - to_days(birthday)),'%Y')+0 as age  from 
statis_user_count where birthday!='0000-00-00' ".$w."  group by age"; //statis_user_count
	   //echo $sql."<br>";
	   $arr = $this->query($sql);
	   $total = 0;
	   foreach($arr as $k=>$v)
	   {
		   $cc = $v['cc'] + 0;
		   $age = $v['age'] +0 ;
		   if($age<14 || $age>80) continue;
		   $dt1[] = $cc;
		   $dt2[] = $age."岁";
		   $total +=$cc;
	   }
	   $res = array();
	   $res['xdata'] = json_encode($dt2);
	   $res['ydata'] = json_encode($dt1);
	   $res['a_total']  =$total;
	  // var_dump($res);
	   return $res;
	}

	public function statisusercitycount() //按城市统计
	{
		$dt1 = array();
		$dt2 = array();
		$sql = "select count(a.uid) as cc,b.name as city from user a left join area b on a.city=b.id where a.city>0  group by a.city order by null";
		$arr = $this->query($sql);
		$total = 0;
		foreach($arr as $k=>$v)
		{
            $cc = $v['cc'] +0;
			$city = $v['city'];
			$dt1[] = $cc;
			$dt2[] = $city;
			$total +=$cc;
		}
		$res = array();
		$res['xdata'] = json_encode($dt2);
		$res['ydata'] = json_encode($dt1);
		$res['total'] = $total;
		return $res;

	}

    //----------------web、wap、ios、android来源统计---------------------------------------
    public function statisreg() //统计新注册人数
	{
		 $w = $this->structwhere(0,"thedate");
         $sql = "select sum(newcome) as total,sum(newcome_web) as webtotal,sum(newcome_wap) as waptotal,sum(newcome_ios) as iostotal,sum(newcome_android) as andtotal from statis_user_daily where 1 ".$w;
		 
         $reg = $this->query($sql);
		 $tnum = array();
		 $tnum['r_total'] = 0;
         $tnum['r_webtotal'] = 0;
         $tnum['r_waptotal'] = 0;
         $tnum['r_iostotal'] = 0;
         $tnum['r_andtotal'] = 0;
		 if(!empty($reg))
		 {
             $tnum['r_total'] = $reg[0]['total']+0;
             $tnum['r_webtotal'] = $reg[0]['webtotal']+0;
             $tnum['r_waptotal'] = $reg[0]['waptotal']+0;
             $tnum['r_iostotal'] = $reg[0]['iostotal']+0;
             $tnum['r_andtotal'] = $reg[0]['andtotal']+0;
		 }
		 return $tnum;
	}
 
 
	public function statischongzhi() //充值统计
	{
		$arr = $this->statisuinfo("frist_recharge_time");
		$return = array();
		foreach($arr as $k=>$v)
		{
           $key = "c_".$k;
		   $return[$key] = $v;
		}
		return $return;
	}

	public function statistouzi() //投资统计
	{
        $arr = $this->statisuinfo("frist_invest_time");
		$return = array();
		foreach($arr as $k=>$v)
		{
           $key = "t_".$k;
		   $return[$key] = $v;
		}
		return $return;
	}

	public function statisauth()
	{
        $arr = $this->statisuinfo("authentication_time");
		$return = array();
		foreach($arr as $k=>$v)
		{
           $key = "a_".$k;
		   $return[$key] = $v;
		}
		return $return;
		
	}

	private function statisuinfo($field='') //统计用户相关
	{
		$tnum = array();
		if(empty($field))
		{
            $tnum['total'] = 0;
			return $tnum;
		}
		$w = $this->structwhere(1,$field);
		$sql = "select count(id) as t,source from statis_user_count where source!=1 ".$w." group by source order by null;";
		//echo $sql."<br>";
		$auth = $this->query($sql);
		$tnum['total'] = 0;
		$tnum['webtotal'] = 0;
	    $tnum['waptotal'] = 0;
	    $tnum['iostotal'] = 0;
	    $tnum['andtotal'] = 0;
		$m = 0;
		foreach($auth as $k=>$v)
		{
				if($v['source']==2)
				{   
					$m = $m+$v['t'];
                    $tnum['webtotal'] = $v['t']+0;
				}
				else if($v['source']==3)
				{
					$m = $m+$v['t'];
                    $tnum['waptotal'] = $v['t']+0;
				}
				else if($v['source']==4)
				{
					 $m = $m+$v['t'];
                     $tnum['iostotal'] = $v['t']+0;
                     
				}
				else if($v['source']==5)
				{
					$m = $m+$v['t'];
                    $tnum['andtotal'] = $v['t']+0;
				}
				else 
				{
					continue;
				}

		}
	    $tnum['total'] = $m;
	    return $tnum;
	}

	function getstatislmplatformdata($tb='',$field='')
	{
           $w = $this->structwhere(1,$field);
		   $ff = "a.id";
		   if($tb=="user")
		   {
			   $ff = "a.uid";
		   }
		   $sql = "select count(".$ff.") as cc,b.lianmeng_typeid as lid  from ".$tb." a left join lianmeng_platform b on a.lianmeng_id=b.id where a.lianmeng_id>0 ".$w."  group by b.lianmeng_typeid order by null;";
		   //echo $sql;
		   $arr = $this->query($sql);
	       $data = array();
	       foreach($arr as $k=>$v)
	       {
				$cc = $v['cc']+0;
				$lid = $v['lid']+0;
				$data[$lid] = $cc;
	       }
		   return $data;
	}

	function foramtlmplatform($data=array(),$tag='')
	{
		 $dt1 = array();
		 $dt2 = array();
		 $i = 0;
		 $total = 0;
		 //var_dump($data);
		 //echo "<br>";
         foreach($this->platform as $k=>$v)
		 {
			$dt2[$i] = $v;
            if(array_key_exists($k,$data))
			{
				$cc =  $data[$k];
				$total +=$cc;
                $dt1[$i]['value'] = $cc;
				$dt1[$i]['name'] = $v;
			}
			else
			{
                $dt1[$i]['value'] =0;
				$total +=$cc;
				$dt1[$i]['name'] = $v;
			}
			$i++;
		 }
		 //var_dump($dt1);
		// echo "<br>";
		 //var_dump(json_encode($dt1));
		// echo "<hr>";
		 $res = array();
		 $res[$tag.'d1'] = json_encode($dt1);
		 $res[$tag.'d2'] = json_encode($dt2);
		 $res[$tag.'total'] = $total;
		 return $res;
	}
    //$this->platform[$id] = $name;
	public function newstatisreglmplatform() //按大分类统计注册
	{
       $data = $this->getstatislmplatformdata('user','regist_time');
	   $res = $this->foramtlmplatform($data,"reg_");
	   return $res;

	}

	
     //authentication_time
	public function newstatisauthlmplatform() //按大分类统计认证
	{
	    $data = $this->getstatislmplatformdata('statis_user_count','authentication_time');
		$res = $this->foramtlmplatform($data,"auth_");
	    return $res;

         
		
	}
    //frist_recharge_time  
	public function newstatisczlmplatform() //按大分类统计新充值
	{
		$data = $this->getstatislmplatformdata('statis_user_count','frist_recharge_time');
		$res = $this->foramtlmplatform($data,"cz_");
	    return $res;
       
	}
    //frist_invest_time
	public function newstatistzlmplatform() //按大分类统计新投资
	{
		$data = $this->getstatislmplatformdata('statis_user_count','frist_invest_time');
		$res = $this->foramtlmplatform($data,"tz_");
	    return $res;
        
	}
   //select count(a.uid) as cc,a.lianmeng_id as lid,b.name as name from user a left join lianmeng_platform b on a.lianmeng_id=b.id where 1    group by a.lianmeng_id order by cc desc limit 30
	//----------------根据渠道统计---------------------------------------
	public  function statisreglmplatform($t=0) //统计注册渠道
	{
	   
       $w = $this->structwhere(1,"regist_time");
       $sql = "select count(a.uid) as cc,a.lianmeng_id as lid,b.name as name from user a left join lianmeng_platform b on a.lianmeng_id=b.id where 1 ".$w."  group by a.lianmeng_id order by cc desc limit 30;";
	   //$sql = "select count(uid) as cc,lianmeng_id from user  where 1 ".$w."  group by lianmeng_id;"
	   $arr = $this->query($sql);
	   if($t==1)
	   {
            return $arr;
	   }
	   else
	   {
           $data = $this->makestructforqd($arr);
		   return $data;
	   }
	   
	  

	}

	public function statisauthlmplatform($t=0) //统计认证渠道
	{
        $data = $this->statisqd("authentication_time",$t,1);
		return $data;
	}

	public function statisczlmplatform($t=0)//统计充值  渠道  
	{ 
        $data = $this->statisqd("frist_recharge_time",$t,2);
		return $data;
	} 

	public function statistzlmplatform($t=0)//统计投资  渠道
	{ 
        $data = $this->statisqd("frist_invest_time",$t,3);
		return $data;
	} 

	private function makestructforqd($arr = array(),$type=0) //构造渠道统计使用的数组结构
	{
       $data = array();
	   $alldata  = array(); //总体
	   $alldata['all_s'] = array();
	   $alldata['all_d'] = array();
	   $partdata  = array(); //部分
	   $partdata['part_s']  = array(); 
	   $partdata['part_d']  = array();
	   if($type==0)
	   {
		   $tag = "网站注册";
	   }
	   else if($type==1)
	   {
           $tag = "网站认证";
	   }
	   else if($type==2)
	   {
           $tag = "网站充值";
	   }
	   else if($type==3)
	   {
           $tag = "网站投资";
	   }
	   $i = 0;
	   $qsum = 0; //其他总渠道和
	   $wsum = 0; //网站总渠道和
	   $qlimit = 0; //其他总数记录（个数小于qd_limit）
	   foreach($arr as $k=>$v)
	   {
          $lid = $v['lid']+0;
		  $num = $v['cc']+0;
		  $name = $v['name'];
		  if($lid==0)
		  {
              $wsum = $num;
              $alldata['all_s'][0] = $tag;
			  $alldata['all_d'][0]['value'] = $num;
			  $alldata['all_d'][0]['name'] = $tag;
		  }
		  else
		  {  
			 $qsum +=$num;
             $alldata['all_s'][1] = "其他";
			 $alldata['all_d'][1]['value'] = $qsum;
			 $alldata['all_d'][1]['name'] = "其他";
		  }
		  if($lid>0) //处理其他
		  {
			  if($num<$this->qd_limit)
			  {
                 $qlimit+=$num;
				 continue;
			  }
              if(!empty($name))
		      {
                 $partdata['part_s'][$i] = $name;
		      }
			  else
			  {
                 $partdata['part_s'][$i] = $lid;
			  }
			  $partdata['part_d'][$i]['value'] = $num;
			  $partdata['part_d'][$i]['name'] = $partdata['part_s'][$i];
			  $i++;
		  }
	   }
	   if($qlimit>0)
	   {
		   $partdata['part_s'][$i] = "其他";
           $partdata['part_d'][$i]['value'] = $qlimit;
		   $partdata['part_d'][$i]['name'] = "其他";
	   }
	   //0--不用展示   1--需要展示
	   $data['cfg'] = 0;  //总体饼图默认不需要展示
	   $data['cfg_a'] = 1; //
	   $data['cfg_p'] = 1; //分部饼图默认需要展示
	   if($qsum==0)
	   {
           $data['cfg_p'] = 0;
	   }
	   if($wsum==0)
	   {
		    $data['cfg_a'] = 0;
	   }
	   if($qsum>0 || $wsum>0)
	   {
           $data['cfg'] = 1;
	   }
	   
	   $data['all'] = $alldata;
	   $data['part'] = $partdata;
	   return $data;
	}
    

	private function statisqd($field,$t=0,$type=0) //渠充值道统计(实名、充值、投资)
	{
         $w = $this->structwhere(1,$field);
		 $sql = "select count(a.id) as cc,a.lianmeng_id as lid,b.name as name from statis_user_count a left join lianmeng_platform b on a.lianmeng_id=b.id where 1 ".$w."  group by a.lianmeng_id order by cc desc limit 30;";
		 $arr = $this->query($sql);
		 if($t==1)
		 {
              return $arr;
		 }
		 else
		 {
              $data = $this->makestructforqd($arr,$type);
		      return $data;
		 }
		 
	}

	//-----------------------按省份统计--------------------------
	public function statisprovreg() //按省份统计新注册
	{
          $data = $this->statisprovince("regist_time",0);
		  return $data;
	}

	public function statisprovauth() //按省份统计认证
	{
         $data = $this->statisprovince("authentication_time",1);
		  return $data;
	}

	public function statisprovcz() //按省份统计认证
	{
         $data = $this->statisprovince("frist_recharge_time",1);
		  return $data;
	}

	public function statisprovtz() //按省份统计认证
	{
         $data = $this->statisprovince("frist_invest_time",1);
		  return $data;
	}


	private function makesql($field,$t=0)
	{
		$sql  = "";
		$w = $this->structwhere(1,$field);
		if($t==0) //user表连接area
		{
           $sql="select b.name as name,count(a.uid) as value from user a left join area b on a.province=b.id where a.province!=0 ".$w." group by a.province order by value asc;";
		}
		else  //user_count 连接 area
		{
             $sql="select c.name as name,count(a.id) as value from statis_user_count a,user b,area c where a.uid=b.uid and b.province=c.id and b.province>0  ".$w." group by b.province order by value asc;";
		}
		//echo $sql."<br>";
		return $sql;
	}
    
	 
	private function statisprovince($field,$t)//按省份统计注册、认证、充值、投资数据
	{
		 $data = array();
		 $data['cfg'] = 0;
         $sql = $this->makesql($field,$t);
		 $arr = $this->query($sql);
		// $dd = array();
		
		 foreach($arr as $k=>&$v)
		 {
			 if($v['name']=="内蒙古自治区")
			 {
                 $v['name'] = "内蒙古";
				 continue;
			 } 
			 if($v['name']=="黑龙江省")
			 {
                 $v['name'] = "黑龙江";
				 continue;
			 }
			 $v['name'] = helper_string::cnsubstr($v['name'],4);
		 }
		 $data['min'] = 0;
		 $data['max'] = 0;
		 if(!empty($arr))
		 {
			 reset($arr);
			 $first = current($arr);
             $data['min'] = $first['value'];
             $data['cfg'] = 1;
			 $last = end($arr);
			 $data['max'] = $last['value'];

			 
		 }

		 $data['data'] = $arr;
         return $data;

	}


}