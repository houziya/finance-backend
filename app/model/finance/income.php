<?php
 /**
  * 项目店铺收入
  */
class model_finance_income extends model
{
    protected $tableName = 'financial_income';

    /**
     * 保存财务原始数据
     * @param string $rawdata
     * @return array|mixed
     */
    public function addIncome($rawdata)
    {
        if(empty($rawdata)) return;

        $financialIncome = M('financialIncome');
        $duolabaoPlugin = M('financialDuolabao');

        //POS机收入分类
        $category_info = D('financialCategory')->getFinancialCategory(array('id'=>3));

        $redis = cache::getInstance('redis');
        $log = json_decode($rawdata['rawdata'],true);
        if(($rawdata['data_source'] == 3) && $log['result'] && !empty($log['data']['orders'])){
            $income_data = array();
            $project = M('project');
            $device_info = $redis->get(model_financejob_pluginRawLog::FINANCIAL_DUOLABAO);
            foreach($log['data']['orders'] as $val){
                $project_uid = $project->field('uid')->where(array('id'=>$device_info[$val['machineNo']]['pid']))->find();
                $income_data['uid'] = $project_uid['uid'];
                $income_data['pid'] = !empty($device_info[$val['machineNo']]['pid']) ? $device_info[$val['machineNo']]['pid'] : 0;
                $income_data['store_num'] = !empty($device_info[$val['machineNo']]['store_num']) ? $device_info[$val['machineNo']]['store_num'] : 0;
                $income_data['amount'] = $val['amount'];
                $income_data['remark'] = $category_info['title'];
                $income_data['cat_id'] = 3;//POS机收入
                //$income_data['attach'] = $val[''];
                $income_data['paydate'] = date('Y-m-d H:i:s',strtotime($val['time']));
                $income_data['add_time'] = date('Y-m-d H:i:s');
                $income_data['data_source'] = $rawdata['data_source'];
                $income_data['source'] = 1;

                //添加财务日志
                if($income_data['pid']){
                    $financialIncome->data($income_data)->add();
                }else{
                    Log::write('financial_income_log_raw3 : '.json_encode($device_info).' ||  financial_income : '.  json_encode($income_data).'\r\n','INFO', 3 , LOG_PATH.'/financial_income_data'.  date('Y-m-d',  time()).'.txt');
                }
            }
        }else if(($rawdata['data_source'] == 4) && in_array($log['status'],array('success','SUCCESS')) && !empty($log['data']['result'])){
            $income_data = array();
            $project = M('project');
            $device_info = $redis->get(model_financejob_pluginRawLog::FINANCIAL_DUOLABAO);
            foreach($log['data']['result'] as $val){
                $project_uid = $project->field('uid')->where(array('id'=>$device_info[$log['data']['appId']]['pid']))->find();
                $income_data['uid'] = $project_uid['uid'];
                $income_data['pid'] = !empty($device_info[$log['data']['appId']]['pid']) ? $device_info[$log['data']['appId']]['pid'] : 0;
                $income_data['store_num'] = !empty($device_info[$log['data']['appId']]['store_num']) ? $device_info[$log['data']['appId']]['store_num'] : 0;
                $income_data['amount'] = $val['totalAmount'];
                $income_data['remark'] = $category_info['title'];
                $income_data['cat_id'] = 3;//POS机收入
                //$income_data['attach'] = $val[''];
                $income_data['paydate'] = $val['datetime'];
                $income_data['add_time'] = date('Y-m-d H:i:s');
                $income_data['data_source'] = $rawdata['data_source'];
                $income_data['source'] = 1;

                //添加财务日志
                if($income_data['pid']){
                    $financialIncome->data($income_data)->add();
                }else{
                    Log::write('financial_income_log_raw4 : '.json_encode($device_info).' ||  financial_income : '.  json_encode($income_data).'\r\n','INFO', 3 , LOG_PATH.'/financial_income_data'.  date('Y-m-d',  time()).'.txt');
                }
            }
        }

    }

}