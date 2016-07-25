<?php

class helper_calendar{
    
/**
 * 按月统计用户的投标数
* @param int $user_id 用户ID
* @param string $date 日期 | 格式2013-01
* @return array
*/
    static public function getUserTenderCalendarCountList($date = '') {
        if (empty($date) || !preg_match('/^[0-9]{4}-[0-9]{2}$/is', $date)) return array();
    
        $t1 = strtotime($date.'-01 00:00:00');
        $t2 = strtotime('+1 month', $t1) - 1;
        $res = array();
    
        $year = date('Y',$t1); //年
        $month = date('n',$t1); //月
        $week = date('w',$t1); //当月1号星期几
    
        //补足1号前面的几天空白
        $days = array();
        for($i = 0;$i < $week; $i++){
            $days[] = array('year' => $year, 'month' => $month, 'day' => '', 'borrow_count' => 0, 'now' => 0);
        }
    
        //生成当前月份日历
        $end_day = date('d', $t2); //当月最后一天是几号
        for ($i = 1; $i <= $end_day; $i++) {
            $now = $year.$month.$i == date('Ynj') ? 1 : 0;
            if(isset($arr[$i])){
                $days[] = array('year' => $year, 'month' => $month, 'day' => $i, 'borrow_count' => $arr[$i], 'now' => $now);
            }else{
                $days[] = array('year' => $year, 'month' => $month, 'day' => $i, 'borrow_count' => 0, 'now' => $now);
            }
        }
    
        if(!empty($days) && is_array($days)){
            foreach ($days as $key=>$value){
                $value = empty($res['lists'][$value['day']])?$value:array_merge($value,$res['lists'][$value['day']]);
                $days[$key] = $value;
            }
        }
    
        $n = count($days);
        for($i=$n; $i<42; $i++){
            $days[] = array('year' => $year, 'month' => $month, 'day' => '', 'borrow_count' => 0, 'now' => 0);
        }
    
        $res['calendar'] = $days;
        $res['date'] = $date;
        $res['last_date'] = date('Y-m',strtotime('-1 month', strtotime($date)));
        $res['next_date'] = date('Y-m',strtotime('+1 month', strtotime($date)));
        $res['date2'] = date('Y年n月',  strtotime($date));
        return $res;
    }
    
    /*
     *function：计算两个日期相隔多少年，多少月，多少天
     *param string $date1[格式如：2011-11-5]
     *param string $date2[格式如：2012-12-01]
     *return array array('年','月','日');
     */
    static function diffDate($date1,$date2){
        if(strtotime($date1)>strtotime($date2)){
            $tmp=$date2;
            $date2=$date1;
            $date1=$tmp;
        }
        list($Y1,$m1,$d1)=explode('-',$date1);
        list($Y2,$m2,$d2)=explode('-',$date2);
        $Y=$Y2-$Y1;
        $m=$m2-$m1;
        $d=$d2-$d1;
        if($d<0){
            $d+=(int)date('t',strtotime("-1 month $date2"));
            $m--;
        }
        if($m<0){
            $m+=12;
            $y--;
        }
        return array('year'=>$Y,'month'=>$m,'day'=>$d);
    }
}