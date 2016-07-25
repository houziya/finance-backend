<?php
 /**
  *
  */
class model_finance_logYear extends model
{
    protected $tableName = 'financial_log_year';
    /**
     * 根据条件获取项目资金记录
     * @param string/array $condition 条件集合
     * @param string/array $fields 获取的字段
     * @return array
     * @author Baijiansheng
     */
    public function getFinancialYearLogs($condition, $fields = '*', $order = '')
    {
        $project = M($this->tableName)->field($fields)->where($condition)->order($order)->find();
        return $project ? $project : array();
    }
}