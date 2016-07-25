<?php
/**
 * 财务收入支出月统计管理
 * -------------------------------------------------------------------------------------------
 * 功能描述
 * -------------------------------------------------------------------------------------------
 * Date: 2015/4/29
 * @author wangbingang<67063492@qq.com>
 * @version 2.0  
 */
class model_financial_log_month extends model_abstruct
{
    public $tableName = 'financial_log_month';

    /**
     * 根据条件查询一条数据
     * @param $condition 条件集合
     * @param field $field 查询字段
     * @return array
     * @Date：2015.4.29
     * @author: wangbingang<67063492@qq.com>
     */
    public function getOneData($condition, $field='*')
    {
        $data = $this->field($field)->where($condition)->find();
        return $data ? $data : array();
    }
}