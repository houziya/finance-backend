<?php
// +----------------------------------------------------------------------
// | 后台专题相关模型
// +----------------------------------------------------------------------

class model_admin_specials extends model_abstruct {

    public static $meetingplaces = array(
        1 => array('id' => 1, 'name' => '北京'),
        2 => array('id' => 2, 'name' => '西安'),
        3 => array('id' => 3, 'name' => '长沙'),
        4 => array('id' => 4, 'name' => '苏州'),
    );
	
	public static $sources = array(
        1 => array('id' => 1, 'name' => '后台'),
        2 => array('id' => 2, 'name' => 'web'),
        3 => array('id' => 3, 'name' => 'wap'),
        4 => array('id' => 4, 'name' => 'ios'),
        5 => array('id' => 5, 'name' => 'android'),
    );

    public static $licenses = array(
        1 => array('id' => 1, 'name' => '典当经营许可证'),
        2 => array('id' => 2, 'name' => '营业执照'),
        3 => array('id' => 3, 'name' => '特种行业许可证'),
        4 => array('id' => 4, 'name' => '税务登记证'),
        5 => array('id' => 5, 'name' => '组织机构代码证'),
    );

    public static $trades = array(
        1 => array('id' => 1, 'name' => '典当行'),
        2 => array('id' => 2, 'name' => '商业物业'),
        3 => array('id' => 3, 'name' => '其它'),
    );

    /**
     * 根据license_ids获取名称
     */
    public static function getLicenseNames($license_ids) {
    	$arr = explode('-', $license_ids);
    	$names = array();
    	foreach ($arr as $lid) {
    		array_push($names, self::$licenses[$lid]['name']);
    	}
    	return implode(',  ', $names);
    }

}
