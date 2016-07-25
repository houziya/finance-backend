<?php
/**
 * Class model_finance_pluginArchive
 * 所有财务监控的项目,都会在这个表里面出现
 * 可以使用最后更新时间,检测监控数据获取的活跃性
 */

class model_finance_pluginArchive extends model
{
    protected $tableName = 'financial_plugin_archive';

    /**
     * @param $args
     * @param null $fieldName
     * @return array|mixed
     */
    public function getArchive($args, $fieldName=null)
    {
        $map = array();
        if (is_string($args)) {
            $map['id'] = $args;
        } elseif (is_array($args) && $args) {
            $map = $args;
        } else {
            return array();
        }
        $info = $this->where($map)->find();
        return $fieldName ? $info[$fieldName] : $info;
    }

    /**
     * 批量获取存档信息
     * @param int $id
     * @param int $limit
     * @return mixed
     */
    function getArchives($id=0, $limit=100)
    {
        $info = $this->where("id>$id")->limit($limit)->find();
        return $info;
    }

    /**
     * 添加一条存档信息
     * @param $pid
     * @param $storenum
     * @param $opttime
     * @return bool
     */
    public function addArchive($pid, $storenum, $opttime)
    {
        $data['pid'] = $pid;
        $data['opttime'] = $opttime;
        $data['store_num'] = $storenum;
        $data['posttime'] = date('Y-m-d H:i:s');

        # todo filter

        if (!empty($data)){
            $res = $this->add($data);
            if($res){
                return true;
            }
            return false;
        };
    }

    /**
     * 更新最后数据获取的时间
     * @param $id   archive id
     * @param $opttime  last operate time
     * @return bool
     */
    public function updateArchiveLastTime($id, $flowNum, $opttime)
    {
        $data['flownum'] = $flowNum;
        $data['opttime'] = $opttime;
        return $this->where("id=$id")->save($data);
    }

}
