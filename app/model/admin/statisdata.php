<?php
/**
 * 构造缓存数据
 * -------------------------------------------------------------------------------------------
 * 功能描述
 * -------------------------------------------------------------------------------------------
 * Date: 2016/3/30
 * @author shazhiyu
 *   
 */
class model_admin_statisData extends model_abstruct
{
    //private $month1 = array('02','03','04','05','06','07','08','09','10','11','12');
	public $month = array('01','02','03','04','05','06','07','08','09','10','11','12');
	public $year = array('2014','2015','2016');
	public $d_time = array(); //要循环便利的时间数组
    
	/*
	public $lj_month_reg = array(); //累计用户 月份=>val
	public $lj_current_month_reg = array(); //当月数
	public $lj_month_tz = array(); //累计投资   月份=>val

	public $lj_month_rz = array(); //累计融资  月份=>val
	public $lj_month_xm = array(); //累计项目数  月份=>val
	public $lj_month_yj = array(); //累计佣金  月份=>val
    */
	public $source_d_time = array();


	
	/*
	     prefix_qd_reg=>qd_cate_reg  通过不同的渠道注册
		 'prefix_qd_tz'=>'qd_cate_tz' 通过不同渠道投资
		 'prefix_source_reg'=>'source_reg' //通过不同来源注册
		 'prefix_source_tz'=>'source_tz'  不同来源 投资数
		 'prefix_pj_rz'=>'project_rz'  融资
		 'prefix_pj_num'=>'project_num' 项目数
		 'prefix_pj_yj'=>'project_yj' 佣金


	*/
	public  $prefix_cache = array('prefix_qd_reg'=>'qd_cate_reg','prefix_qd_tz'=>'qd_cate_tz','prefix_source_reg'=>'source_reg','prefix_source_tz'=>'source_tz','prefix_pj_rz'=>'project_rz','prefix_pj_num'=>'project_num','prefix_pj_yj'=>'project_yj','prefix_auth_count'=>'auth_count');//缓存前缀
	public $cfg = 1; //缓存开关   1-开启  0-关闭
	public $refresh_cache_fg = 0; //0-   1-强刷缓存

    //渠道注册
	public $total_qd_reg_count = array(); //渠道注册总计
	public $current_month_qd_reg_count =array(); //渠道注册当月总计

	//渠道投资
    public $total_qd_tz_count = 0; //渠道注册总计
	public $current_month_qd_tz_count =0; //渠道注册当月总计

	public $lm_type = array(); //联盟分类数据

	public $source = array('2'=>'web','3'=>'wap','4'=>'ios','5'=>'android'); //来源
    
  
   /*
       $tnum['r_webtotal'] = $reg[0]['webtotal']+0;
             $tnum['r_waptotal'] = $reg[0]['waptotal']+0;
             $tnum['r_iostotal'] = $reg[0]['iostotal']+0;
             $tnum['r_andtotal'] = $reg[0]['andtotal']+0;
    public $lj_month_reg = array(); //累计用户 月份=>val
	public $lj_current_month_reg = array(); //当月数
	  '2'=>'web','3'=>'wap','4'=>'ios','5'=>'android'
   */

   //---------------注册来源统计---------------------------------

   public function makeMonthSourceRegCount() //当月来源注册统计
   {
	   $data = array();
        $current_month = date("Y-m");
		$dd = $this->monthtotime($current_month);
		 $t1 = $dd['t1'];
		 $t2 = $dd['t2'];
		 D('admin/statisuser')->setparam($t1,$t2);
		$reg_num = D('admin/statisuser')->statisreg();
		 $data[$current_month] = array(
			    	 2=>array('tid'=>2,'u_count'=>$reg_num['r_webtotal'],'tname'=>'web'),
				     3=>array('tid'=>3,'u_count'=>$reg_num['r_waptotal'],'tname'=>'wap'),
				     4=>array('tid'=>4,'u_count'=>$reg_num['r_iostotal'],'tname'=>'ios'),
				     5=>array('tid'=>5,'u_count'=>$reg_num['r_andtotal'],'tname'=>'android'),
			 );
        $key = $this->prefix_cache['prefix_source_reg'].":".$current_month;
			//$tkey = $this->prefix_cache['prefix_source_reg']."_lj_total".":".$dv; //月累计
			//$ckey = $this->prefix_cache['prefix_source_reg']."_curr_total".":".$dv; //当月
		$this->setCache($key,$data,10800);

   }

   public function getSourceRegCount() //获取来源注册
   {
	    $darr = array();
		$darr_lj = array();
		foreach($this->source as $k=>$v)
		{
			$darr[$k] = array();
			 
		}
        $current_month = date("Y-m");
		$r_arr = $this->d_time;
		$this->d_time = array_reverse($this->d_time);
		$total = 0;
        foreach($this->d_time as $dk=>$dv)
	    {
			$data = array();
            $key = $this->prefix_cache['prefix_source_reg'].":".$dv;
			$tkey = $this->prefix_cache['prefix_source_reg']."_lj_total".":".$dv; //月累计
			$dd1 = $this->getCache($key);
			$darr_lj[$dv] = $this->getCache($tkey);
			if($dd1===false)
			{
                  $dd = $this->monthtotime($dv);
			      $t1 = $dd['t1'];
			      $t2 = $dd['t2'];
			      D('admin/statisuser')->setparam($t1,$t2);
				  $reg_num  =array();
			      $reg_num = D('admin/statisuser')->statisreg();
				  $data = array(
			    	 2=>array('tid'=>2,'u_count'=>$reg_num['r_webtotal'],'tname'=>'web'),
				     3=>array('tid'=>3,'u_count'=>$reg_num['r_waptotal'],'tname'=>'wap'),
				     4=>array('tid'=>4,'u_count'=>$reg_num['r_iostotal'],'tname'=>'ios'),
				     5=>array('tid'=>5,'u_count'=>$reg_num['r_andtotal'],'tname'=>'android'),
			          );
				   $total =$reg_num['r_webtotal']+$reg_num['r_waptotal']+$reg_num['r_iostotal']+$reg_num['r_andtotal'];
				   $darr_lj[$dv]=$total;
				   if($dv==$current_month)
			      {
                     $this->setCache($key,$data,10800); // 当月保存三个小时
				     $this->setCache($tkey,$total,10800); // 当月保存三个小时
				      
			     }
			     else
			     {
                     $this->setCache($key,$data); //
				     $this->setCache($tkey,$total); 
				      
			     }
  
			}
			else
			{
               $data = $dd1;
			}
            
			if(empty($data))
			{
				foreach($this->source as $yk=>$yv)
				{
                   $darr[$yk][$dv] = 0;
				   
				}
			}
			else
			{
                foreach($this->source as $yk=>$yv)
				{
					if(array_key_exists($yk,$data))
					{
                        $darr[$yk][$dv] = $data[$yk]['u_count'];;
					}
					else
					{
                        $darr[$yk][$dv] = 0;
					}
				}
			}
			//$dd2 = $this->getCache($tkey);
			//var_dump($dd2);

		}
		//var_dump($darr_lj);
		$darr2 = array();
		$sum = 0;
		//var_dump($r_arr );
		foreach($r_arr as $lk=>$lv)
	    {
			if(array_key_exists($lv,$darr_lj))
			{
                $sum = $sum + $darr_lj[$lv];
				$darr2[$lv] = $sum;
			}
			else
			{
				continue;
			}
		}
     
		$sdata = array('d1'=>($darr),'d2'=>($darr2));
		//var_dump($sdata);
		return $sdata;
   }
   public function makeSourceRegCount() //来源统计
   {
        $current_month = date("Y-m");
		$total = 0;
        foreach($this->d_time as $dk=>$dv) //循环年
	    {
			 $data = array();
			 $dd = $this->monthtotime($dv);
			 $t1 = $dd['t1'];
			 $t2 = $dd['t2'];
			 D('admin/statisuser')->setparam($t1,$t2);
			 $reg_num = D('admin/statisuser')->statisreg(); 
			 $data[$dv] = array(
			    	 2=>array('tid'=>2,'u_count'=>$reg_num['r_webtotal'],'tname'=>'web'),
				     3=>array('tid'=>3,'u_count'=>$reg_num['r_waptotal'],'tname'=>'wap'),
				     4=>array('tid'=>4,'u_count'=>$reg_num['r_iostotal'],'tname'=>'ios'),
				     5=>array('tid'=>5,'u_count'=>$reg_num['r_andtotal'],'tname'=>'android'),
			 );
			 $total = $total+$reg_num['r_webtotal']+$reg_num['r_waptotal']+$reg_num['r_iostotal']+$reg_num['r_andtotal'];
			 $ctotal = $reg_num['r_webtotal']+$reg_num['r_waptotal']+$reg_num['r_iostotal']+$reg_num['r_andtotal'];
			 //$this->lj_month_reg[$dv] = $total;
			 /*
			 if($dv==$current_month )
			 {
                $this->lj_current_month_reg = $reg_num['r_webtotal']+$reg_num['r_waptotal']+$reg_num['r_iostotal']+$reg_num['r_andtotal'];
			 }*/

			$key = $this->prefix_cache['prefix_source_reg'].":".$dv;
			$tkey = $this->prefix_cache['prefix_source_reg']."_lj_total".":".$dv; //月累计
			$ckey = $this->prefix_cache['prefix_source_reg']."_curr_total".":".$dv; //当月
			//var_dump($data);
			//echo "<hr>";
			//continue;
			if($dv==$current_month)
			{
                 $this->setCache($key,$data,10800); // 当月保存三个小时
				 $this->setCache($tkey,$total,10800); // 当月保存三个小时
				 $this->setCache($ckey,$ctotal,10800); // 当月保存三个小时
			}
			else
			{
                $this->setCache($key,$data); //
				$this->setCache($tkey,$total); 
				$this->setCache($ckey,$ctotal); 
			}

		}

   }

   public function getSourceTzCount() //获取来源投资
   {
	    $darr = array();
		$darr_lj = array();
		foreach($this->source as $k=>$v)
		{
			$darr[$k] = array();
			 
		}
        $current_month = date("Y-m");
		$r_arr = $this->d_time;
		$this->d_time = array_reverse($this->d_time);
		$total = 0;
	    foreach($this->d_time as $dk=>$dv) //循环年
	    {
			   $data = array();
              $key = $this->prefix_cache['prefix_source_tz'].":".$dv;
	          $tkey = $this->prefix_cache['prefix_source_tz']."_lj_total".":".$dv; //月累计
			  $dd1 = $this->getCache($key);
			  $darr_lj[$dv] = $this->getCache($tkey);
			  if($dd1===false)
			  {
				  $dd = $this->monthtotime($dv);
			      $t1 = $dd['t1'];
			      $t2 = $dd['t2'];
			      D('admin/statisuser')->setparam($t1,$t2);
				  $tz_num = D('admin/statisuser')->statistouzi();
			      $data = array(
			    	 2=>array('tid'=>2,'u_count'=>$tz_num['t_webtotal'],'tname'=>'web'),
				     3=>array('tid'=>3,'u_count'=>$tz_num['t_waptotal'],'tname'=>'wap'),
				     4=>array('tid'=>4,'u_count'=>$tz_num['t_iostotal'],'tname'=>'ios'),
				     5=>array('tid'=>5,'u_count'=>$tz_num['t_andtotal'],'tname'=>'android'),
			         );
				   $total = $tz_num['t_webtotal']+$tz_num['t_waptotal']+$tz_num['t_iostotal']+$tz_num['t_andtotal'];
				   $darr_lj[$dv]=$total;
				   if($dv==$current_month)
				   {
                        $this->setCache($key,$data,10800); // 当月保存三个小时
				        $this->setCache($tkey,$total,10800); // 当月保存三个小时
				   }
				   else
				   {
                         $this->setCache($key,$data); // 当月保存三个小时
				        $this->setCache($tkey,$total); // 当月保存三个小时
				   }
			  }
			  else
			  {
				  $data = $dd1;
			  }

			 if(empty($data))
			{
				foreach($this->source as $yk=>$yv)
				{
                   $darr[$yk][$dv] = 0;
				   
				}
			}
			else
			{
                foreach($this->source as $yk=>$yv)
				{
					if(array_key_exists($yk,$data))
					{
                        $darr[$yk][$dv] = $data[$yk]['u_count'];;
					}
					else
					{
                        $darr[$yk][$dv] = 0;
					}
				}
			}
			  
		}
		$darr2 = array();
		$sum = 0;
		foreach($r_arr as $lk=>$lv)
	    {
			if(array_key_exists($lv,$darr_lj))
			{
                $sum = $sum + $darr_lj[$lv];
				$darr2[$lv] = $sum;
			}
			else
			{
				continue;
			}
		}

		$sdata = array('d1'=>($darr),'d2'=>($darr2));
		//var_dump($sdata);
		return $sdata;
        
   }
   
   /*
       $tnum['t_webtotal'] = 0;
	    $tnum['t_waptotal'] = 0;
	    $tnum['t_iostotal'] = 0;
	    $tnum['t_andtotal'] = 0;
   */
   //D('admin/statisuser')->statistouzi();  投资
   public function makeSourceTzCount()
   {
        $current_month = date("Y-m");
		$total = 0;
        foreach($this->d_time as $dk=>$dv) //循环年
	    {
             $data = array();
			 $dd = $this->monthtotime($dv);
			 $t1 = $dd['t1'];
			 $t2 = $dd['t2'];
			 D('admin/statisuser')->setparam($t1,$t2);
			 $tz_num = D('admin/statisuser')->statistouzi();
			  $data[$dv] = array(
			    	 2=>array('tid'=>2,'u_count'=>$tz_num['t_webtotal'],'tname'=>'web'),
				     3=>array('tid'=>3,'u_count'=>$tz_num['t_waptotal'],'tname'=>'wap'),
				     4=>array('tid'=>4,'u_count'=>$tz_num['t_iostotal'],'tname'=>'ios'),
				     5=>array('tid'=>5,'u_count'=>$tz_num['t_andtotal'],'tname'=>'android'),
			 );
			 $total = $total+$tz_num['t_webtotal']+$tz_num['t_waptotal']+$tz_num['t_iostotal']+$tz_num['t_andtotal'];
			 $key = $this->prefix_cache['prefix_source_tz'].":".$dv;
			 $tkey = $this->prefix_cache['prefix_source_tz']."_lj_total".":".$dv; //月累计
			 if($dv==$current_month)
			{
                 $this->setCache($key,$data,10800); // 当月保存三个小时
				 $this->setCache($tkey,$total,10800); // 当月保存三个小时
				// $this->setCache($ckey,$ctotal,10800); // 当月保存三个小时
			}
			else
			{
                $this->setCache($key,$data); //
				$this->setCache($tkey,$total); 
				//$this->setCache($ckey,$ctotal); 
			}

		}
   }

   //--------------------------项目信息统计-------------------
   //'prefix_pj_num'=>'project_num' 项目数
	//	 'prefix_pj_yj'=>'project_yj' 佣金

	public function getProjectNumCount()
	{
		 $darr = array();
		 $current_month = date("Y-m");
		 $r_arr = $this->d_time;
		 $this->d_time = array_reverse($this->d_time);
         foreach($this->d_time as $dk=>$dv) //循环年
		 {
             $key = $this->prefix_cache['prefix_pj_num'].":".$dv;
			 $dd1 = $this->getCache($key);
			 if($dd1===false)
			 {
                    $total = $this->makeOneMonthProjectNumCount($dv);//
					$darr[$dv] = $total;
					if($dv==$current_month)
					{
						$this->setCache($key,$total,10800); 
						//$this->setCache($tkey,$sum,10800);
					}
					else
					{ 
						$this->setCache($key,$total); 
				   //$this->setCache($tkey,$sum);
					}
			 }
			 else
			 {
                 $darr[$dv] = $dd1;
			 }

			  //$tkey = $this->prefix_cache['prefix_pj_num']."_lj_total".":".$dv;  
		 } 
		$darr2 = array();
		$sum = 0;
		foreach($r_arr as $lk=>$lv)
	    {
			if(array_key_exists($lv,$darr))
			{
                $sum = $sum + $darr[$lv];
				$darr2[$lv] = $sum;
			}
			else
			{
				continue;
			}
		}
		//var_dump($darr);
		//echo "<hr>";
		// var_dump($darr2);
		 $sdata = array('d1'=>$darr,'d2'=>($darr2));
		 return $sdata;
	}
   public function makeProjectNumCount()
   {
        $current_month = date("Y-m");
		$sum = 0;
		foreach($this->d_time as $dk=>$dv) //循环年
	    {
              $total = $this->makeOneMonthProjectNumCount($dv);//
			  $sum  = $sum + $total;
			  $key = $this->prefix_cache['prefix_pj_num'].":".$dv;
			  $tkey = $this->prefix_cache['prefix_pj_num']."_lj_total".":".$dv;
			  if($dv==$current_month)
			  {
                   $this->setCache($key,$total,10800); 
				   $this->setCache($tkey,$sum,10800);
			  }
			  else
			 { 
				   $this->setCache($key,$total); 
				   $this->setCache($tkey,$sum);
			  }

		}
   }

   public function makeOneMonthProjectNumCount($t) //单月项目数统计
   {
       $total = 0;
       $dd = $this->monthtotime($t,1);
	   $t1 = $dd['t1'];
	   $t2 = $dd['t2'];
	   //$sql = "select count(id) as total from project where (status=6 or status=8) and is_show=1 and  finsh_time>=".$t1." and finsh_time<=".$t2;
	    $sql = "select count(id) as total from project where (status=6 or status=7) and is_show=1 and  finsh_time>=".$t1." and finsh_time<=".$t2;
	  // echo $sql."<br>";
	   $arr = $this->query($sql);
	   $total = $arr[0]['total'];
	   return $total; 
   }
   public function makeCurrMonthProRzCount() //融资统计
	{
           $current_month = date("Y-m");
		   $total = $this->makeOneMonthProjectRzCount($current_month);
		   $key = $this->prefix_cache['prefix_pj_rz'].":".$dv;
		   $this->setCache($key,$total,10800); 

   }

   public function getProjectRzCount()
   {
	    $darr_rz = array();
		$darr_yj = array();
		$r_arr = $this->d_time;
		$current_month = date("Y-m");
		$this->d_time = array_reverse($this->d_time);
        foreach($this->d_time as $dk=>$dv) //循环年
	    {
              $key = $this->prefix_cache['prefix_pj_rz'].":".$dv;
			  $ykey = $this->prefix_cache['prefix_pj_yj'].":".$dv;
			  $d1 = $this->getCache($key);
			  $d2 = $this->getCache($ykey);
			  if($d1===false)
			  {
                   $total = $this->makeOneMonthProjectRzCount($dv) +0;
                   $darr_rz[$dv] = $total;
				   $syj = $total *0.05;
				   $darr_yj[$dv] = $syj;
				   if($dv==$current_month)
			      {
                   $this->setCache($key,$total,10800); 
				   $this->setCache($ykey,$syj,10800);
			      }
			      else
			     { 
				   $this->setCache($key,$total); 
				   $this->setCache($ykey,$syj);
			      }
			  } 
			  else
			  {
                 $darr_rz[$dv] = $d1;
				 $darr_yj[$dv] = $d2;
			  }
			 
		}

		$darr2 = array();
		$sum = 0;
		foreach($r_arr as $lk=>$lv)
	    {
			if(array_key_exists($lv,$darr_rz))
			{
                $sum = $sum + $darr_rz[$lv];
				$darr2[$lv] = $sum;
			}
			else
			{
				continue;
			}
		}

		$darr3 = array();
		$sum = 0;
		foreach($r_arr as $lk=>$lv)
	    {
			if(array_key_exists($lv,$darr_yj))
			{
                $sum = $sum + $darr_yj[$lv];
				$darr3[$lv] = $sum;
			}
			else
			{
				continue;
			}
		}
		/*
		var_dump( $darr_rz);
		echo "<br>";
		var_dump($darr2);
		echo "<hr>";
		var_dump($darr_yj);
		echo "<br>";
		var_dump($darr3);
		*/
		$sdata = array('d1'=>$darr_rz,'d2'=>($darr2),'d3'=>$darr_yj,'d4'=>($darr3));
		//var_dump($sdata);
		return $sdata;

   }

   public function makeProjectRzCount() //融资总量统计
   {
	    $current_month = date("Y-m");
		$sum = 0;
        foreach($this->d_time as $dk=>$dv) //循环年
	    {
              $total = $this->makeOneMonthProjectRzCount($dv) +0;//
			  //var_dump($total);
			  $sum  = $sum + $total;
			  $ytotal = $total * 0.05;
			  $ysum = $sum*0.05;
			  $key = $this->prefix_cache['prefix_pj_rz'].":".$dv;
			  $ykey = $this->prefix_cache['prefix_pj_yj'].":".$dv;
			  $tkey = $this->prefix_cache['prefix_pj_rz']."_lj_total".":".$dv;
			  $ytkey = $this->prefix_cache['prefix_pj_yj']."_lj_total".":".$dv;
			  echo $total."---".$sum."----".$ytotal."----".$ysum."<hr>"; 
			  if($dv==$current_month)
			  {
                   $this->setCache($key,"".$total,10800); 
				   $this->setCache($ykey,"".$ytotal,10800); 
				   $this->setCache($tkey,"".$sum,10800);
				   $this->setCache($ytkey,"".$ysum,10800);
			  }
			  else
			 { 
				   $this->setCache($key,"".$total); 
				    $this->setCache($ykey,"".$ytotal); 
				   $this->setCache($tkey,"".$sum);
				    $this->setCache($ytkey,"".$ysum); 
			  }

		}
   }

   

   public function makeOneMonthProjectRzCount($t) //单月融资统计
   {
	   $total = 0;
	   
       $dd = $this->monthtotime($t,1);
	   
	   $t1 = $dd['t1'];
	   $t2 = $dd['t2'];
	   //$sql = "select sum(finance_total) as total from project where (status=6 or status=8) and is_show=1 and   finsh_time>=".$t1." and finsh_time<=".$t2;
	   $sql = "select sum(finance_total) as total from project where (status=6 or status=7) and is_show=1 and   finsh_time>=".$t1." and finsh_time<=".$t2;
	   $arr = $this->query($sql);
	   $total = $arr[0]['total'];
	   return $total; 

   }



   
   
   
   //-------------------------------------------------------

   public function yearisrun($year) //判断瑞年还是平年  28   29
   {
	  
      $time = mktime(20,20,20,4,20,$year);//取得一个日期的 Unix 时间戳;  
      if (date("L",$time)==1)
	   { //格式化时间，并且判断是不是闰年，后面的等于一也可以省略；  
         return true;
       }
	   else
	   {  
          return false;
       }  
   }
   public function monthtotime($t,$type=0) //返回月的开始一天 和最后一天的时间戳
   {
	    if($type==1)
	    {
            $t1 = $t."-01 00:00:00";
		}
		else
	    {
           $t1 = $t."-01";
		}
	    
		$t2 = "";
	    $arr = array('01','03','05','07','08','10','12');
        $tt = explode("-",$t);
		$year = $tt[0];
		$month = $tt[1];
		$fg = $this->yearisrun($year);
		if($fg)
	    {
            if($month=="02")
			{
				if($type==1)
				{
                  $t2 = $t."-29 23:59:59";
				}
				else
				{
                   $t2 = $t."-29";
				}
                
			}
			else
			{
				if(in_array($month,$arr))
				{
					if($type==1)
					{
                        $t2 = $t."-31 23:59:59";
					} 
					else
					{
                        $t2 = $t."-31";
					}
					
				}
				else
				{
					if($type==1)
					{
                        $t2 = $t."-30 23:59:59";
					}
					else
					{
                         $t2 = $t."-30";
					}
                    
				}
			}
		}
		else
	    {
             if($month=="02")
			{
				 if($type==1)
				 {
                     $t2 = $t."-28 23:59:59";
				 }
				 else
				 {
                     $t2 = $t."-28";
				 }
                
			}
			else
			{
				if(in_array($month,$arr))
				{
					if($type==1)
					{
                          $t2 = $t."-31 23:59:59";
					}
					else
					{
                        $t2 = $t."-31";
					}
					
				}
				else
				{
					if($type==1)
					{
                        $t2 = $t."-30 23:59:59";
					}
					else
					{
                       $t2 = $t."-30";
					}
                    
				}
			}
		}
		$dd = array();
		if($type==1)
	    {
            $dd['t1'] = strtotime($t1);
		   $dd['t2'] = strtotime($t2);
		}
		else
	    {
			$dd['t1'] = ($t1);
		    $dd['t2'] = ($t2);
		}
		
		return $dd;


   } 
   
	public function updateYear() //将今年加入年份
	{
		$y = date("Y");
		if(!in_array($y,$this->year))
		{
			 array_push($this->year,$y);
		}
	}

	

	public function getLmType() //获取联盟分类
	{
		$sql = "select id,name,is_show from lianmeng_type where is_show=1 order by id asc"; //查询分类
		$arr = $this->query($sql);
		foreach($arr as $k=>$v)
		{
			$id = $v['id'];
			$name = $v['name'];
			$this->lm_type[$id] = $name;
		}
	}

	function ts_deal($t=0)//0-2014特殊年  1-正常年（12月）  2-当年
	{
      if($t==0)
	  {
          $this->month = array('02','03','04','05','06','07','08','09','10','11','12');
	  }
	  else if($t==1)
      {
           $this->month = array('01','02','03','04','05','06','07','08','09','10','11','12');
	  }
	  else
	  {
		  $d = date("m") +0;
		  $this->month = array_slice($this->month,0,$d);
	  }
	
	}

	public function makeTimeArr() //构造时间数组
	{
        foreach($this->year as $k=>$v)
		{

			if($v=="2014")
			{
                $this->ts_deal(0);
				foreach($this->month as $mk=>$mv)
				{
					$hh = $v."-".$mv;
					if(in_array($hh,$this->d_time))
					{
                         continue;
					}
					else
					{ 
                        $this->d_time[] = $v."-".$mv;
					}
                    
				} 
				continue;
			}
			$dd = date('Y');
			if($dd==$v) //当年
			{
                 $this->ts_deal(2);
				 foreach($this->month as $mk=>$mv)
				{
					 $hh = $v."-".$mv;
					if(in_array($hh,$this->d_time))
					{
                        continue;
					}
					else
					{
                        $this->d_time[] = $v."-".$mv;
					}
                    
				} 
				continue;
			}
			$this->ts_deal(1);
			foreach($this->month as $mk=>$mv)
			{
				 $hh = $v."-".$mv;
					if(in_array($hh,$this->d_time))
				    {
                        continue;
					}
					else
				    {
                        $this->d_time[] = $v."-".$mv;
					}
                
			} 
		}
        
		$this->source_d_time = $this->d_time;
		//$this->d_time = ($this->d_time); 
	}


    //-------------------投资月统计-----------------------------
	public function makeCurrentQDTz() //构造当月渠道投资缓存
	{
        $current_month = date("Y-m");
		$data = $this->getOneMonthQdTzCount($current_month);
        $key = $this->prefix_cache['prefix_qd_tz'].":".$current_month;
		$this->setCache($key,$data,10800); // 当月保存三个小时
	}
    

	public function makeQDTz() //构造渠道投资的缓存
	{
        $current_month = date("Y-m");
        foreach($this->d_time as $dk=>$dv) //循环年
		{
            $data = $this->getOneMonthQdTzCount($dv);
            $key = $this->prefix_cache['prefix_qd_tz'].":".$dv;
			//var_dump($data);
			//echo "<hr>";
			//continue;
			if($dv==$current_month)
			{
                 $this->setCache($key,$data,10800); // 当月保存三个小时
			}
			else
			{
                $this->setCache($key,$data); //
			}
		}
	}

    public function getQDTz()//获取渠道数据
	{
		$darr = array();
		foreach($this->lm_type as $k=>$v)
		{
			$darr[$k] = array();
		}
        $current_month = date("Y-m");
		$this->d_time = array_reverse($this->d_time);

        foreach($this->d_time as $dk=>$dv)
		{
			$key = $this->prefix_cache['prefix_qd_tz'].":".$dv;
            $data = $this->getCache($key);
			if($data===false)
			{
               $data1 = $this->getOneMonthQdTzCount($dv);
			   if($dv==$current_month)
			  {
                 $this->setCache($key,$data1,10800); // 当月保存三个小时
			  }
			  else
			  {
                $this->setCache($key,$data1); //
			  }
			  $data = $data1;

			}
			if(empty($data))
			{ 
				foreach($this->lm_type as $yk=>$yv)
				{
                   $darr[$yk][$dv]=0;
					//array_push(,$)
				}
               
			}
			else
			{
				foreach($this->lm_type as $yk=>$yv)
				{
					if(array_key_exists($yk,$data))
					{
                        $darr[$yk][$dv] = $data[$yk]['u_count'];
					}
					else
					{
                          $darr[$yk][$dv] =  0;
					}
				}
                  
			} 
		}
        return $darr;
		//var_dump($darr);
		//	echo "<hr>";
	}
    
	//frist_invest_time
	public function getOneMonthQdTzCount($t='') //获取某个月渠道注册统计数据
	{  //$this->lm_type
	    $current_month = date("Y-m");
		$dd = $this->monthtotime($t,1);
		$t1 = $dd['t1'];
		$t2 = $dd['t2'];
		$data = array();
		//$sql ="select count(a.id) as cc,b.lianmeng_typeid as tid from statis_user_count a left join lianmeng_platform b on a.lianmeng_id=b.id where a.lianmeng_id>0 and a.frist_invest_time>=".$t1." and a.frist_invest_time<=".$t2." FROM_UNIXTIME(a.frist_invest_time,'%Y-%m')='{$t}' group by b.lianmeng_typeid order by b.lianmeng_typeid asc";
		$sql ="select count(a.id) as cc,b.lianmeng_typeid as tid from statis_user_count a left join lianmeng_platform b on a.lianmeng_id=b.id where a.lianmeng_id>0 and a.frist_invest_time>=".$t1." and a.frist_invest_time<=".$t2."  group by b.lianmeng_typeid order by b.lianmeng_typeid asc";
		//echo $sql."<br>";
		$arr = $this->query($sql);
		foreach($arr as $k=>$v)
		{
			  $cc = $v['cc'];
			  $tid = $v['tid'];
              $data[$tid]= array('tid'=>$tid,'u_count'=>$cc,'tname'=>(@($this->lm_type[$tid])==null?'':$this->lm_type[$tid]));
			  $this->total_qd_tz_count = $this->total_qd_tz_count+$cc;
			  if($current_month==$t)
			  {
                  $this->current_month_qd_tz_count = $this->current_month_qd_tz_count+$cc;
			  }
		}
		return $data;
	}
	//-------------------------------------------------------------
    
	//--------------渠道注册统计-------------------------------------
	public function makeCurrentQDReg() //构造当月的渠道注册缓存
	{
        $current_month = date("Y-m");
		$data = $this->getOneMonthQdRegCount($current_month);
        $key = $this->prefix_cache['prefix_qd_reg'].":".$current_month;
		$this->setCache($key,$data,10800); // 当月保存三个小时
	}
    //public  $prefix_cache = array('prefix_qd_reg'=>'qd_cate_reg')
	public function makeQDReg() //构造渠道注册缓存
	{
		$current_month = date("Y-m");
        foreach($this->d_time as $dk=>$dv) //循环年
		{
            $data = $this->getOneMonthQdRegCount($dv);
            $key = $this->prefix_cache['prefix_qd_reg'].":".$dv;
			//var_dump($data);
			//echo "<hr>";
			//continue;
			if($dv==$current_month)
			{
                 $this->setCache($key,$data,10800); // 当月保存三个小时
			}
			else
			{
                $this->setCache($key,$data); //
			}
		}

	}
    
    /*
	   $this->lm_type[$id] = $name;
	    格式：
		【渠道id】=>月份=>数量
	*/
	public function formatQdData($data,$dv) //格式化输出渠道数据
	{
		$darr = array();
        if(empty($data))
	   { 
				foreach($this->lm_type as $yk=>$yv)
				{
                   $darr[$yk][$dv]=0;
					//array_push(,$)
				}
               
		}
		else
		{
				foreach($this->lm_type as $yk=>$yv)
				{
					if(array_key_exists($yk,$data))
					{
                        $darr[$yk][$dv] = $data[$yk]['u_count'];
					}
					else
					{
                          $darr[$yk][$dv] =  0;
					}
				}
                  
		}
		return $darr;
	   
	}

	public function getTotalQdTz() //月度渠道投资总数
	{
         $darr = array();
		 $current_month = date("Y-m");
         $this->d_time = array_reverse($this->d_time);
		 foreach($this->d_time as $dk=>$dv)
		 {
             $key = $this->prefix_cache['prefix_qd_tz']."_QdTotal_".":".$dv;
			 $cc = $this->getCache($key);
			 if($cc===false)
			 {
                  $cc1 = $this->getTotalTzOneMonth($dv);
				  $darr[$dv] = $cc1;
				  if($current_month==$dv)
				  {
                     $this->setCache($key,$cc1,10800);
				  }
				  else
				 {    
                       $this->setCache($key,$cc1);
				  }

			 }
			 else
			 {
                 $darr[$dv] = $cc;
			 }
			 
		 }
		 return $darr;
	}
	public function getTotalTz() //月度投资总数
	{ 
          $darr = array();
		 $current_month = date("Y-m");
         $this->d_time = array_reverse($this->d_time);
		 foreach($this->d_time as $dk=>$dv)
		 {
             $key = $this->prefix_cache['prefix_qd_tz']."_Total_".":".$dv;
			 $cc = $this->getCache($key);
			 if($cc===false)
			 {
                  $cc1 = $this->getTotalTzOneMonth($dv,1);
				  $darr[$dv] = $cc1;
				  if($current_month==$dv)
				  {
                     $this->setCache($key,$cc1,10800);
				  }
				  else
				 {    
                       $this->setCache($key,$cc1);
				  }

			 }
			 else
			 {
                 $darr[$dv] = $cc;
			 }
			 
		 }
		 return $darr;
	}
    //frist_invest_time  lianmeng_id	
	public function getTotalTzOneMonth($t,$tty=0)
	{
		$w = "";
       if($tty==0)
	   {
           $w.=" and lianmeng_id>0";
	   }
	    $dd = $this->monthtotime($t,1);
		$t1 = $dd['t1'];
		$t2 = $dd['t2'];
	   $sql = "select count(id) as cc from statis_user_count where frist_invest_time>=".$t1." and frist_invest_time<=".$t2.$w;
	  // echo $sql."<br>";
	   $arr = $this->query($sql);
	   //var_dump($arr);
	   $cc = $arr[0]['cc'] +0;
	   return $cc;
	}

	public function getTotalQDReg() //渠道总数
	{
		 $darr = array();
		 $current_month = date("Y-m");
         $this->d_time = array_reverse($this->d_time);
		 foreach($this->d_time as $dk=>$dv)
		 {
			 $key = $this->prefix_cache['prefix_qd_reg']."_QdTotal_".":".$dv;
			 $cc = $this->getCache($key);
			 if($cc===false)
			 {
                 $cc1 = $this->getToatalQdRegOneMonth($dv);
				 $darr[$dv] = $cc1;
				 if($dv==$current_month)
			    {
                  $this->setCache($key,$cc1,10800);
			    }
			    else
			    {
                 $this->setCache($key,$cc1);
			    }
			 }
			 else
			 {
                 $darr[$dv] = $cc;
			 }
             
			 
			 
		 }
		 return $darr;
	}

	public function getTotalReg() //注册总计
	{
          $darr = array();
		 $current_month = date("Y-m");
         $this->d_time = array_reverse($this->d_time);
		 foreach($this->d_time as $dk=>$dv)
		 {
			 $key = $this->prefix_cache['prefix_qd_reg']."_Total_".":".$dv;
			 $cc = $this->getCache($key);
			 if($cc===false)
			 {
                 $cc1 = $this->getToatalQdRegOneMonth($dv,1);
				  $darr[$dv] = $cc1;
				 if($dv==$current_month)
			    {
                  $this->setCache($key,$cc1,10800);
			    }
			    else
			    {
                 $this->setCache($key,$cc1);
			    }
			 }
			 else
			 {
                 $darr[$dv] = $cc;
			 }
             
			 
			 
		 }
		 return $darr;
	}



	public function getToatalQdRegOneMonth($t,$tty=0)
	{
		$w = "";
		if($tty==0)
		{
             $w.=" and lianmeng_id>0";
		}
        $dd = $this->monthtotime($t,1);
		$t1 = $dd['t1'];
		$t2 = $dd['t2'];
		$sql = "select count(uid) as  cc from user where regist_time>=".$t1." and regist_time<=".$t2.$w;
		//echo $sql."<br>";
		$arr = $this->query($sql);
		$cc = $arr[0]['cc']+0;
		return $cc;
	}

	public function getAuthCount()
	{
         $darr = array();
		 $current_month = date("Y-m");
         $this->d_time = array_reverse($this->d_time);
		 foreach($this->d_time as $dk=>$dv)
		 {
			 $key = $this->prefix_cache['prefix_auth_count'].":".$dv;
			 $cc = $this->getCache($key);
			 if($cc===false)
			 {
                 $cc1 = $this->getOneMonthAuthCount($dv);
				  $darr[$dv] = $cc1;
				 if($dv==$current_month)
			    {
                  $this->setCache($key,$cc1,10800);
			    }
			    else
			    {
                 $this->setCache($key,$cc1);
			    }
			 }
			 else
			 {
                 $darr[$dv] = $cc;
			 }
             
			 
			 
		 }
		 return $darr;
	}

	public function getOneMonthAuthCount($t)
	{
		 $dd = $this->monthtotime($t,1);
		 $t1 = $dd['t1'];
		 $t2 = $dd['t2'];
		 $sql = "select count(id) as cc from statis_user_count where authentication_time>=".$t1." and authentication_time<=".$t2;
		 $arr = $this->query($sql);
		 $cc = $arr[0]['cc']+0;
		 return $cc;

	}
    
	/*
	     $this->total_qd_reg_count = $this->total_qd_reg_count+$cc;
			  if($current_month==$t)
			  {
                  $this->current_month_qd_reg_count 
	*/
	public function getQDReg()//获取渠道数据
	{
		$darr = array();
		foreach($this->lm_type as $k=>$v)
		{
			$darr[$k] = array();
		}
        $current_month = date("Y-m");
		$this->d_time = array_reverse($this->d_time);
		//var_dump($this->cfg);
        foreach($this->d_time as $dk=>$dv)
		{
			$key = $this->prefix_cache['prefix_qd_reg'].":".$dv;
			//echo $key;
            $data = $this->getCache($key);
			//var_dump($data);echo "<br>";
			if($data===false)
			{
				//echo $dv." no cache"."<br>";
                  $data1 = $this->getOneMonthQdRegCount($dv);
                  //$key = $this->prefix_cache['prefix_qd_reg'].":".$dv;
				  if($dv==$current_month)
			      {
					 // S($key,$data1,10800);
					 // var_dump(S($key));
                     $this->setCache($key,$data1,10800); // 当月保存三个小时
			      }
			      else
			      {
					  //S($key,$data1);
					  //var_dump(S($key));
                      $this->setCache($key,$data1); //
					  //var_dump($this->getCache($key));
			      }
				  $data = $data1;
			}
			if(empty($data))
			{ 
				foreach($this->lm_type as $yk=>$yv)
				{
                   $darr[$yk][$dv]=0;
					//array_push(,$)
				}
               
			}
			else
			{
				foreach($this->lm_type as $yk=>$yv)
				{
					if(array_key_exists($yk,$data))
					{
                        $darr[$yk][$dv] = $data[$yk]['u_count'];
					}
					else
					{
                          $darr[$yk][$dv] =  0;
					}
				}
                  
			}
			
		}
		//var_dump($darr);
			//echo "<hr>";
		return $darr;
	}





	public function getOneMonthQdRegCount($t='') //获取某个月渠道注册统计数据
	{  //$this->lm_type
	    $current_month = date("Y-m");
		$dd = $this->monthtotime($t,1);
		//var_dump($dd);echo "<br>";
		$t1 = $dd['t1'];
		//var_dump(date("Y-m-d H:i:s",$t1));echo "<br>";
		$t2 = $dd['t2'];
		//var_dump(date("Y-m-d H:i:s",$t2));echo "<br>";
		$data = array();
        //$sql = "select count(a.uid) as cc,b.lianmeng_typeid as tid from user a left join lianmeng_platform b on a.lianmeng_id=b.id where a.lianmeng_id>0 and FROM_UNIXTIME(a.regist_time,'%Y-%m')='{$t}' group by b.lianmeng_typeid order by b.lianmeng_typeid asc";
		$sql = "select count(a.uid) as cc,b.lianmeng_typeid as tid from user a left join lianmeng_platform b on a.lianmeng_id=b.id where a.lianmeng_id>0 and a.regist_time>=".$t1." and a.regist_time<=".$t2."  group by b.lianmeng_typeid order by b.lianmeng_typeid asc";
			//echo $sql."<br>";return;
		$arr = $this->query($sql);
		foreach($arr as $k=>$v)
		{
			  $cc = $v['cc'];
			  $tid = $v['tid'];
              $data[$tid]= array('tid'=>$tid,'u_count'=>$cc,'tname'=>(empty($this->lm_type[$tid])?$tid:$this->lm_type[$tid]));
			  $this->total_qd_reg_count[$t] = $this->total_qd_reg_count[$t]+$cc;
			  if($current_month==$t)
			  {
                  $this->current_month_qd_reg_count[$t] = $this->current_month_qd_reg_count[$t]+$cc;
			  }
		}
		if(empty($data))
		{
			 $this->total_qd_reg_count[$t] = 0;
			 if($current_month==$t)
			 {
                $this->current_month_qd_reg_count[$t] = 0;
			 }
		}

		return $data;
	}
	//---------------------------------------------------------------

	public function setRefreshCacheFg($fg=0) //设置是否强刷缓存数据
	{
         $this->refresh_cache_fg = $fg;
	}

	 //获取缓存数据
	function getCache($key)
	{
      if($this->refresh_cache_fg==1) return false;
	  $data = S($key);
	  return $data;

	}

	function setCache($key,$val,$expire=31536000) //设置缓存
	{
		//var_dump($this->cfg);
        if($this->cfg==1) //开启缓存
		{
            S($key,$val,$expire);
		}
		else
		{
            return;
		}
	}

	function getsourcetime()
	{
		$this->d_time = $this->source_d_time;
	}
   


	 

}