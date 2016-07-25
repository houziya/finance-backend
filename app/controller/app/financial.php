<?php
/**
 * 消息控制器
 * @author Baijiansheng
 */
class controller_app_financial extends controller_app_abstract {

    public function __construct() {
        parent::__construct();
    }

    /*
     * 我投资的项目-财务收入、支出（月，年）
     * @param int $page 当前页【默认：1】
     * @param int $page 每页显示数量【默认：10】
     * @param date $start_num 开始时间
     * @param date $end_time 结束时间
     * @return array
     */
    public function actionGetFinanceInfo()
    {
        $this->checkLogin();

        $type = $this->_request('type') ? intval($this->_request('type')) : 1;
        $condition['pid'] = $this->_request('pid') ? intval($this->_request('pid')) : 0;
        $srid = $this->_request('time') ? intval($this->_request('time')) : date('Ym');
        $year_time = date('Y',strtotime($srid.'01'));
        $month_time = date('m',strtotime($srid.'01'));
        if($srid < date('Y').'01') $this->ajax('-105','只能查询当前年记录');
        if($srid > date('Ym')) $this->ajax('-106','已超出了时间范围');
        $condition['topdate'] = array("like", "%".date('Y-m',strtotime($srid.'01'))."%");

        if(!$condition['pid']) $this->ajax('-103','参数获取失败，请稍后再试');
        //判断是否为该项目的投资人
        $project_invest = D('ProjectInvestment')->getProjectInvestment(array('pid'=>$condition['pid'], 'uid'=>$this->_userinfo['uid'], 'status'=>3));
        if(empty($project_invest)) $this->ajax('-104','对不起！您没有该访问权限');

        $page = $this->getPageCondition();

        $order = 'srid DESC';
        // 基本信息页面变量渲染（日收入）
        if($type == '1') $fields = 'topdate,amount_income';
        else $fields = 'topdate,amount_expenditure';
        $cacheKey3 = "model_finance_logDay_{$condition['pid']}_{$srid}" . helper_cache::makeKey($condition, 0, 31, $fields, $order);
        $callback3 = array(D('finance/logDay'), 'getFinancialDayLogs');
        $day_log = helper_cache::getSmartCache($cacheKey3, $callback3, 30, array($condition, 0, 31, $fields, $order, true));

        unset($condition['topdate']);
        // 基本信息页面变量渲染（月收入）
        $condition['srid'] = $year_time.$month_time;
        /*$fields = '*';
        $cacheKey = "model_finance_logMonth_{$condition['pid']}_{$srid}" . helper_cache::makeKey($condition, $page['start_num'], $page['page_num'], $fields, $order);
        $callback = array(D('finance/logMonth'), 'getFinancialMonthLogs');
        $month_log = helper_cache::getSmartCache($cacheKey, $callback, 30, array($condition, $page['start_num'], $page['page_num'], $fields, $order, true));*/
        $info['month'] = M('financial_log_month')->field('sum(amount_income) as amount_income,sum(amount_expenditure) as amount_expenditure')->where($condition)->find();

        // 基本信息页面变量渲染（年收入）
        $condition['srid'] = $year_time;//
        /*$cacheKey2 = "model_finance_logYear_{$condition['pid']}_{$year_time}";
        $callback2 = array(D('finance/logYear'), 'getFinancialYearLogs');
        $year_log = helper_cache::getSmartCache($cacheKey2, $callback2, 30, array('pid='.$condition['pid'].' and srid= '.$year_time));var_dump((M()->getlastsql()));*/
        $info['year'] = M('financial_log_year')->field('sum(amount_income) as amount_income,sum(amount_expenditure) as amount_expenditure')->where($condition)->find();

        if($type == '1'){
            $list['month_total_amount'] = $info['month']['amount_income'] ? $info['month']['amount_income'] : "";
            $list['year_total_amount'] = $info['year']['amount_income'] ? $info['year']['amount_income'] : "";
            if($list['year_total_amount'] < 1) $day_log = array();
        }else if($type == 2){
            $list['month_total_expenditure'] = $info['month']['amount_expenditure'] ? $info['month']['amount_expenditure'] : "";
            $list['year_total_expenditure'] = $info['year']['amount_expenditure'] ? $info['year']['amount_expenditure'] : "";
            if($list['year_total_expenditure'] < 1) $day_log = array();
        }

        $list['info'] = $day_log;
        $list['last_month'] = $year_time.sprintf('%02d',$month_time - 1);
        $list['next_month'] = $year_time.sprintf('%02d',$month_time + 1);
        $list['year'] = date('Y',strtotime($srid.'01'));
        $list['month'] = date('m',strtotime($srid.'01'));
        $list['project_id'] = $condition['pid'];
        if( isset($list) && !empty($list) ){
            foreach($list['info'] as $key=>$val){
                $list['info'][$key]['topdate'] = strtotime($val['topdate']);
            }
            $list['time']  =  date('Y年m月',strtotime($srid.'01'));
            $list['datetime']  =  date('Ym',strtotime($srid.'01'));
            $this->ajax('-101','获取成功',$list);
        }else{
            $this->ajax('-102','您获取的信息不存在');
        }
    }

    /*
     * 保存票据信息
     * @param int $pid 项目ID
     * @param int $amount 金额
     * @param date $date 时间
     * @param string $remark 说明
     * @return array
     */
    public function actionSetBillInfo(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];

        $data['cat_id'] = intval($this->_request('cat_id', 0));
        $data['store_num'] = intval($this->_request('store_num', 1));
        $data['pid'] = intval($this->_request('pid', 0));
        $data['amount'] = $this->_post('amount') ? sprintf("%1.2f",$this->_post('amount')):'';
        $data['paydate'] = $this->_post('date') ? date('Y-m-d H:i:s',strtotime($this->_post('date'))) : date('Y-m-d H:i:s');
        $data['remark'] = $this->_post('remark') ? helper_string::safeReplace(helper_string::removeXss($this->_post('remark'))) : '';
        $attach = $this->_post('save_img') ? helper_string::safeReplace(helper_string::removeXss(($this->_post('save_img')))) : '';

        if (!$data['cat_id']) $this->ajax('-105', '请选择支出所属类型');
        if(!$data['amount']) $this->ajax('-103','金额不能为空');
        if(!$data['remark'] && !$data['data']['url']) $this->ajax('-104','票据或者说明必须填写一种');
        if($data['amount']){
            $data['uid'] = $uid;
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['source'] = C('sys_global_source');
            $data['attach'] = serialize($attach);
            $res = M('financialExpenditure')->data($data)->add();
            $this->savelog("用户中心-我的项目投后管理-财务支出添加：" . serialize($data));
        }
        if($res){
            $this->ajax('-101','提交成功');
        }else{
            $this->ajax('-102','提交失败');
        }
    }
    /*
     * 获取票据支出类型
     * @return array
     */
    public function actionGetExpenditureType(){
        // 用款类别信息
        $types = D('FinancialCategory')->getFinancialCategoryByPid(2);
        if(!empty($types)){
            foreach($types as $key=>$val){
                $data[$key]['id'] = $val['id'];
                $data[$key]['pid'] = $val['pid'];
                $data[$key]['title'] = $val['title'];
                unset($types);
            }
            $this->ajax('-101','获取成功',$data);
        }else{
            $this->ajax('-102','您获取的信息不存在');
        }
    }

    //获取分页参数
    private function getPageCondition(){
        $data['page'] = $this->_request('page') ? intval($this->_request('page')) : 1;
        $data['page_num'] = $this->_request('page_num') ? intval($this->_request('page_num')) : 10;
        $data['start_num'] = ($data['page'] - 1) * $data['page_num'];

        return $data;
    }
}
