<?php

/**
 * 用户业务层
 */
class model_user_user extends model_abstruct {

    protected $tableName = 'user';

    /**
     * 获取项目所属地区
     * @param int $uid 用户Id
     * @return string
     */
    public function getUserArea($uid) {
        $fields = 'province, city';
        $user = D('user')->getUserByUid(intval($uid), $fields);
        if (!$user) {
            return '';
        }

        $address = '';
        if ($user['province'] || $user['city']) {
            $areaIds = array();
            if ($user['province'])
                $areaIds[] = $user['province'];
            if ($user['city'])
                $areaIds[] = $user['city'];
            $areas = D('area')->getAreas(array('id' => array('in', $areaIds)));
            helper_tool::setKeyArray($areas, 'id');
            if ($areas) {
                $address = '';
                if ($user['province'])
                    $address .= $areas[$user['province']]['name'];
                if ($user['city']) {
                    if ($areas[$user['province']]['name'] != $areas[$user['city']]['name']) {
                        $address .= $areas[$user['city']]['name'];
                    }
                }
            }
        }

        return $address;
    }

    /**
     * 获取用户列表根据用户ID集合
     * @param array $uids 用户ID集合
     * @param int $cacheTime 缓存时间秒
     * @return array
     */
    public static function getUsersByUids($uids, $cahceTime = 30) {
        $uids[] = '0';
        $condition = array('uid' => array('in', $uids));
        $cacheKey = 'model_user_getUsers_' . helper_cache::makeKey($condition);
        $callback = array(D('user'), 'getUsers');
        $users = helper_cache::getSmartCache($cacheKey, $callback, $cahceTime, array($condition));
        helper_tool::setKeyArray($users, 'uid');
        return $users;
    }

    /**
     * 获取用户列表根据用户ID集合
     * @param array $uids 用户ID集合
     * @param int $cacheTime 缓存时间秒
     * @return array
     */
    public static function getUsersCache($condition, $fields = '*', $start = 0, $length = 0, $cahceTime = 30) {
        $cacheKey = 'model_user_getUsers_' . helper_cache::makeKey($condition, $fields, $start, $length);
        $callback = array(D('user'), 'getUsers');
        $users = helper_cache::getSmartCache($cacheKey, $callback, $cahceTime, array($condition, $fields, $start, $length));
        return $users;
    }
    
  /**
     * 获取用户列表(带缓存)
     * @param $delcache 是否删除缓存
     * @author tianxiang 
     * @return array|mixed
     */
    public function getUserList() {
             $info = M('user')->findAll();    
             if(!empty($info)){
                return  $info;
             }
    }
    /*
     * 获得用户Select列表
     * @author tianxiang
     * @param 无
     * @return $option
     */
    public function getSelect() {
        $result = $this->getUserList();
        $option = '';
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $option.= '<option value="' . $value['id'] . '">' . $value['username'] . '</option>';
            }
        }
        return $option;
    }
}
