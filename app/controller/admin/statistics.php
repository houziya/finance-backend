<?php

/**
 * 后台统计
 * User: wangmengmeng
 * Date: 2016/7/6
 * Time: 10:07
 */
class controller_admin_statistics extends controller_admin_abstract {
    private static $statCycel = array(0 => "消费日统计", 1 => "消费周统计", 2 => "消费月统计");
    private static $statYTitle = array(0 => "30日内趋势图", 1 => "30周内趋势图", 2 => "30月内趋势图");
    /*
     * 消费统计
     * @author wangmengmeng
     */
    public function actionExpense() {
        //搜索参数处理
        $map = $this->_search(array('sortware', 'sortwareClient', 'expenseCollect'));
        if (trim($this->_get('start_time'))) {
            $map['e.topdate'] = array("EGT", trim($this->_get('start_time')));
        }
        if (trim($this->_get('end_time'))) {
            if ($map['e.topdate']) {
                $map['e.topdate'] = array($map['e.topdate'], array("ELT", trim($this->_get('end_time'))));
            } else {
                $map['e.topdate'] = array("ELT", trim($this->_get('end_time')));
            }
        }
        $list = $this -> getStatExpense($map ? $map : array('type' => 0));
        $list['stat_cycel'] = self::$statCycel;
        $this->assign(array_merge($this->_get(), $list));
        //$this->setReUrl();
        $this->display();
    }
    /*
     * 消费统计报表
     * @author wangmengmeng
     */
    public function actionexpenseStat() {
        $map = $this->_search(array('sortware', 'expenseCollect'));
        $list = $this -> getStatExpense($map);
        $list['x_date'] = array();
        $list['y_amount'] = array();
        $list['y_degreen'] = array();
        $statType = 2;
        array_walk($list['lists'], function($value) use (&$list, $map, $statType){
            $list['x_date'][] = ($map['type'] == $statType) ? date("Y-m", strtotime($value['topdate'])) : $value['topdate'];
            $list['y_amount']['amount'][] = intval($value['amount']);
            $list['y_degreen']['degreen'][] = intval($value['degreen']);
        });
        $list['x_date'] = json_encode($list['x_date']);
        $list['y_amount'] = json_encode($list['y_amount']);
        $list['y_degreen'] = json_encode($list['y_degreen']);
        $list['charttype'] = $this->_get('charttype') ? $this->_get('charttype') : 'line';
        $list['stat_cycel'] = self::$statCycel;
        $list['title'] = self::$statCycel[$map['type'] ? $map['type'] : 0];
        $list['y_title'] = self::$statYTitle[$map['type'] ? $map['type'] : 0];
        $this->assign(array_merge($this->_get(), $list));
        //$this->setReUrl();
        $this->display("statExpense");
    }

    /*
     * 消费列表
     * @author wangmengmeng
     */

    public function actionExpenseList() {
        //搜索参数处理
        $map = $this->_search(array('sortwareClient', 'sortware'));
        $list = $this->getList($map);
        $this->assign(array_merge($this->_get(), $list));
        $this->setReUrl();
        $this->display();
    }
    private function getStatExpense($where = "") {
        return M('expense_collect e') -> join ('LEFT JOIN sortware_client c ON c.id = e.sort_client_id' ) -> join("LEFT JOIN sortware_version v ON v.id = c.sort_ver_id")  -> join("LEFT JOIN sortware s ON s.id = c.sort_id") ->field("e.id, e.sort_client_id, e.topdate, e.amount, e.degreen, c.sort_id, c.sort_ver_id, c.company, c.mobile, c.subbranch, v.version, s.name") -> where($where) -> page();
    }
    private function getList($where = "") {
        $list = M('expense e')->join('LEFT JOIN sortware_client c ON c.id = e.sort_client_id')->join('LEFT JOIN sortware s ON s.id = c.sort_id')->field("e.id,e.amount,e.start_time, e.end_time, c.sort_id, c.sort_ver_id, c.subbranch, c.company, c.mobile, s.name")->where($where)->order("e.id desc")->page();
        $list['lists'] = array_map(function (&$value) {
            //$value['sort_id'] = D('finance/software') -> getInfo($value['sort_id'], "name");
            $value['sort_ver_id'] = D('finance/softwareVersion')->getInfo($value['sort_ver_id'], "version");
            $value['start_time'] = date('Y-m-d H:i:s', $value['start_time']);
            $value['end_time'] = date('Y-m-d H:i:s', $value['end_time']);
            return $value;
        }, $list['lists']);
        return $list;
    }

    /*
     * 消费列表详情
     * @author wangmengmeng
     */

    public function actionDetail() {
        $id = $this->_get(id);
        $data = M('expense')->field('data')->where(array("id" => $id))->find();
        $list = unserialize($data['data']);
        $this->assign('lists', $list);
        $this->setReUrl();
        $this->display();
    }

    /*
     * 消费统计详情
     * @author wangmengmeng
     * */

    public function actioncollectDetail() {
        $id = $this->_get(id);
        $where = "sort_client_id = " . $this->_get('sort_client_id');
        $this->assign($this->getList($where));
        $this->setReUrl();
        $this->display();
    }

    /*
     * 客户端在线状态情况统计
     * @author tianxiang
     * */

    public function actionClientStatus() {
        $id = $this->_get(id);
        $map['sort_client_id'] = !empty($id)? $id : 101;
        //日统计数据
        $client_day_list = M('sortware_client_status_statistics s')->where($map)->field("thedate,success_num,fail_num")->order('thedate desc')->limit(30)->findAll();
        $client_day_data = $this->setClientStatusData($client_day_list, 'day');

        //周统计数据
        $client_week_list = M('sortware_client_status_statistics s')->where($map)->field("DATE_FORMAT(thedate,'%Y-%u') weeks,sum(success_num) as success_num,sum(fail_num) as fail_num")->group('weeks')->order('weeks desc')->limit(30)->findAll();
        $client_week_data = $this->setClientStatusData($client_week_list, 'weeks');

        //月统计数据
        $client_month_list = M('sortware_client_status_statistics s')->where($map)->field("DATE_FORMAT(thedate,'%Y-%m') months,sum(success_num) as success_num,sum(fail_num) as fail_num")->group('months')->order('months desc')->limit(12)->findAll();
        $client_month_data = $this->setClientStatusData($client_month_list, 'month');
        $params = array(
            'dayData' => $client_day_data,
            'weekData' => $client_week_data,
            'monthData' => $client_month_data,
        );
        $params['id'] = $id;
        $params['charttype'] = $this->_get('charttype') ? $this->_get('charttype') : 'line';
        $this->assign($params);
        $this->display();
    }

    /*
     * 客户端在线个数情况统计
     * @author tianxiang
     * */

    public function actionClientCounts() {
        //日统计数据
        $client_day_list = M('sortware_client_online_statistics s')->where($map)->field("thedate,online_num,offline_num")->order('thedate desc')->limit(30)->findAll();
        $client_day_data = $this->setClientOnlineData($client_day_list, 'day');

        //周统计数据
        $client_week_list = M('sortware_client_online_statistics s')->where($map)->field("DATE_FORMAT(thedate,'%Y-%u') weeks,sum(online_num) as online_num,sum(offline_num) as offline_num")->group('weeks')->order('weeks desc')->limit(30)->findAll();
        $client_week_data = $this->setClientOnlineData($client_week_list, 'weeks');

        //月统计数据
        $client_month_list = M('sortware_client_online_statistics s')->where($map)->field("DATE_FORMAT(thedate,'%Y-%m') months,sum(online_num) as online_num,sum(offline_num) as offline_num")->group('months')->order('months desc')->limit(12)->findAll();
        $client_month_data = $this->setClientOnlineData($client_month_list, 'month');
        $params = array(
            'dayData' => $client_day_data,
            'weekData' => $client_week_data,
            'monthData' => $client_month_data,
        );
        $params['charttype'] = $this->_get('charttype') ? $this->_get('charttype') : 'line';
        $this->assign($params);
        $this->display();
    }

    /**
     * 提取客户端状态报表数据
     * @param $data
     * @return json
     * @Date：2016.07.14
     * @author: tianxiang<609279316@qq.com>
     */
    private function setClientStatusData($data, $type = 'week') {
        if (!$data) {
            return json_encode('');
        }
        krsort($data);
        foreach ($data as $val) {
            //提取日期
            if ($type == 'month') {
                $thedate = $val['months'];
            } elseif ($type == 'weeks') {
                $warr = explode("-", $val['weeks']);

                if (!empty($warr)) {
                    $date = $this->GetWeekDate($warr[1], $warr[0]);
                }
                $thedate = $date[1];
            } else if ($type == 'day') {
                $thedate = date('Y-m-d', strtotime($val['thedate']));
            }
            $new_datas['thedate'][] = $thedate;
            //提取客户端当天成功抓取次数
            $new_datas['success_num'][] = (int) $val['success_num'];
            //提取客户端当天失败抓取次数
            $new_datas['fail_num'][] = (int) $val['fail_num'];
        }
        return json_encode($new_datas);
    }

    /**
     * 提取客户端在线个数报表数据
     * @param $data
     * @return json
     * @Date：2016.07.14
     * @author: tianxiang<609279316@qq.com>
     */
    private function setClientOnlineData($data, $type = 'week') {
        if (!$data) {
            return json_encode('');
        }
        krsort($data);
        foreach ($data as $val) {
            //提取日期
            if ($type == 'month') {
                $thedate = $val['months'];
            } elseif ($type == 'weeks') {
                $warr = explode("-", $val['weeks']);

                if (!empty($warr)) {
                    $date = $this->GetWeekDate($warr[1], $warr[0]);
                }
                $thedate = $date[1];
            } else if ($type == 'day') {
                $thedate = date('Y-m-d', strtotime($val['thedate']));
            }
            $new_datas['thedate'][] = $thedate;
            //提取客户端当天成功抓取次数
            $new_datas['online_num'][] = (int) $val['online_num'];
            //提取客户端当天失败抓取次数
            $new_datas['offline_num'][] = (int) $val['offline_num'];
        }
        return json_encode($new_datas);
    }

    /**
     * 计算每周的开始日期，结束日期
     * @param $data
     * @return json
     * @Date：2016.07.14
     * @author: tianxiang<609279316@qq.com>
     */
    private function GetWeekDate($week, $year) {
        $timestamp = mktime(0, 0, 0, 1, 1, $year);
        $dayofweek = date("w", $timestamp);
        if ($week != 1)
            $distance = ($week - 1) * 7 - $dayofweek + 1;
        $passed_seconds = $distance * 86400;
        $timestamp += $passed_seconds;
        $firt_date_of_week = date("Y-m-d", $timestamp);
        if ($week == 1)
            $distance = 7 - $dayofweek;
        else
            $distance = 6;
        $timestamp += $distance * 86400;
        $last_date_of_week = date("Y-m-d", $timestamp);
        return array($firt_date_of_week, $last_date_of_week);
    }
}
?>