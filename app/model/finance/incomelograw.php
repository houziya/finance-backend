<?php
 /**
  * 项目店铺收入原始数据
  */
class model_finance_incomeLogRaw extends model
{
    protected $tableName = 'financial_income_log_raw';

    # 正常日志字段
    private $logFields = null;
    # 原始日志字段
    private $rawLogFileds = null;


    /**
     * 获取一条未处理的数据
     * @return mixed
     */
    function getOneRecord()
    {
        $map['status'] = 0;
        $info = $this->where($map)->limit(1)->find();
        $this->rawLogFileds = $info;
        #var_dump($info);
        return $info;
    }

    /**
     * 把记录标记为以处理
     * @param $id
     * @param $pbf
     * @return bool
     */
    function mark2done($id)
    {
        $map['id'] = $id;
        //$map['pbf'] = $pbf;
        $upfields['status'] = 1;
        return $this->where($map)->save($upfields);
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
        $this->logFields['uid']    = 0;
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


    /**
     * 处理 rawlogFields['sysname'] == normal 的
     * 智百威回调函数
     * @author wangbingang
     */
    private function zbw_normal()
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
     * 处理 rawlogFields['sysname'] == normal 的
     * 荣耀回调函数
     * @author wangbingang
     */
    private function ry_normal()
    {
        $dat = json_decode($this->rawLogFileds['rawdata'],true);
        //收入金额
        $this->logFields['amount'] = $dat['Price'] ? $dat['Price'] : 0;
        //收入编号
        $this->logFields['remark'] = $dat['ConsumptionDate'] ? $dat['ConsumptionDate'] : '';
        //收入日期
        $this->logFields['paydate'] = $dat['pay_date'] ? $dat['pay_date'] : 0;
    }

    /**
     * 处理 rawlogFields['sysname'] == normal 的
     * 托维食天下回调函数
     * @author wangbingang
     */
    private function tw_normal()
    {
        $dat = json_decode($this->rawLogFileds['rawdata'],true);
        //收入金额
        $this->logFields['amount'] = $dat['合计金额'] ? $dat['合计金额'] : 0;
        //收入编号
        $this->logFields['remark'] = $dat['item_no'] ? $dat['item_no'] : '';
        //收入日期
        $this->logFields['paydate'] = $dat['pay_date'] ? $dat['pay_date'] : 0;
    }
}