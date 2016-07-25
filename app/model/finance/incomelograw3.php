<?php
 /**
  * 项目店铺收入原始数据
  */
class model_finance_incomeLogRaw3 extends model
{
    protected $tableName = 'financial_income_log_raw3';

    # 正常日志字段
    private $logFields = null;
    # 原始日志字段
    private $rawLogFileds = null;
    //财务原始数据抓起地址 - 多拉宝
    private $duolabaoUrlTest = 'http://test.duolabao.com/pay/merInterface.action';//测试
    private $duolabaoUrlDeploy = 'http://pay.duolabao.com/pay/merInterface.action';//正式
    //代理商编号 - 多拉宝
    private $agentNoTest = 'AN1435916457966';//测试
    private $agentNoDeploy = 'AN1435907862534';//正式
    //代理商秘钥 - 多拉宝
    private $secretTest = '46F1FumY574JmXn24bl3j45H4w85089tt1AmMf00o8550PRBqrG1Y83817Z5';
    private $secretDeploy = '4ou7m008U7iPR9320uK7f1nmP86y08p2lDynrVLVW1ojY61QZ29169r0p015';

    //抓取数据时间间隔设置【秒】
    private $timeInterval = 600;

    public function __construct() {
        parent::__construct();
        if(RUN_MODE != 'deploy'){
            //多拉宝
            $this->agentNoDeploy = $this->agentNoTest;
            $this->duolabaoUrlDeploy = $this->duolabaoUrlTest;
            $this->secretDeploy = $this->secretTest;
        }
    }
    /**
     * 抓取店铺收入原始数据（多拉宝）
     */
    public function saveIncomeData($date = '')
    {
		if(empty($date)) $date = date('Y-m-d');
		
        $incomeLog = M('financial_income_log_raw3');
		$last_day = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($date. ' 00:00:00')));

		//一天时间段拆分（10分钟为一个时间段）
		$period_time = 24 * 60 / 10;
		for ($i = 0; $i < $period_time; $i++) {
            $page = 1;//默认分页
			$startDate = date('YmdHis', strtotime($last_day) + $i * 600);
			$endDate = date('YmdHis', strtotime($last_day) + ($i+1) * 600);
			$data = array();
            $data['srid'] = date('Y-m-d H:i:s', strtotime($startDate));
            $data['rawdata'] = '';

			$all_data = $income_data = array();
            while(true){
                $url_arr = array('cmd' => 'QOF', 'agentNo' => $this->agentNoDeploy, 'startDate' => $startDate, 'endDate' => $endDate, 'pageSize' => 2000, 'curPage' => $page);
                //数据签名：对cmd+agentNo+startDate+endDate+pageSize+curPage的值，用代理商的密钥进行签名
                $signature = hash_hmac('md5', implode('', $url_arr), $this->secretDeploy);
                $duolabao_url = $this->duolabaoUrlDeploy . '?' . http_build_query($url_arr) . '&hmac=' . $signature;

                //获取数据 json
                $str_income_data = file_get_contents($duolabao_url);
                $income_data2 = json_decode($str_income_data, true);
                if(empty($income_data2['data']['orders']) || !is_array($income_data2['data']['orders'])){
					break;
                }else{
                    //获取当前数据和已有数据合并
                    $income_data = $income_data2;
                    $income_data['data']['orders'] = $all_data = array_merge($all_data, $income_data2['data']['orders']);
                }

                $page++;
            }
            if(!empty($income_data['data']['orders'])){
                $data['rawdata'] = json_encode($income_data);
                $this->addIncomeLogRaw3($data);
            }
			
			//暂停 1 秒
			//sleep(1);
			echo date('Y-m-d H:i:s', strtotime($startDate))." -- ".date('Y-m-d H:i:s', strtotime($endDate))." \n";
		}
        return true;
    }

    /**
     * 添加财务数据
     * @param array $data 数据集合
     * @return string $sysname 数据来源类型
     * @return string $data_source 数据来源表
     */
    public function addIncomeLogRaw3($data, $sysname = 'duolabao', $data_source = 3){
        if(empty($data)) return ;
        if(!intval($data_source)) $data_source = 3;
        $incomeLog = M('financial_income_log_raw'.$data_source);

        $data['srid'] = $data['srid'];
        //$data['pid'] = $income_data['orders']['shopNo'];
        //$data['store_num'] = $income_data['data']['orders']['shopNo'];
        $data['status'] = 0;
        $data['sysname'] = $sysname;
        $data['addtime'] = date('Y-m-d H:i:s');
        $data['rawdata'] = $data['rawdata'];

        //如果所抓取时间段数据已经存在，则覆盖原来的数据
        $income_row = $incomeLog->where(array('srid' => $data['srid']))->count();
        if (!$income_row) {
            $incomeLog->data($data)->add();
        } else {
            $incomeLog->where(array('srid' => $data['srid']))->data($data)->save();
        }
    }

    /**
     * 获取一条未处理的数据
     * @param array $map 条件集合
     * @return mixed
     */
    function getOneRecord($map, $data_source = '')
    {
        $map['status'] = 0;
        if($data_source){
            $info = M('financial_income_log_raw'.$data_source)->where($map)->find();
        }else{
            foreach(model_financejob_pluginRawLog::$data_source_arr as $val){
                $info = M('financial_income_log_raw'.$val['id'])->where($map)->find();
                if($info){
                    return $info;
                }
            }
        }
        //$this->rawLogFileds = $info;
        #var_dump($info);
        return $info;
    }

    /**
     * 把记录标记为以处理
     * @param $id
     * @param $pbf
     * @return bool
     */
    function mark2done($id, $data_source)
    {
        if(!$id || !$data_source) return ;
        $map['id'] = $id;
        //$map['pbf'] = $pbf;
        $upfields['status'] = 1;
        return M('financial_income_log_raw'.$data_source)->where($map)->save($upfields);
    }

    /**
     * 从原始数据内整理出证实的日志数据字段
     */
    function getLogFields()
    {

        //$this->logFields['uid'] = $this->rawLogFileds['uid'];
        $this->logFields['pid'] = $this->rawLogFileds['pid'];
        //$this->logFields['paydate'] = $this->rawLogFileds['opttime'];
        $this->logFields['store_num'] = $this->rawLogFileds['store_num'];
        $this->logFields['add_time'] = strtotime($this->rawLogFileds['addtime']);
        # 填充列
        $this->logFields['amount'] = 0;
        $this->logFields['cat_id'] = 3;
        $this->logFields['source'] = 2;
        $this->logFields['remark'] = '';
        $proc = $this->rawLogFileds['sysname'];
        if(method_exists($this, $proc)){
            $this->$proc();
        }
        return $this->logFields;
    }

    /**
     * 处理 rawlogFields['sysname'] == normal 的
     *
     */
    private function normal()
    {
        $dat = json_decode($this->rawLogFileds['rawdata'],true);
        //收入金额
        $this->logFields['amount'] = $dat['amount'] ? $dat['amount'] : 0;
        //收入编号
        $this->logFields['remark'] = $dat['item_no'] ? $dat['item_no'] : '';
        //收入日期
        $this->logFields['paydate'] = $dat['pay_date'] ? $dat['pay_date'] : 0;
    }

    /**
     * 处理 sysname == 其他的
     */
    private function someone(){}
    private function someone2(){}

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