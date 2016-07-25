<?php
 /**
  * 项目店铺收入原始数据
  */
class model_finance_incomeLogRaw4 extends model
{
    protected $tableName = 'financial_income_log_raw4';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 抓取店铺收入原始数据（银豹收银系统）
     */
    public function saveYbIncomeData($date = '')
    {
        $auth = model_financejob_pluginRawLog::$data_source_arr[4]['authInfo'];
        foreach($auth as $val){
            $this->getYbIncomeData($date,$val);
        }
    }

    /**
     * 抓取店铺收入原始数据并保存（银豹收银系统）
     * @param date  $date 日期
     * @param array() $auth 第三方接口认证信息（每个门店认证信息不同）
     */
    public function getYbIncomeData($date = '', $auth = '')
    {
        if(empty($auth)) return false;
        if(empty($date)) $date = date('Y-m-d');

        $incomeLog = M('financial_income_log_raw4');
        $last_day = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($date. ' 00:00:00')));

        //一天时间段拆分（10分钟为一个时间段）
        $period_time = 24 * 60 / 10;
        for ($i = 0; $i < $period_time; $i++) {
            $startDate = date('Y-m-d H:i:s', strtotime($last_day) + $i * 600);
            $endDate = date('Y-m-d H:i:s', strtotime($last_day) + ($i+1) * 600);
            $data = array();
            $data['srid'] = $startDate;
            $data['rawdata'] = '';

            $page['postBackParameter'] = array("parameterType"=> "LSAT_RESULT_MAX_ID","parameterValue"=> "");//默认分页
            $all_data = $income_data = array();
            while(true){
                //银豹接口地址：https://area4-win.pospal.cn:443/pospal-api2/openapi/v1/ticketOpenApi/queryTicketPages
                $request_url = $auth['urlPreFix'].'pospal-api2/openapi/v1/ticketOpenApi/queryTicketPages';
                $arr_data = array("appId"=>$auth['appID'],"startTime"=> $startDate,"endTime"=>$endDate);
                if($page['postBackParameter']['parameterValue']) $arr_data['postBackParameter'] = $page['postBackParameter'];
                $json_data = json_encode($arr_data);
                $data_signature = strtoupper(md5($auth['appKey'].$json_data));

                //获取数据 json
                $str_income_data = $this->ybRequestPost($request_url, $json_data, $data_signature);
                $income_data2 = json_decode($str_income_data, true);
                if($income_data2['data']['postBackParameter']['parameterValue'])
                    $page['postBackParameter'] = array('parameterType'=>$income_data2['data']['postBackParameter']['parameterType'],'parameterValue'=>$income_data2['data']['postBackParameter']['parameterValue']);
                if(!in_array($income_data2['status'],array( 'success','SUCCESS')) || empty($income_data2['data']['result']) || !is_array($income_data2['data']['result'])){
                    break 1;
                }else{
                    $income_data2['data']['appId'] = $auth['appID'];
                    //获取当前数据和已有数据合并
                    $income_data = $income_data2;
                    //$income_data['data']['pageSize'] = intval($all_data['data']['pageSize']) + count($income_data2['data']['result']);
                    $income_data['data']['result'] = $all_data = array_merge($all_data, $income_data2['data']['result']);

                    //如果返回数据不小于每页返回总条数 则获取下一页数据
                    if(count($income_data2['data']['result']) < $income_data2['data']['pageSize']) break;
                    //$page['postBackParameter'] = array('parameterType'=>$income_data2['data']['postBackParameter']['parameterType'],'parameterValue'=>$income_data2['data']['postBackParameter']['parameterValue']);
                    //else break 1;
                }

            }
            if(!empty($income_data['data']['result'])){
                $data['rawdata'] = json_encode($income_data);
                //$this->addIncomeLogRaw3($data, 'yinbao');
                D('finance_incomeLogRaw3')->addIncomeLogRaw3($data, 'yinbao', 4);
            }

            //暂停 1 秒
            //sleep(1);
            echo $startDate." -- ".$endDate." \n";
        }
        return true;
    }

    //银豹接口数据请求
    public function ybRequestPost($url, $data_string = null, $signature = ''){
        if(!$data_string) return array();

        $header = array (
            "User-Agent:openApi",
            "Content-Type: application/json; charset=utf-8",
            "accept-encoding: gzip,deflate",
            "time-stamp: ".(time() * 1000),
            "data-signature: {$signature}",
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($ch);

        return $data ? $data : array();
    }

}