<?php
 /**
  *
  */
class model_finance_logMonth extends model
{
    protected $tableName = 'financial_log_month';

    /**
     * 根据条件获取项目资金记录
     * @param string/array $condition 条件
     * @param string/array $fields 获取的字段
     * @param int $start 分页起始位置
     * @param int $length 分页长度
     * @return array
     * @author Baijiansheng
     */
    public function getFinancialMonthLogs($condition, $start = 0, $length = 10, $fields = '*', $order = '')
    {
        $articles = M($this->tableName)->field($fields)->where($condition)->order($order)->limit("{$start}, {$length}")->select();
        return $articles ? $articles : array();
    }
}