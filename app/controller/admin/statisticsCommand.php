<?php
/**
 * 后台统计
 * User: wangmengmeng
 * Date: 2016/7/6
 * Time: 10:07
 */
//if(!IS_CLI) exit('error');
class controller_admin_statisticsCommand extends controller_admin_abstract{
    private static $expenseCollectTypeDay = 0;
    private static $expenseCollectTypeWeek = 1;
    private static $expenseCollectTypeMonth = 2;
    /*消费日统计
     *今天跑前天的数据
     *
     */
    public function actionExpenseDay() {
        try {
            $date = date("Y-m-d", strtotime('-2 day'));
            //$list = M('expense') -> where("start_time >= " . strtotime($date) . " AND start_time <= " . (strtotime($date) + 24 * 3600 - 1)) -> findAll();
            $model = new Model();
            $sql = "SELECT sort_client_id, code, SUM(`amount`) as `amount`, COUNT(*) as `degreen` FROM `expense` where start_time >= " . strtotime($date) . " AND start_time < " . (strtotime($date) + 24 * 3600) . " group by sort_client_id";
            $list = $model->query($sql);
            $statType = self::$expenseCollectTypeDay;
            array_walk($list, function (&$value, $key) use ($date, $statType) {
                $value['add_time'] = time();
                $value['topdate'] = $date;
                $value['type'] = $statType;
                return $value;
            });
            if (!empty($list) && is_array($list)) {
                M('expense_collect')->addAll($list);
            }
        }catch (Exception $e) {
            $e->getMessage();
        }
    }
    /*消费周统计
    *这周跑上周的数据(每周一跑)
    */
    public function actionExpenseWeek() {
        if (date('w') != 1) {
            exit("date is faild");
        }
        try {
            $date = date("Y-m-d", strtotime('-7 day'));
            $model = new Model();
            $sql = "SELECT sort_client_id, code, SUM(`amount`) as `amount`, COUNT(*) as `degreen` FROM `expense` where start_time >= " . strtotime($date) . " AND start_time < " . (strtotime($date) + 24 * 3600 * 7) . " group by sort_client_id";
            $list = $model->query($sql);
            $statType = self::$expenseCollectTypeWeek;
            $lastDay = date("Y-m-d", strtotime("+6 days", strtotime($date)));//周日
            $lastDay = $date;//周一
            if(date("w", strtotime($lastDay)) != 1) {
                exit("date is faild");
            }
            array_walk($list, function (&$value, $key) use ($lastDay, $statType) {
                $value['add_time'] = time();
                $value['topdate'] = $lastDay;
                $value['type'] = $statType;
                return $value;
            });
            if (!empty($list) && is_array($list)) {
                M('expense_collect')->addAll($list);
            }
        }catch (Exception $e) {
            $e->getMessage();
        }
    }
    /*消费月统计
    *这月跑上月的数据(每月一号跑此脚本)
    */
    public function actionExpenseMonth() {
        if(date("d") != 1) {
            exit("date is faild");
        }
        try {
            $date = date("Y-m-d", strtotime('-1 month', strtotime(date("Y-m-d"))));
            $model = new Model();
            $sql = "SELECT sort_client_id, code, SUM(`amount`) as `amount`, COUNT(*) as `degreen` FROM `expense` where start_time >= " . strtotime($date) . " AND start_time < " . (strtotime('+1 month', strtotime($date))) . " group by sort_client_id";
            $list = $model->query($sql);
            $statType = self::$expenseCollectTypeMonth;
            $monthDay = date("Y-m-d", strtotime($date));
            if(date("d", strtotime($monthDay)) != 1) {
                exit("date is faild");
            }
            array_walk($list, function (&$value, $key) use ($monthDay, $statType) {
                $value['add_time'] = time();
                $value['topdate'] = $monthDay;
                $value['type'] = $statType;
                return $value;
            });
            if (!empty($list) && is_array($list)) {
                M('expense_collect')->addAll($list);
            }
        }catch (Exception $e) {
            $e->getMessage();
        }
    }
    /*
     * 根据客户端最后更新时间离线在线状态定时更新
     *  @author tianxiang
     */
    public function actionClientOnlineStatus() {
          $mapA = array("status"=>1,"online_status"=>1);
          $resultB = M("sortware_client")->where($mapA)->field("id")->findAll();
          $now = time();
          $i=0;
          if(!empty($resultB)){
              foreach ($resultB as $key => $value) {
                  $id= $value['id'];
                  $mapB['cid'] = $id;
                  $mapB['status'] = 1;
                  $row = M("sortware_client_queryconfig")->where($mapB)->field("qu_type,qu_frequency")->order("qu_type desc")->find();
                  $last_online_time = $value['last_online_time'];
                  $qu_type = $row['qu_type'];
                  
                  $qu_frequency = $row['qu_frequency'];
                  //如果选择分钟
                 if ($qu_type == 1) {
                    $second = 1 * 60 * $qu_frequency;
                 }
                 //选择小时
                 else if ($qu_type == 2) {
                     $second = 1 * 60 * 60;
                 }
                 //选择天
                 else if ($qu_type == 3) {
                     $second = 1 * 60 * 60 * 24;
                 } 
                 //选择周
                 else if ($qu_type == 4) {
                     $second = 1 * 60 * 60 * 24 * 7;
                 } 
                 //选择月
                 else if ($qu_type == 5) {
                     $second = 1 * 60 * 60 * 24 * 7 * 30;
                 }
                 
                 $diff = $now - $last_online_time;
                 if($diff > $second){
                      $i++;
                      M('sortware_client')->where(array("id"=>$id))->save(array("online_status"=>0));
                 } 
             }
          } 
          
    }
     //客户端在线状态统计
    public function actionClientStatus() {
          $date = date("Y-m-d", strtotime('-1 day'));
          $start_timeA = array(array('egt',strtotime($date." 00:00:00")),array('elt',strtotime($date." 23:59:59")),"and");
          $mapA['start_time'] = $start_timeA;
         // $mapC['c.status'] = 1;
          $resultA = M("expense")->where($mapA)->field("distinct sort_client_id")->group("sort_client_id")->findAll();
          
          $sql = "SELECT c.id as sort_client_id   FROM sortware_client c
LEFT JOIN  (SELECT sort_client_id FROM expense e WHERE  e.start_time >= ".strtotime($date." 00:00:00")."
    AND e.start_time <= ".strtotime($date." 23:59:59").")  b ON c.id =b.sort_client_id  WHERE  c.status=1 and b.sort_client_id IS NULL";
           $resultB = M()->query($sql);
          
          /*$start_timeB = array(array('lt',strtotime($date." 00:00:00")),array('gt',strtotime($date." 23:59:59")),"and");
          $mapB['last_online_time'] = $start_timeB;
          $mapB['status'] = 1;
          $resultB = M("sortware_client c")->where($mapB)->field("c.id as sort_client_id")->findAll();
         */
          if(!empty($resultA)){
              $i=0;
              foreach ($resultA as $key => $value) {
                  //查该天失败查询次数
                  unset($data);
                  $data['sort_client_id'] = $sort_client_id = $value['sort_client_id'];
                  
                  $mapC['sort_client_id'] = $sort_client_id;
                  $mapC['start_time'] = $start_timeA;
                  $mapC['amount'] = array("eq",0);
                  $data['fail_num'] = M("expense")->where($mapC)->count();
                  //查该天成功查询次数
                  $mapD['sort_client_id'] = $sort_client_id;
                  $mapD['start_time'] = $start_timeA;
                  $mapD['amount']  = array("neq",0);
                  $data['success_num'] = M("expense")->where($mapD)->count();
                  $data['thedate'] = $date;
                  $data['add_time'] = time();
                  $id = M("sortware_client_status_statistics")->add($data);
                  if($id){
                      $i++;
                  }
              }
            echo "客户端离在线状态".$i."个更新完毕<br/>";
          }
          //考虑客户端停了的情况,记入失败
          if(!empty($resultB)){
              foreach ($resultB as $key => $value) {
                  //查该天失败查询次数
                  unset($data);
                  $data['sort_client_id'] = $sort_client_id = $value['sort_client_id'];
                  $result = M("sortware_client_queryconfig q")->where(array("qu_type"=>1,"cid"=>$sort_client_id))->field("q.qu_num,q.qu_frequency")->find();
                  $qu_frequency = $result['qu_frequency'];
                  $num = intval((3600*24) / ($qu_frequency * 60));
                  $data['success_num'] =0;
                  $data['thedate'] = $date;
                  $data['fail_num'] = $num;
                  $id = M("sortware_client_status_statistics")->add($data);
                  if($id){
                      $i++;
                  }
              }  
          }
          echo $date."在线状态".$i."个客户端统计完毕<br/>";
          $this->ClientCounts($date);
         
          
    }
    
     //客户端在线个数统计
    public function clientCounts($date) { 
          $mapA['thedate'] = $date;
          $mapA['success_num'] = array("neq",0);
          $data['online_num'] = M("sortware_client_status_statistics")->where($mapA)->count();
          
          $client_total = M("sortware_client")->where(array("status"=>1))->count();
          $data['offline_num'] = $client_total - $data['online_num'];
          $data['thedate'] = $date;
          $id = M("sortware_client_online_statistics")->add($data);
          echo $date."客户端在线个数统计完毕";  
    }
}
?>