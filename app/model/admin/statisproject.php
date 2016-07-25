<?php
/**
 * 根据项目统计用户信息
 * -------------------------------------------------------------------------------------------
 * 功能描述
 * -------------------------------------------------------------------------------------------
 * Date: 2016/3/16
 * @author shazhiyu
 *   
 */

class model_admin_StatisProject extends model_abstruct
{

	 private $start_time = 0;
	 private $end_time = 0 ;
	 private $parr = array(); //存储项目信息
	 private $pid = 0; //项目id
	 public $ret = array(); //返回数据信息

	 public function setparam($pid=0) //设置参数信息
	 { 
	 	$pid +=0;
	 	$this->pid = $pid;
	 }

	 private function getwhere($field,$t1,$t2) //构造查询条件
    {
        $w = "";
        if($t1>0&&$t2>0)
        {
        	$w .=" and ".$field.">=".$t1." and ".$field."<=".$t2;
        }
        return $w; 
    }

	 private function findproject() 
	 {
	 	  $id = $this->pid;

          //$sql = "select hits_count,status,preheat_begin_time,amount_end_time,amount_begin_time,finsh_time,fail_time from project where id=".$id." and (status=2 or status=4 or status=5 or status=6 or status=8)";
		  $sql = "select hits_count,status,preheat_begin_time,amount_end_time,amount_begin_time,finsh_time,fail_time from project where id=".$id." and (status=2 or status=4 or status=5 or status=6 or status=7)";
          //echo $sql;
          $arr = $this->query($sql);
          //var_dump($arr);
          if(!empty($arr))
          {
          	  $this->parr['hit_count'] = $arr[0]['hits_count'];// 点击次数
    		  $this->parr['status'] = $arr[0]['status'];// 项目状态 2-预热 4，融资中；5，融资失败；6，融资完成 8-结项
    		  $this->parr['preheat_begin_time'] = $arr[0]['preheat_begin_time'];// 预热开始时间
    		  $this->parr['amount_end_time'] = $arr[0]['amount_end_time'];// 融资计划结束时间
    		  $this->parr['amount_begin_time'] = $arr[0]['amount_begin_time'];// 融资开始时间
    		  $this->parr['finsh_time'] = $arr[0]['finsh_time'];// 融资结束
			  $this->parr['fail_time'] = $arr[0]['fail_time'];
			  
          }
          else
          {
          	 $this->parr = array();
          }
	 }

	 public function statis() //统计信息
	 {
          $this->findproject(); //获取项目信息
          if(empty($this->parr))
          {
          	 $this->ret['st']=0;
          	 return;
          }
         // echo "bbb";
          $this->ret['st']=1;
          $this->ret['dt'] = array();
          $this->gethitcount(); //点击量  hit_count
          //var_dump($this->ret);
          $this->getregcount(); //reg_num
          $this->getyycount(); /*
           $this->ret['dt']['yy_num'] = $total; //预约总人数
        $this->ret['dt']['yy_c_num'] = $c_num; //预约中充值人数
        $this->ret['dt']['yy_t_num'] = $t_num; //预约总投资
        $this->ret['dt']['yy_s_num'] = $s_num; //首次预约人数*/
          $this->getrgcount();
        /*
         $this->ret['dt']['rg_num'] = $total;
        $this->ret['dt']['rg_f_num'] = $f_num;
        */
        $this->getrzspeed();//speed
        $this->getregcountavg();   //$this->ret['dt']['avg_reg_num'] = 0; 每天新注册人数
		$this->getyycountavg();//$this->ret['dt']['avg_yy_num'] 每天平均预约
		$this->getyycountreg(); 
		/*
		     $this->ret['dt']['yy_r_num'] = 0; //预约中注册的人
              $this->ret['dt']['avg_yy_r_num'] = 0; //每天新注册

	        $this->ret['dt']['avg_rg_num'] = $total; //每天认购人数
					 $this->ret['dt']['avg_rg_f_num'] = $f_num; //每天首次认购
		*/

        return $this->ret;
	 }

	 private function gettime() //计算时间段 
	 {
            $this->start_time = $this->parr['preheat_begin_time'];
			$st = $this->parr['status'];//2-预热  4-融资中  5-融资失败  6-融资成功   8-结项
            $t2 = $this->parr['finsh_time']; //融资结束时间
		    if($st==5) //融资失败 -取失败时间
		    {
                  $this->end_time = $this->parr['fail_time']; //失败时间
			}
			else
		    {
                   if($t2>0)
				   {
						$this->end_time = $t2;
				   }
				   else
				   {
            	        $this->end_time = $this->parr['amount_end_time']; //融资计划结束时间
				    }
			}
            
	 }

	 private function getntime()//新的方式计量时间段
	 {
		 $this->start_time = $this->parr['preheat_begin_time']; //开始时间不变
         $st = $this->parr['status'];//2-预热  4-融资中  5-融资失败  6-融资成功   8-结项
		 if($st==2||$st==4) //处于预热和融资中的，取当前时间
		 {
			 $this->end_time = time(); 
		 }
		 //if($st==6||$st==8) //成功的时候  取融资成功的时间
		 if($st==6||$st==7) //成功的时候  取融资成功的时间
		 {
			 $this->end_time = $this->parr['finsh_time'];
		 }
		 if($st==5)  //融资失败  取融资失败时间
		 {
			  $this->end_time = $this->parr['fail_time'];
             
		 }
	 }

	 private function gethitcount() //获取点击信息
	 {
            $this->ret['dt']['hit_count'] = empty($this->parr['hit_count'])?0:$this->parr['hit_count'];
	 }

	 private function getregcount() //注册统计
	 {
	 	  $this->gettime();
	 	  $cc = 0;
	 	  if($this->start_time==0||$this->end_time==0)
	 	  {
			$this->ret['dt']['reg_num'] = $cc;
	 	  	return $cc;
	 	  }
	 	  $w = $this->getwhere('regist_time',$this->start_time,$this->end_time);
          $sql = "select count(uid) as cc from user where 1 ".$w;
          $arr = $this->query($sql);
          if(!empty($arr))
          {
          	 $cc = $arr[0]['cc'];
          }
          $this->ret['dt']['reg_num'] = $cc;
          //returnn $cc;
	 }

	 private function getregcountavg() //获取每天点击人数
	 {
           $this->getntime();//判断开始时间  结束时间
		   if($this->start_time==0||$this->end_time==0)
	 	   {
			  $this->ret['dt']['avg_reg_num'] = 0;
	 	  	  return;
	 	   }
		   $cc = 0;
		   $w = $this->getwhere('regist_time',$this->start_time,$this->end_time);
		   $sql = "select count(uid) as cc from user where 1 ".$w;
           $arr = $this->query($sql);
           if(!empty($arr))
           {
          	 $cc = $arr[0]['cc'];
           }
		   $second = $this->end_time - $this->start_time;
           $d = self::second2day($second);
		   if($d>0)
		   {
			   $avg = $cc/$d;
			   $avg = round($avg,1);
			   $this->ret['dt']['avg_reg_num'] = $avg;
		   }
		   else
		   {
               $this->ret['dt']['avg_reg_num'] = 0;
		   }
           //$this->ret['dt']['reg_num'] = $cc;
	 }
     
	 private function getyycountavg() //获取每天平均预约人数
	 {
         $t1 =  $this->parr['preheat_begin_time'];
		 $t2 =$this->parr['amount_begin_time']; 
		 $second = $t2-$t1;
		 $sql = "select count(id) as cc from project_investment_pre where pid=".$this->pid;
         $r1 = $this->query($sql);
         $total = $r1[0]['cc']; //总的预约人数
		 if($second>0)
		 {
                
                $d = self::second2day($second);
				if($d>0)
			    { 
                     $avg = $total/$d;
			         $avg = round($avg,1);
					 $this->ret['dt']['avg_yy_num'] = $avg;
				}
				else
			    {
                     $this->ret['dt']['avg_yy_num'] = $total;
				}
		 }
		 else if($second==0)
		 {
            $this->ret['dt']['avg_yy_num'] = $total;
		 }
		 else
		 {
             $this->ret['dt']['avg_yy_num'] = 0; //容错处理
		 }

	 }

	 private function getyycountreg() //预约中的新注册人数  regist_time
	 {
          $this->gettime(); //计算时间段
		  if($this->start_time==0||$this->end_time==0)
		  {
			  $this->ret['dt']['yy_r_num'] = 0; //预约中注册的人
              $this->ret['dt']['avg_yy_r_num'] = 0; //每天新注册
			  return;
		  } 
		  $sql = "select count(a.id) as cc from project_investment_pre a left join user b on a.uid=b.uid where a.pid=".$this->pid." and b.regist_time>=".$this->start_time." and b.regist_time<=".$this->end_time;
		  $r = $this->query($sql);
		  $cc = $r[0]['cc'];
		  $this->ret['dt']['yy_r_num'] = $cc;
		  $t1 =  $this->parr['preheat_begin_time'];
		  $t2 =$this->parr['amount_begin_time']; 
		  $second = $t2-$t1;
		  if($second>0)
		  {
                $d = self::second2day($second);
				if($d>0)
			    { 
                     $avg = $cc/$d;
			         $avg = round($avg,1);
					 $this->ret['dt']['avg_yy_r_num'] = $avg;
				}
				else
			    {
                     $this->ret['dt']['avg_yy_r_num'] = $cc;
				}
		  }
		  else if($second==0)
		  {
              $this->ret['dt']['avg_yy_r_num'] = $cc;
		  }
		  else
		  {
              $this->ret['dt']['avg_yy_r_num'] = 0;
		  }



	 }
     //authentication_time --认证  frist_recharge_time--充值  frist_invest_time--投资 frist_predict_time-第一次预约
	 private function getyycount() //获取预约人数、预约中认证、充值、认购的人数
	 { //select a.pid,a.uid,b.frist_recharge_time from project_investment_pre a left join statis_user_count b on a.uid=b.uid where a.pid=142;
	    $this->gettime(); //计算时间段
		
        $sql = "select count(id) as cc from project_investment_pre where pid=".$this->pid;
        $r1 = $this->query($sql);
        $total = $r1[0]['cc']; //总的预约人数

		if($this->start_time==0||$this->end_time==0)
	 	 {
			$this->ret['dt']['yy_num'] = $total; //预约总人数
            $this->ret['dt']['yy_c_num'] = 0; //预约中充值人数
            $this->ret['dt']['yy_t_num'] = 0; //预约总投资
            $this->ret['dt']['yy_a_num'] = 0; //预约总投资
            $this->ret['dt']['yy_s_num'] = 0; //首次预约人数
	 	  	return ;
	 	}
        $fsql = "select count(a.id) as cc
 from project_investment_pre a left join statis_user_count b on a.uid=b.uid where a.pid=".$this->pid." and b.frist_recharge_time>=".$this->start_time." and b.frist_recharge_time<=".$this->end_time."  union all select count(a.id) as cc
 from project_investment_pre a left join statis_user_count b on a.uid=b.uid where a.pid=".$this->pid." and b.frist_invest_time>=".$this->start_time." and b.frist_invest_time<=".$this->end_time."  union all select count(a.id) as cc
 from project_investment_pre a left join statis_user_count b on a.uid=b.uid where a.pid=".$this->pid." and b.authentication_time>=".$this->start_time." and b.authentication_time<=".$this->end_time."  union all select count(a.id) as cc
 from project_investment_pre a left join statis_user_count b on a.uid=b.uid where a.pid=".$this->pid." and b.frist_predict_time>=".$this->start_time." and b.frist_predict_time<=".$this->end_time.";";
        $r2 = $this->query($fsql);
        $c_num = $r2[0]['cc']; //充值
        $t_num = $r2[1]['cc']; //投资
        $a_num = $r2[2]['cc']; //认证
        $s_num = $r2[3]['cc']; //认证
        $this->ret['dt']['yy_num'] = $total; //预约总人数
        $this->ret['dt']['yy_c_num'] = $c_num; //预约中充值人数
        $this->ret['dt']['yy_t_num'] = $t_num; //预约总投资
        $this->ret['dt']['yy_a_num'] = $a_num; //预约总投资
        $this->ret['dt']['yy_s_num'] = $s_num; //首次预约人数
	 }

	 private function getrgcount() //投资统计
	 {
		 $this->gettime(); 
        $sql = "select count(DISTINCT(uid)) as cc from project_investment where pid=".$this->pid ." and `status` in(1,3)";
        $r1 = $this->query($sql);
        if(empty($r1))
        {
        	 $total = 0;
        }
        else
        {
        	 $total = $r1[0]['cc']; //总的投资人数
        }
        
        if($this->start_time==0||$this->end_time==0)
	 	 {
			$this->ret['dt']['rg_num'] = $total;
            $this->ret['dt']['rg_f_num'] = 0;

			 $this->ret['dt']['avg_rg_num'] = 0;
			 $this->ret['dt']['avg_rg_f_num'] = 0;
	 	  	return ;
	 	}
        $fsql = "select count(DISTINCT(a.id)) as cc
 from project_investment a left join statis_user_count b on a.uid=b.uid where a.pid=".$this->pid."  and a.`status` in(1,3) and b.frist_invest_time>=".$this->start_time." and b.frist_invest_time<=".$this->end_time;
        $r2 = $this->query($fsql);
        $f_num = $r2[0]['cc'];
        $this->ret['dt']['rg_num'] = $total;
        $this->ret['dt']['rg_f_num'] = $f_num;

		$this->getntime(); //重新获取天数
		$second = $this->end_time - $this->start_time;
		if($second>0)
		{
                $d = self::second2day($second);
				if($d>0)
			    { 
                     $avg1 = $total/$d;
			         $avg1 = round($avg1,1);
					 $this->ret['dt']['avg_rg_num'] = $avg1;
					 $avg2 = $f_num/$d;
			         $avg2 = round($avg2,1);
					 $this->ret['dt']['avg_rg_f_num'] = $avg2;
				}
				else
			    {
                     $this->ret['dt']['avg_rg_num'] = $total; //每天认购人数
					 $this->ret['dt']['avg_rg_f_num'] = $f_num; //每天首次认购
				}
		}
		else if($second==0)
		{
              $this->ret['dt']['avg_rg_num'] = $total; 
			  $this->ret['dt']['avg_rg_f_num'] = $f_num;
		}
		else //容错
		{
              $this->ret['dt']['avg_rg_num'] = 0; 
			  $this->ret['dt']['avg_rg_f_num'] = 0;
		}


	 }
     
     //// 项目状态 2-预热 4，融资中；5，融资失败；6，融资完成 8-结项
	 private function getrzspeed() //获取融资速度
	 { 
          $st = $this->parr['status'];
          //if($st==6||$st==8)
		  if($st==6||$st==7)
          {
          	 $s = "";
          	 $tt = $this->parr['finsh_time'] - $this->parr['amount_begin_time'];
			 /*
          	 $day = floor($tt/(24*3600));
          	 if($day>=1)
          	 {
          	 	$s.=$day."天";
          	 }
          	 $hour = floor(($tt-($day*24*3600))/3600);
          	 if($hour>=1)
          	 {
          	 	$s.=$hour."小时";
          	 }
          	 $min = floor(($tt-($day*24*3600)-($hour*3600))/60);
          	 if($min>=1)
          	 {
                 $s.=$min."分";
          	 }
          	 $send = $tt-($day*24*3600)-($hour*3600)-($min*60);
          	 if($send>0)
          	 {
          	 	$s.=$send."秒";
          	 }*/
          	 $this->ret['dt']['speed'] = self::second2date($tt);
          }
          else
          {
          	  $this->ret['dt']['speed'] = 0;
          }
	 }
     static public function second2day($second)
	 {
		     $tt = $second+0;
			 if($tt==0)
		     {
                return false;
			 } 
			 $day = floor($tt/(24*3600));
			 if($day>=1)
		     {
				 return $day;
			 }
			 else
		     {
				 return 0;
			 }

	 }
	 static public  function second2date($second) //传入秒   输出 天 时 分 秒
	 {
		     $s = "";
		     $tt = $second+0;
			 if($tt==0)
		     {
                $s = 0;
				return $s;
			 } 
             $day = floor($tt/(24*3600));
          	 if($day>=1)
          	 {
          	 	$s.=$day."天";
          	 }
          	 $hour = floor(($tt-($day*24*3600))/3600);
          	 if($hour>=1)
          	 {
          	 	$s.=$hour."小时";
          	 }
          	 $min = floor(($tt-($day*24*3600)-($hour*3600))/60);
          	 if($min>=1)
          	 {
                 $s.=$min."分";
          	 }
          	 $send = $tt-($day*24*3600)-($hour*3600)-($min*60);
          	 if($send>0)
          	 {
          	 	$s.=$send."秒";
          	 }
			 return $s;
	 }



	 
    
}