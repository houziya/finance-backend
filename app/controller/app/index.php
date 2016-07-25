<?php
class controller_app_index extends controller_app_abstract {

    const PURCHASE_COUNTDOWN_TIME = 7200; //抢购项目倒计时时间，单位：秒

    public function __construct() {
        parent::__construct();
    }

    /*
     * 获取首页项目
     * @param $type 项目类型（待秒杀:1，抢购：2，认购：3）
     * @param $limit 条数【默认：1】
     * @return array
     */
	public function actionGetHomeProject(){
        $where_sql = array();
        //待秒杀:1，抢购：2，认购：3
        //$limit = trim($this->_post('limit'))?intval($this->_post('limit')):1;

        $arr_img = C('url');
        $project_status = array(2=>'预热',4=>'融资',5=>'失败',6=>'成功',7=>'结项');
        $cachename = 'app_index_GetHomeProject';
        $cache_data = S($cachename);
        if(!empty($cache_data)) $this->ajax(-101, '获取成功',$cache_data);

		$project = D('project');
        //待秒杀
        $where_sql['status'] = 2 ;
        $where_sql['is_show'] = 1;
        $where_sql['amount_begin_time'] = array('gt', time());
        $order = ' amount_begin_time ASC ';

        //抢购
        /*$where_sql2['is_show'] = 1;
        $where_sql2['status'] = 4;
        $where_sql2['amount_begin_time'] = array('elt', ('amount_begin_time+'.self::PURCHASE_COUNTDOWN_TIME));
        $order2 = ' amount_begin_time DESC ';*/

        //认购
        $field3 = ',invest_amount,founder_amount,finance_amount/finance_total as invest_progress';
        $where_sql3['is_show'] = 1;
        $where_sql3['status'] = 4;
        //$where_sql3['amount_begin_time'] = array('egt', ('amount_begin_time+'.self::PURCHASE_COUNTDOWN_TIME));
        $order3 = ' amount_begin_time DESC ';

        $M =  M('');
        //$snapup = $project->getProjects($where_sql2,0,1,$field,$order2);
        //$list['invest'] = $project->getProjects($where_sql3,0,1,$field.$field3,$order3);
        $field = 'id,name,img_app,img_cover,province,city,area,lest_finance,finance_total,founder_pay,finance_amount,amount_begin_time,funding_cycle,is_deposit,deposit,is_fixed_return,fixed_return,is_float_return,float_return_min,float_return_max,is_buyback,buyback_cycle,frequency_fixed,frequency_float,finsh_time,opening_time,pre_year_return_min,pre_year_return_max,is_share,status,areatype,is_new_mode,is_type_stock,is_type_profit,is_type_consumption,is_type_product,return_display';
        //$sql2 = 'SELECT '.$field.' FROM (SELECT '.$field.',('.time().'-amount_begin_time) as countdown FROM `project` WHERE ( `is_show` = 1 ) AND ( `status` = 4 ) ORDER BY '.$order2.' LIMIT 0, 1 ) as project where countdown<='.self::PURCHASE_COUNTDOWN_TIME;
        //$sql3 = 'SELECT '.$field.$field3.' FROM (SELECT '.$field.$field3.',("'.time().'"-amount_begin_time) as countdown FROM `project` WHERE ( `is_show` = 1 ) AND ( `status` = 4 ) ORDER BY '.$order3.' LIMIT 0, 3 ) as project where countdown>'.self::PURCHASE_COUNTDOWN_TIME;
        $sql3 = 'SELECT '.$field.$field3.' FROM `project` WHERE ( `is_show` = 1 ) AND ( `status` = 4 ) ORDER BY '.$order3/*.' LIMIT 0, 3 '*/;
        //$snapup = $M->query($sql2);
        //$waiting = $project->getProjects($where_sql,0,3 ,$field,$order);//- count($snapup)
        $waiting = M('project')->field($field)->where($where_sql)->order($order)->limit(0,3)->select();
        $list['invest'] = $M->query($sql3);
        //if($limit == 1) $list = $list[0];
        if(!empty($waiting)){
            foreach($waiting as $key=>$val){
                //额外回报
                $other_return = model_ProjectProperty::getInfo($val['id'],'other_return');
                //$waiting[$key]['other_return'] = $other_return['other_return'];
                $val['other_return'] = $other_return['other_return'];
                $waiting[$key] = model_project::fetchOtherToProjectOne($val);
                $waiting[$key]['percent'] = 0; // 预热项目进度全部为0
                $waiting[$key]['img_app'] = $val['img_app'] ? helper_tool::getThumbImg($val['img_app'], 300, 200) : helper_tool::getThumbImg($val['img_cover'], 300, 200);
                $waiting[$key]['invest_type'] = 'waiting';
                $waiting[$key]['status_str'] = $project_status[$val['status']];
                //项目状态
                $waiting[$key]['status_str'] = $project_status[$val['status']];
                //定向地区
                //$areatype = D('project/project')->getProjectAreasCache(array($val['id']));
                //$waiting[$key]['areatype'] = $areatype[$val['id']]['shortAddress'];
                $waiting[$key]['is_areatype'] = $val['areatype'] ? 1 : 0;

                $share_info = $this->getShares(array($val['id']));
                // 月回报
                $waiting[$key]['month_rate'] = $share_info['monthReturnRates'][$val['id']];
                // 年回报
                $waiting[$key]['year_rate'] = $share_info['yearReturnRates'][$val['id']];
                // 最新分红
                $lastShareAmount = $share_info['lastShares'][$val['id']]['amount'];
                $waiting[$key]['share_amount'] = $lastShareAmount ? helper_tool::moneyFormat($lastShareAmount) : '-';
                // 分红总金额
                $projectShareAmount = $share_info['shareAmounts'][$val['id']];
                $waiting[$key]['project_share_amount'] = $projectShareAmount ? helper_tool::moneyFormat($projectShareAmount) : '-';
                // 分红期数
                $waiting[$key]['periods'] = isset($share_info['periods'][$val['id']]) ? "{$share_info['periods'][$val['id']]}期" : '0期';

                //分红类型
                //$waiting[$key]['shares_type'] = $waiting[$key]['share_type_str']; // $this->getSharesType($val);
                $waiting[$key]['shares_type'] = $waiting[$key]['superscript2']['share_type_str'];
                // 分红类型
                /*$stockTypes = array();
                if ($val['is_type_stock']) $stockTypes[] = '股权';
                if ($val['is_type_profit']) $stockTypes[] = '收益权';
                if ($val['is_type_consumption']) $stockTypes[] = '消费权';
                if ($val['is_type_product']) $stockTypes[] = '产品权';
                $waiting[$key]['stock_types'] = $stockTypes ? implode('+', $stockTypes) : '-';*/
                $waiting[$key]['stock_types'] = $waiting[$key]['superscript2']['stock_type_str'];

                //地区
                $area = D('area')->getArea(array('id'=>$val['province']),'shortname');
                $waiting[$key]['province_str'] = $area['shortname'] ? $area['shortname'] : '';
                $area = D('area')->getArea(array('id'=>$val['city']));
                $waiting[$key]['city_str'] = $area['name'] ? $area['name'] : '';
                $area = D('area')->getArea(array('id'=>$val['area']),'shortname');
                $waiting[$key]['area_str'] = $area['shortname'] ? $area['shortname'] : '';

                unset($waiting[$key]['areatype']);
                
                if ($val['id'] == 18697) {
                    $waiting[$key]['is_new_mode'] = 1;
                    $waiting[$key]['is_float_return'] = 1;
                    $waiting[$key]['frequency_fixed_str'] = '';
                    $waiting[$key]['frequency_float_str'] = '';
                }

                unset($waiting[$key]['superscript2']);
            }
        }else{
            $list['waiting'] = array();
        }
        /*if(!empty($snapup)){
            foreach($snapup as $key=>$val){
                $snapup[$key]['img_app'] = $val['img_app'] ? helper_tool::getThumbImg($val['img_app'], 300, 200) : helper_tool::getThumbImg($val['img_cover'], 300, 200);
                $snapup[$key]['invest_type'] = 'snapup';
            }
        }*/
        if(!empty($list['invest'])){
                foreach($list['invest'] as $key=>$val){
                //额外回报
                $other_return = model_ProjectProperty::getInfo($val['id'],'other_return');
                //$waiting[$key]['other_return'] = $other_return['other_return'];
                $val['other_return'] = $other_return['other_return'];
                $list['invest'][$key] = model_project::fetchOtherToProjectOne($val);

                $list['invest'][$key]['img_app'] = $val['img_app'] ? helper_tool::getThumbImg($val['img_app'], 300, 200) : helper_tool::getThumbImg($val['img_cover'], 300, 200);
                $list['invest'][$key]['invest_type'] = 'invest';
                //融资剩余时间
                $cycleTime = $val['amount_begin_time'] + $val['funding_cycle'] * 3600 * 24;
                $days = ceil(($cycleTime - time()) / (3600 * 24));
                $list['invest'][$key]['surplus_time'] = $days > 0 ? $days : 0;
                //项目状态
                $list['invest'][$key]['status_str'] = $project_status[$val['status']];
                //定向地区
                //$areatype = D('project/project')->getProjectAreasCache(array($val['id']));
                //$list['invest'][$key]['areatype'] = $areatype[$val['id']]['shortAddress'];
                $list['invest'][$key]['is_areatype'] = $val['areatype'] ? 1 : 0;

                $share_info = $this->getShares(array($val['id']));
                // 月回报
                $list['invest'][$key]['month_rate'] = $share_info['monthReturnRates'][$val['id']];
                // 年回报
                $list['invest'][$key]['year_rate'] = $share_info['yearReturnRates'][$val['id']];
                // 最新分红
                $lastShareAmount = $share_info['lastShares'][$val['id']]['amount'];
                $list['invest'][$key]['share_amount'] = $lastShareAmount ? helper_tool::moneyFormat($lastShareAmount) : '-';
                // 分红总金额
                $projectShareAmount = $share_info['shareAmounts'][$val['id']];
                $list['invest'][$key]['project_share_amount'] = $projectShareAmount ? helper_tool::moneyFormat($projectShareAmount) : '-';
                // 分红期数
                $list['invest'][$key]['periods'] = isset($share_info['periods'][$val['id']]) ? "{$share_info['periods'][$val['id']]}期" : '0期';

                //分红类型
                $list['invest'][$key]['shares_type'] = $list['invest'][$key]['superscript2']['share_type_str']; // $this->getSharesType($val);
                // 分红类型
                /*$stockTypes = array();
                if ($val['is_type_stock']) $stockTypes[] = '股权';
                if ($val['is_type_profit']) $stockTypes[] = '收益权';
                if ($val['is_type_consumption']) $stockTypes[] = '消费权';
                if ($val['is_type_product']) $stockTypes[] = '产品权';
                $list['invest'][$key]['stock_types'] = $stockTypes ? implode('+', $stockTypes) : '-';*/
                $list['invest'][$key]['stock_types'] = $list['invest'][$key]['superscript2']['stock_type_str'];

                //地区
                $area = D('area')->getArea(array('id'=>$val['province']),'shortname');
                $list['invest'][$key]['province_str'] = $area['shortname'] ? $area['shortname'] : '';
                $area = D('area')->getArea(array('id'=>$val['city']));
                $list['invest'][$key]['city_str'] = $area['name'] ? $area['name'] : '';
                $area = D('area')->getArea(array('id'=>$val['area']),'shortname');
                $list['invest'][$key]['area_str'] = $area['shortname'] ? $area['shortname'] : '';

                if ($val['id'] == 18697) {
                    $list['invest'][$key]['is_new_mode'] = 1;
                    $list['invest'][$key]['is_float_return'] = 1;
                    $list['invest'][$key]['frequency_fixed_str'] = '';
                    $list['invest'][$key]['frequency_float_str'] = '';
                }

                unset($list['invest'][$key]['superscript2']);
            }
        }else{
            $list['invest'] = array();
        }
        $list['time'] = time();
        //if(empty($waiting)) $list['waiting'] = $snapup ? $snapup : array();
        //if(empty($snapup)) $list['waiting'] = $waiting ? $waiting : array();
        //if(!empty($waiting) && !empty($snapup)) $list['waiting'] = array_merge($waiting,$snapup);
        if(!empty($waiting)) $list['waiting'] = $waiting;
        if(!empty($list)){
            S($cachename, $list, 3);
            $this->ajax(-101, '获取成功',$list);
        }
        $this->ajax(-103, '暂无相关内容',$list);
	}

    public function getShares($projectIds)
    {
        //$projectIds[] = '0';
//        // 获取项目扩展属性
//        $fields = 'pid, e_pnum, period_time, e_address, par_time, p_profit_time';
//        $condition = array('pid' => array('in', $projectIds));
//        $propertys = M('project_property')->where($condition)->field($fields)->select();
//        helper_tool::setKeyArray($propertys, 'pid');

        // 项目分红总金额
        $shareAmounts = array();
        // 月回报率
        $monthReturnRates = array();
        // 年回报率
        $yearReturnRates = array();
        // 分红总期数
        $periods = array();
        // 分红涉及到的年份
        $shareYears = array();

        foreach ($projectIds as $val) {
            if (!$val) {
                continue;
            }
            $share_amount = D('ProjectShareRepayment')->getSharesProjectIds($val, 1);
            //$shareAmounts[$val] = D('ProjectShareSubject')->getProjectShareAmount($val);
            $shareAmounts[$val] = $share_amount[$val];
            $monthReturnRates[$val] = D('project/share')->getMonthRate($val);
            $yearReturnRates[$val] = D('project/share')->getYearRate($val);
            $share_periods = D('ProjectShareRepayment')->getSharesProjectIds($val, 2);
            $periods[$val] = $share_periods[$val];
            $share_years = D('ProjectShareRepayment')->getSharesProjectIds($val, 3);
            $shareYears[$val] = $share_years[$val];
        }
        // 最新分红
        $lastShares = D('ProjectShareRepayment')->getLastSharesByProjectIds($projectIds);

        return array(
//            'propertys' => $propertys,
            'shareAmounts' => $shareAmounts,
            'monthReturnRates' => $monthReturnRates,
            'yearReturnRates' => $yearReturnRates,
            'periods' => $periods,
            'shareYears' => $shareYears,
            'lastShares' => $lastShares
        );
    }

    /*
     * 相关统计数据
     * @param null
     * @return array
     */
    public function actionGetHomeCount(){
        $where_sql = array();

        // 单项最高众筹金额
        $condition = array('is_show' => 1, 'status' => array('in', array(2, 4, 6, 7)));
        $res = M('project')->field('max(finance_total)')->where($condition)->find();
        $list['max_project_amount'] = array_pop($res);

        // 项目总数
        $condition = array('status' => array('in', array(2, 4, 6)), 'is_show' => 1);
        $cacheKey = 'app_model_project_getProjectCount_' . helper_cache::makeKey($condition);
        $callback = array(D('project'), 'getProjectCount');
        $list['project_count'] = helper_cache::getSmartCache($cacheKey, $callback, 3600, array($condition));

        // 成功众筹金额
        $condition = array('status' => array('in', array(1, 2, 3)));
        $cacheKey = 'model_projectInvestment_getProjectInvestmentAmount_' . helper_cache::makeKey($condition);
        $callback = array(D('projectInvestment'), 'getProjectInvestmentAmount');
        $list['project_amount'] = helper_cache::getSmartCache($cacheKey, $callback, 30, array($condition));

        // 投资人数
        $cacheKey = 'app_model_user_getUserCount';
        $callback = array(D('user'), 'getUserCount');
        $list['user_count'] = helper_cache::getSmartCache($cacheKey, $callback, 30, array(array()));

        // 预约认购项目总金额
        $cacheKey = 'app_model_ProjectInvestmentPre_getProjectInvestmentPreAmount';
        $callback = array(D('ProjectInvestmentPre'), 'getProjectInvestmentPreAmount');
        $list['investment_count'] = helper_cache::getSmartCache($cacheKey, $callback, 30);

        // 成功项目总金额
        $condition = array('status' => array('in', array(1, 2, 3)));
        $cacheKey = 'app_model_projectInvestment_getProjectInvestmentAmount_' . helper_cache::makeKey($condition);
        $callback = array(D('projectInvestment'), 'getProjectInvestmentAmount');
        $list['invest_amount'] = helper_cache::getSmartCache($cacheKey, $callback, 30, array($condition));

        // 其他各类项目数, //融资项目数  预热项目数  分红项目数 成功项目数
        $counts = array();
        $types = array('amountingCount', 'preheatCount', 'shareCount', 'successCount');
        foreach ($types as $val) {
            $list[$val] = D('project/project')->getProjectCount($val);
        }

        if(!empty($list)){
            $this->ajax(-101, '获取成功',$list);
        }
        $this->ajax(-103, '暂无相关内容',$list);
    }

    //将秒（非时间戳）转化成 ** 小时 ** 分
    protected function sec2time($sec){
        $sec = round($sec/60);
        if ($sec >= 60){
            $hour = floor($sec/60);
            $min = $sec%60;
            $res = $hour.'小时';
            $min != 0  &&  $res .= $min.'分';
        }else{
            $res = $sec.'分钟';
        }
        return $res;
    }

    //分红类型
    public function getSharesType($project){
        $frequency = model_project::$shareFrequency;
        // 回报类型
        $returnType = array();
        // 分红频次
        $shareFrequency = array();
        if ($project['is_fixed_return']) {
            $returnType[] = '基础';
            if ($project['frequency_fixed']) {
                $shareFrequency[] = $frequency[$project['frequency_fixed']]['name'];
            }
        }
        if ($project['is_float_return']) {
            $returnType[] = '浮动';
            if ($project['frequency_float']) {
                $shareFrequency[] = $frequency[$project['frequency_float']]['name'];
            }
        }
        $returnType = implode('+', $returnType);
        $returnType = $returnType ? $returnType : ' - ';
        return $returnType;
    }

}
