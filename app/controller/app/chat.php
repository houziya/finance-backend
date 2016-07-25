<?php
/**
 * 聊天控制器
 * @author Baijiansheng
 */
class controller_app_chat extends controller_app_abstract {
    private $chat;

	public function __construct() {
		parent::__construct();

        $this->chat = D('chat/chat');
	}

    /*
	 * 获取聊天用户信息
	 * @return array
	 * @author Baijiansheng
	 */
    public function actionGetChatUserInfo(){
        if(!$uid = intval($this->_request('uid'))) $this->ajax(-103, '参数获取失败，请稍后再试');
        $result = $this->chat->getUsers($uid);
        if($result['duration']){
            $this->ajax(-101, '获取成功',$result);
        }
        $this->ajax(-102, '获取失败');
    }

    /*
	 * 注册环信用户（用户必须为人人投会员）
	 * @return array
	 * @author Baijiansheng
	 */
    private function registerChatUsers($uid){
        //$uid = intval($this->_request('uid',0));
        if(!$uid) $this->ajax(-103, '参数获取失败，请稍后再试');
        $user_info = D('user')->getInfo($uid);
        if(empty($user_info)) $this->ajax(-104, '对不起！您所注册的用户不是人人投会员');
        //判断是否已经创建
        $chat_user = D('chat/user')->getChatUser(array('uid'=>$uid));
        if(!empty($chat_user)) $this->ajax(-105, '您所注册的用户已经存在，不能重复注册');
        else{
            $hxchat = new helper_hxchat();
            $hxchat->deleteUser($uid);
        }
        //创建用户（注册）
        $userInfo = array(array('username' => $user_info['uid'],
            'password' => md5($user_info['password'].'rrt_chat'),
            'nickname' => $user_info['realname']),
        );
        $result = $this->chat->createUser($userInfo);
        if(!$result['duration']){
            $this->ajax(-102, '注册失败');
        }
    }

    /*
	 * 创建聊天群，群主为项目方
	 * @return array
	 * @author Baijiansheng
	 */
    public function actionAddChatGroup(){
        $this->checkLogin();

        if(!$project_id = intval($this->_request('project_id'))) $this->ajax(-103, '参数获取失败，请稍后再试');
        $grouptype = intval($this->_request('grouptype')) ? intval($this->_request('grouptype')) : '1';
        $project_info = D('project')->getInfo($project_id);
        if(empty($project_info)) $this->ajax(-104, '对不起！您所创建群组的相关项目信息不存在或已过期');
        //判断如果为预热项目时创建（此群为公开群，只有认证投资人才可加入此群【单用户申请加入是会有此验证】）
        //if($project_info['status'] != 2) $this->ajax(-105, '对不起！此项目无法创建聊天群');
        //判断该项目聊天群否已经创建
        $chat_group = D('chat/group')->getChatGroup(array('project_id'=>$project_info['id'], 'grouptype'=> $grouptype));
        if(!empty($chat_group)) $this->ajax(-105, '此项目聊天群已经存在');
        //判断群主是否已经注册聊天用户
        $chat_user = D('chat/user')->getChatUser(array('uid'=>$project_info['uid']));
        if(empty($chat_user))  $this->registerChatUsers($project_info['uid']);

        //创建群组（可同时添加组成员）
        $groupInfo = array(
            "groupname" => $project_info['name'], //群组名称, 此属性为必须的
            "desc" => $project_info['oneword'], //群组描述, 此属性为必须的
            "public" => $grouptype == '1' ? true : false, //是否是公开群, 此属性为必须的,为false时为私有群
            "maxusers" => 1000, //群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
            //"approval" => false, //（加此属性报错）加入公开群是否需要批准, 默认值是false（加群不需要群主批准）, 此属性为可选的,只作用于公开群
            "owner" => $project_info['uid'], //群组的管理员, 此属性为必须的
            //"members" => array("112","113") //群组成员,此属性为可选的,但是如果加了此项,数组元素至少一个（注：群主jma1不需要写入到members里面）
        );
        $extra_info = array(
            'project_id' => $project_info['id'],
        );
        $groupInfo = array_merge($groupInfo, $extra_info);
        $result = $this->chat->createGroup($groupInfo);
        if($result['duration']){
            $this->ajax(-101, '创建成功',$result);
        }
        $this->ajax(-102, '创建失败');
    }

    /*
	 * 获取群组详情
	 * @return array
	 * @author Baijiansheng
	 */
    public function actionGetGroupInfo(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];
        $groupid = $this->_request('groupid') ? trim($this->_request('groupid')) : 0;
        if(!$uid || !$groupid) $this->ajax(-103, '参数获取失败，请稍后再试');

        $cachename = 'app_chat_GetGroupInfo_' . $uid . $groupid;
        $data = S($cachename);
        if(!empty($data)) $this->ajax(-101, '获取成功', $data);

        $arr_url = C('url');
        //判断用户是否为人人投会员
        $user_info = D('user')->getInfo($uid);
        //判断此用户是否已经注册环信
        $chat_user = D('chat/user')->getChatUser(array('uid'=>$uid));
        if(empty($user_info) || empty($chat_user))  $this->ajax(-104, '对不起！您没有权限查看群组用户信息');

        //获取群组用户
        //$data = D('chat/group')->getChatGroup(array('groupid'=>$groupid));
        $condition = array('groupid' => $groupid);
        $cacheKey = 'app_model_chat_group_getChatGroup_' . helper_cache::makeKey($condition);
        $callback = array(D('chat/group'), 'getChatGroup');
        $data = helper_cache::getSmartCache($cacheKey, $callback, 3, array($condition));
        if($data){
            $user = (array)json_decode($data['affiliations']);
            $owner[] = $user['owner'];
            if($user['members']) $affiliations_users = array_merge($owner,$user['members']);
            else $affiliations_users = $owner;
            $project_img = D('project')->getInfo($data['project_id'], 'img_cover');
            $data['img_logo'] = helper_tool::getThumbImg($project_img, 300, 200);
            $owner_face = D('user')->getInfo($user['owner'],'face');
            $data['face'] = helper_tool::getThumbImg($owner_face, 100, 100);
            foreach($affiliations_users as $key=>$val){
                $userinfo = D('user')->getInfo($val);
                $chatuser = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid,'uid'=>$val));
                $city = D('area')->id2name($userinfo['city']);
                $area = D('area')->id2name($userinfo['area']);
                $data['users'][$key]['uid'] = $chatuser['uid'] ? $chatuser['uid'] : "";
                $data['users'][$key]['nickname'] = $chatuser['nickname'] ? $chatuser['nickname'] : "";
                $data['users'][$key]['sex'] = $userinfo['sex'];
                $data['users'][$key]['usertype'] = $chatuser['usertype'];
                $face = D('user')->getInfo($chatuser['uid'],'face');
                $data['users'][$key]['face'] = $face ? $arr_url['img2'].$face : '';
                $data['users'][$key]['area'] = $city.$area;
                unset($userinfo);
                unset($chatuser);
            }
            S($cachename, $data, 3);
            $this->ajax(-101, '获取成功', $data);
        }

        $this->ajax(-102, '暂无相关信息');
    }

    /*
	 * 加入聊天群组（公开群 = 预热开始项目（用户申请加入），私有群 = 融资完成项目（后台处理自动加入该项目的所有投资人））
	 * @return array
	 * @author Baijiansheng
	 */
    public function actionAddGroupUsers(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];
        $groupid = $this->_request('groupid') ? (string) $this->_request('groupid') : '';
        if(!$uid || !$groupid) $this->ajax(-103, '暂未开通讨论组');

        $arr_url = C('url');

        //判断用户是否为人人投会员
        $user_info = D('user')->getInfo($uid);
        if(empty($user_info)) $this->ajax(-104, '您所注册的用户不是人人投会员');
        if($user_info['is_idcard'] < 2) $this->ajax(-108, '您还不是认证投资人');
        //判断此用户加入的群是否为股东群
        $chat_group = D('chat/group')->getChatGroup(array('groupid'=>$groupid));
        if($chat_group['grouptype'] == 2) $this->ajax(-105, '您没有加入该聊天群的权限');
        //if($chat_group['owner'] == $uid) $this->ajax(-107, '对不起！您已经是该群组管理员，不能重复加入');
        //判断此用户是否已经加入该聊天群
        $chat_user = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid, 'uid'=>$uid));
        if($chat_user['status'] == 2) $this->ajax(-107, '您已经被加入黑名单');
        if(!empty($chat_user)) $this->ajax(-106, '您已经加入该聊天群，不能重复加入');

        //判断此用户是否已经注册环信
        $chat_user = D('chat/user')->getChatUser(array('uid'=>$uid));
        if(empty($chat_user))  $this->registerChatUsers($uid);

        $usernames = array((string)$uid);
        $result = $this->chat->addGroupUsers($groupid, $usernames);
        if($result['duration']){
            $users = array();
            $group_info = D('chat/group')->getChatGroup(array('groupid'=>$groupid));
            if($group_info['member']) $users = json_decode($group_info['member']);
            $users = array_values(array_unique(array_merge($users, $usernames)));
            //更新群组
            $save_data['member'] = $users ? json_encode($users) : '';
            $save_data['affiliations'] = json_encode(array('owner'=>$chat_group['owner'], 'members'=>$users));
            $save_data['affiliations_count'] = count($users) + 1;
            M('chat_group')->where(array('groupid'=>$groupid))->data($save_data)->save();

            //返回数据
            $city = D('area')->id2name($user_info['city']);
            $area = D('area')->id2name($user_info['area']);
            $data['nickname'] = $user_info['nickname'];
            $face = D('user')->getInfo($uid,'face');
            $data['face'] = $face ? $arr_url['img2'].$face : '';
            $data['area'] = $city.$area;
            $this->ajax(-101, '加入成功', $data);
        }
        $this->ajax(-102, '加入失败');
    }

    /*
	 * 获取群组用户
	 * @return array
	 * @author Baijiansheng
	 */
    public function actionGetGroupUsers(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];
        $groupid = $this->_request('groupid') ? (string) $this->_request('groupid') : '';
        if(!$uid || !$groupid) $this->ajax(-103, '参数获取失败，请稍后再试');

        $cachename = 'app_chat_GetGroupUsers_' . $uid . $groupid;
        $data = S($cachename);
        if(!empty($data)) $this->ajax(-101, '获取成功', $data);
        $arr_url = C('url');
        //判断用户是否为人人投会员
        $user_info = D('user')->getInfo($uid);
        //判断此用户是否已经注册环信
        $chat_user = D('chat/user')->getChatUser(array('uid'=>$uid));
        if(empty($user_info) || empty($chat_user))  $this->ajax(-104, '对不起！您没有权限查看群组用户信息');

        //获取群组用户
        $result = D('chat/groupUser')->getChatGroupUsers(array('groupid'=>$groupid, 'status'=>1), 0, 1000, 'uid,nickname', 'id ASC');
        if($result){
            foreach($result as $key=>$val){
                $userinfo = D('user')->getInfo($val['uid']);
                $city = D('area')->id2name($userinfo['city']);
                $area = D('area')->id2name($userinfo['area']);
                $data[$key]['uid'] = $val['uid'];
                $data[$key]['nickname'] = $val['nickname'];
                $face = D('user')->getInfo($val['uid'],'face');
                $data[$key]['face'] = $face ? $arr_url['img2'].$face : '';
                $data[$key]['area'] = $city.$area;
            }
            S($cachename, $data, 3);
            $this->ajax(-101, '获取成功', $data);
        }
        $this->ajax(-102, '暂无相关信息');
    }

    /*
    * 获取群组列表
    * @return array
    * @author Baijiansheng
    */
    public function actionGetGroupList(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];

        $page = $this->getPageCondition();

        $arr_url = C('url');
        //搜索条件
        $grouptype = $this->_request('grouptype') ? intval($this->_request('grouptype')) : 0;
        $status = $this->_request('status') ? intval($this->_request('status')) : 0;
        //will newly
        $order = $this->_request('order') ? trim($this->_request('order')) : '';
        if(!$grouptype && !$status) $this->ajax(-103, '参数获取失败，请稍后再试');
        if($grouptype == '1'){
            $page['start_num'] = 0;
            $page['page_num'] = 500;
            if($page['page'] >= 2) $this->ajax(-102, '暂无相关信息');
        }
        //获取缓存数据
        $cache = cache::getInstance('redis');
        $cacheKey = "app_GetGroupList_cache_{$uid}{$grouptype}{$status}{$order}{$page['start_num']}{$page['page_num']}";
        $result = $cache->get($cacheKey);
        if(!empty($result)) $this->ajax(-101, '获取成功', $result);

        //排序
        if($order == 'newly') $orderby = ' order by g.add_time desc';
        if($order == 'will'){
            $fields = ',p.finance_amount * 100 / p.finance_total as invest_progress';
            $orderby = ' order by invest_progress desc';
        }

        //获取群组
        $fields = 'g.groupid,g.project_id,g.name,g.descscription,g.grouptype'.$fields;
        if($grouptype){
            if($grouptype != -1) $sql = ' and g.grouptype='.$grouptype;
            //$result = D('chat/group')->getChatGroups(array('grouptype'=>$grouptype), $page['start_num'], $page['page_num'], $fields);
            $result = M()->query('select '.$fields.' from chat_group as g left join chat_group_user as u on g.groupid=u.groupid where u.uid='.$uid.' and u.status=1'.$sql.' group by groupid limit '.$page['start_num'].','.$page['page_num']);
        }else{
            $where_sql = ' g.grouptype=1 and p.is_show=1 ';
            if($status != '-1')  $where_sql = $where_sql.' and p.status='.$status;
            if($status == 3)  $where_sql = ' g.grouptype=3 ';

            //获取加入的群组id
            $groupuser = M('chat_group_user')->field('groupid')->where(array('uid'=>$uid, 'status'=>1))->select();;
            if($groupuser){
                foreach($groupuser as $val){
                    $groupids[] = $val['groupid'];
                }
                $where_sql = $where_sql.' and g.groupid NOT IN('.implode(',', $groupids).') ';
            }
            $result = M()->query('select '.$fields.' from chat_group as g left join project as p on g.project_id=p.id where '.$where_sql.' group by groupid '.$orderby.' limit '.$page['start_num'].','.$page['page_num']);
        }
        if(!empty($result)){
            foreach($result as $key=>$val){
                $img_logo = D('project')->getInfo($val['project_id'],'img_cover');
                $result[$key]['img_logo'] = helper_tool::getThumbImg($img_logo, 300, 200);
                if($val['grouptype'] == 2) $grouptype = '股东群';
                else $grouptype = '讨论组';
                $result[$key]['name'] = $val['name'].' - '.$grouptype;
                unset($grouptype);
            }
            // 页面数据缓存
            $cache->set($cacheKey, $result, 5);
            $this->ajax(-101, '获取成功', $result);
        }
        $this->ajax(-102, '暂无相关信息');
    }

    /*
    * 修改群组信息
    * @return array
    * @author Baijiansheng
    */
    public function actionUpdateGroupInfo(){
        $this->checkLogin();

        $groupid = $this->_request('groupid') ? (string) $this->_request('groupid') : '';
        //群组描述
        $desc = $this->_request('descscription') ? trim($this->_request('descscription')) : '';
        if(!$groupid || !$desc) $this->ajax(-103, '参数获取失败，请重试');

        $group = D('chat/group')->getChatGroup(array('groupid'=>$groupid));
        if($group['owner'] != $this->_userinfo['uid']) $this->ajax(-104, '您没有修改权限');

        $result = $this->chat->updateGroup($groupid, array('description'=>$desc));
        if($result['duration']){
            $this->ajax(-101, '修改成功');
        }
        $this->ajax(-102, '修改失败');
    }

    /*
    * 退出群组
    * @return array
    * @author Baijiansheng
    */
    public function actionExitGroup(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];
        $groupid = $this->_request('groupid') ? trim($this->_request('groupid')) : 0;
        if(!$groupid) $this->ajax(-103, '参数获取失败，请重试');

        $chat_group = D('chat/group')->getChatGroup(array('groupid'=>$groupid));
        if($chat_group['grouptype'] == 2) $this->ajax(-105, '对不起！您没有权限退出股东群');
        //判断此用户是否在该群组
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('uid'=>$uid, 'groupid'=>$groupid));
        if(empty($chat_group_user)) $this->ajax(-104, '对不起！您还没有加入过此聊天群或者该群组不存在');
        if($chat_group_user['usertype'] == '1') $this->ajax(-105, '对不起！您没有退出操作权限');

        $result = $this->chat->deleteGroupUser($groupid, $uid);
        if($result['duration']){
            $this->ajax(-101, '已退出');
        }
        $this->ajax(-102, '退出失败');
    }

    /*
   * 群主移除群组用户（群主为项目方）
   * @return array
   * @author Baijiansheng
   */
    public function actionDeleteGroupUser(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];
        $touid = $this->_request('uid') ? intval($this->_request('uid')) : 0;
        $groupid = $this->_request('groupid') ? (string) $this->_request('groupid') : '';
        if(!$touid || !$groupid) $this->ajax(-103, '参数获取失败，请重试');
        if($uid == $touid) $this->ajax(-106, '您没有移除该用户的权限');

        //判断登录用户是否有移除群组用户权限（判断是否为项目方）
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid,'uid'=>$uid,'usertype'=>1));
        if(empty($chat_group_user)) $this->ajax(-104, '您没有移除操作权限');

        //判断移除用户是否在该群组
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid,'uid'=>$touid));
        if(empty($chat_group_user)) $this->ajax(-105, '此用户不在该聊天群');

        $result = $this->chat->deleteGroupUser($groupid, $touid, true);
        if($result['duration']){
            $this->ajax(-101, '已移除');
        }
        $this->ajax(-102, '移除失败');
    }

    /*
   * 将群组用户加入黑名单（群主操作）
   * @return array
   * @author Baijiansheng
   */
    public function actionAddBlocksUser(){
        $this->checkLogin();

        $uid = $this->_userinfo['uid'];
        $touid = $this->_request('uid') ? intval($this->_request('uid')) : 0;
        $groupid = $this->_request('groupid') ? (string) $this->_request('groupid') : '';
        if(!$touid || !$groupid) $this->ajax(-103, '参数获取失败，请重试');
        if($uid == $touid) $this->ajax(-106, '您没有加入黑名单权限');

        //判断登录用户是否有移除群组用户权限（判断是否为群主/项目方）
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid,'uid'=>$uid,'usertype'=>1));
        if(empty($chat_group_user)) $this->ajax(-104, '您没有将成员加入黑名单权限');

        //判断加入黑名单用户是否在该群组
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid,'uid'=>$touid));
        if(empty($chat_group_user)) $this->ajax(-105, '此用户不在该聊天群');

        $result = $this->chat->addBlocksUser($groupid, $touid);
        if($result['duration']){
            $this->ajax(-101, '已加入黑名单');
        }
        $this->ajax(-102, '加入黑名单失败');
    }

    /*
    * 屏蔽群组用户消息（环信无接口）
    * @return array
    * @author Baijiansheng
    */
    public function actionShieldingGroupUser(){
        $this->checkLogin();

        $uid = $this->_request('uid') ? intval($this->_request('uid')) : 0;
        $groupid = $this->_request('groupid') ? (string) $this->_request('groupid') : '';
        //群组描述
        if(!$uid || !$groupid) $this->ajax(-103, '参数获取失败，请重试');

        //判断登录用户是否有移除群组用户权限（判断是否为项目方）
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('groupid'=>$groupid));
        $project_info = D('project')->getInfo($chat_group_user['project_id']);
        if($this->_userinfo['uid'] != $project_info) $this->ajax(-104, '对不起！您没有该操作权限');

        //判断此用户是否在该群组
        $chat_group_user = D('chat/groupUser')->getChatGroupUser(array('uid'=>$uid));
        if(empty($chat_group_user)) $this->ajax(-104, '对不起！此用户不在该聊天群或已被删除');

        $result = $this->chat->deleteGroupUser($groupid, $uid);
        if($result['duration']){
            $this->ajax(-101, '已移除');
        }
        $this->ajax(-102, '移除失败');
    }

    /*
    * 发送聊天信息（客户端发送，后台导出）
    * @return array
    * @author Baijiansheng
    */
    public function actionSendGroupMsg(){

    }

    /*
    * 获取聊天组搜索条件
    * @param $id  int 项目ID
    * @return array
    * @author Baijiansheng
    */
    public function actionGetSearchList(){
        $status = $this->_post('status') ? intval($this->_post('status')) : 0;
        $order_name = '默认排序';
        //状态
        $arr_status = array(
            '0'=>array($order_name,'will'=>'将要完成','newly'=>'最新融资创建'),
            '2'=>array($order_name,'will'=>'将要完成','newly'=>'最新融资创建'),
            '3'=>array(),
            '4'=>array($order_name,'will'=>'将要完成','newly'=>'最新融资创建'),
        );
        $list['order'] = $arr_status[$status];
        $list['search']  = array('-1'=>'所有可加入讨论组','4'=>'融资中项目讨论组','2'=>'预热中项目讨论组','3'=>'自由讨论组');
        if($list){
            $flag = 0;
            foreach($list['order'] as $key=>$val){
                $data['order'][$flag]['id'] = $key;
                $data['order'][$flag]['name'] = $val;
                $flag++;
            }
            $flag = 0;
            foreach($list['search'] as $key=>$val){
                $data['search'][$flag]['id'] = $key;
                $data['search'][$flag]['name'] = $val;
                $flag++;
            }
            $this->ajax('-101','获取成功',$data);
        }
        $this->ajax('-102','暂无相关分类');
    }

    /*
    * 修改IM用户密码
    * $password mix  String
    *  demo: $password = 123456
    */
    public function actionSetHxPassword(){
        $this->checkLogin();
        $result = D('user')->updateHxPassword($this->_userinfo['uid']);
        if($result['duration']) $this->ajax('-101','修改成功');
        else $this->ajax('-102','修改失败');
    }

    /**
     * 获取搜索条件
     * @author Baijianshneg
     */
    private function getPageCondition(){
        $data['page'] = $this->_post('page') ? intval($this->_post('page')) : 1;
        $data['page_num'] = $this->_post('page_num') ? intval($this->_post('page_num')) : 100;
        $data['start_num'] = ($data['page'] - 1) * $data['page_num'];

        return $data;
    }

}
