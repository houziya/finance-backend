<?php

/**
 * ip模型
 */
class model_ipAddress extends model_abstruct {

	protected $tableName = 'ip_address';
	/**
         * 根据ip获取省市id
         * @param type $ip
         * @return null
         */
        public static function getAddress($ip)
        {
            $ipNum = helper_string::ip2num($ip);
            if($ipNum)
            {
                $ipModel = D('ipAddress');
                $info = $ipModel->where('num_begin <= "'.$ipNum.'" AND num_end >= "'.$ipNum.'"')->field("address,province,city")->order("id asc")->find();
                return $info;
            }
            return null;
        }
}
