<?php
/*
 * --------------强调说明-------------
 * 参数 数字int 最好填 String 如groupId 1423734662380237 ,传参时传getGroupDetial("1423734662380237");
 */
class helper_hxchat
{
    private $host = 'https://a1.easemob.com';
    private $client_id;
    private $client_secret;
    private $org_name;
    private $app_name;
    private $token;
    private $userAccessToken;
    /*
     * ------------------------
     * 公共方法             开始
     * ------------------------
     */
    /**
     * 初始化参数
     *
     * @param $client_id
     * @param $client_secret
     * @param $org_name
     * @param $app_name
     */
    public function __construct() {
        $huanxin = C('huanxin');
        $this->client_id = $huanxin['client_id'];
        $this->client_secret = $huanxin['client_secret'];
        $this->org_name = $huanxin['org_name'];
        $this->app_name = $huanxin['app_name'];
        //服务端获取
        $this->token = '';
    }
    private function request($api_name, $data, $method='POST')
    {
        //$data = array("name" => "Hagrid", "age" => "36");
        if(isset($data)){
            $data_string = json_encode($data);
        }//echo $this->host . "/$this->org_name/$this->app_name/".$api_name."\n";
        $ch = curl_init($this->host . "/$this->org_name/$this->app_name/".$api_name);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if(strtoupper($method)!='GET'){

            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        }
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            //'Accept: application/json',
            'Authorization: Bearer '.$this->getToken()
            // 'Content-Length: ' . strlen($data_string)
        )   );
        $result = curl_exec($ch);
        $result =  json_decode($result, true);
        curl_close($ch);
        return $result;
    }

    /*
     * 取得TOKEN
     */
    public function getToken($reGet=false)
    {
        if(!$this->token || $reGet == true){
            $path = "/$this->org_name/$this->app_name/token";
            $data = array(
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret
            );
            $data_string = json_encode($data);

            $ch = curl_init($this->host . $path);
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt ($ch, CURLOPT_POSTFIELDS,$data_string);
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
            curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            $result = curl_exec($ch);
            $result_arr = json_decode($result, true);
            if(isset($result_arr['error'])){
                echo $result;exit;
            }else{
                $this->token = $result_arr['access_token'];
            }
            return $this->token;
        }else{
            return $this->token;
        }
    }
    

    /*
     * 取得用户登录TOKEN
     */
    public function getUserAccessToken($uid, $password, $reGet=false)
    {
        if(!$this->userAccessToken || $reGet == true){
            $path = "/$this->org_name/$this->app_name/token";
            $data = array(
                'grant_type' => 'password',
                'username' => $uid,
                'password' => $password
            );
            $data_string = json_encode($data);

            $ch = curl_init($this->host . $path);
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt ($ch, CURLOPT_POSTFIELDS,$data_string);
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
            curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            $result = curl_exec($ch);
            $result_arr = json_decode($result, true);
            if(isset($result_arr['error'])){
                echo $result;exit;
            }else{
                $this->userAccessToken = $result_arr['access_token'];
            }
            return $this->userAccessToken;
        }else{
            return $this->userAccessToken;
        }
    }

    /*
     * ------------------------
     * 公共方法             结束
     * ------------------------
     */

    /*
     *
     * $userInfo Array 群信息参数如下;
    "username":"jliu", //用户名称, 此属性为必须的
    "password":"123456", //用户密码, 此属性为必须的
    "public":true, //是否是公开群, 此属性为必须的
    "maxusers":300, //群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
    "approval":true, //加入公开群是否需要批准, 没有这个属性的话默认是true（不需要群主批准，直接加入）, 此属性为可选的
    "nickname":"建国", //用户昵称, 此属性为可选
     * demo:
    * $userInfo = array(
                'username' => 'jliu',
                'password' => '123456',
                'nickname' => ''
    );
     */
    /*
    * 创建用户
    *  @param $userInfo 必填 用户信息 array
    */
    public function createUser($userInfo)
    {
        $result =  $this->request('users', $userInfo, 'POST');
        return $result;
    }
    /*
    * 获取用户信息
    * @param $username 必填 用户ID Stirng
    */
    public function getUsers($username = '', $where = ''){
        if($username) $url = 'users/'.$username;
        else $url = 'users'.$where;
        $result = $this->request($url, '', 'GET');
        return $result;
    }
    /*
    * 修改用户昵称
    *  @param $username 必填 用户名 String
    *  @param $userInfo 必填 用户信息 array
    */
    public function updateUser($username, $userInfo)
    {
        $result =  $this->request('users/'.$username, $userInfo, 'PUT');
        return $result;
    }
    /*
     * ------------------------------------
     * 群组方法             开始
     * ------------------------------------
     */
    /*
     * 获取app中所有的群组
     */
    public function getGroupList()
    {
        $result =  $this->request('chatgroups', '','GET');
        return $result;
    }
    /*
    * 获取一个或者多个群组的详情
    * $groupList mix  String or Array
    *  demo: $groupList = array('1423734662380237', '1423734662380238)
    */
    public function getGroupDetial($groupList)
    {

        if(gettype($groupList) == 'array'){
            $group_list = implode(',', $groupList);
        }else{
            $group_list = $groupList;
        }

        $result =  $this->request('chatgroups'.'/'.$group_list, '','GET');
        return $result;
    }
    /*
     *
     * $groupInfo Array 群信息参数如下;
    "groupname":"testrestgrp12", //群组名称, 此属性为必须的
    "desc":"server create group", //群组描述, 此属性为必须的
    "public":true, //是否是公开群, 此属性为必须的
    "maxusers":300, //群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
    "approval":true, //加入公开群是否需要批准, 没有这个属性的话默认是true（不需要群主批准，直接加入）, 此属性为可选的
    "owner":"jma1", //群组的管理员, 此属性为必须的
    "members":["jma2","jma3"] //群组成员,此属性为可选的,但是如果加了此项,数组元素至少一个（注：群主jma1不需要写入到members里面）
     * demo:
    * $groupInfo = array(
                'groupname' => 'leee',
                'desc'       => 'leeff',
                'owner' => 'sy1'
    );
     */
    public function createGroup($groupInfo)
    {
        $groupInfo['public'] = isset($groupInfo['public']) ? $groupInfo['public'] : true;       //默认公开
        $groupInfo['approval'] = isset($groupInfo['approval']) ? $groupInfo['maxusers'] : false;//默认需要审核

        $result =  $this->request('chatgroups', $groupInfo, 'POST');
        return $result;
    }

    /*
     * 更新群组信息
     * @param $groupId int 群组id  必填
     * $param $groupInfo array 群组信息 必填
     * 参数说明：
     * $groupInfo = array( "groupname":"testrestgrp12", //群组名称 可选
        "description":"update groupinfo", //群组描述 可选
        "maxusers":300, //群组成员最大数(包括群主), 值为数值类型 可选
      )
     */

    public function updateGroup($groupId, $groupInfo=array())
    {
        $result =  $this->request('chatgroups'.'/'.$groupId, $groupInfo ,'PUT');
        return $result;
    }
    /*
     * 群组删除
     * @param $groupId 必填 群组ID Stirng
     */
    public function deleteGroup($groupId){
        $result = $this->request('chatgroups'.'/'.$groupId,'', 'DELETE');
        return $result;
    }
    /*
    * 获取群组用户
    * @param $groupId 必填 群组ID Stirng
    */
    public function getGroupUsers($groupId){
        $result = $this->request('chatgroups'.'/'.$groupId.'/users','', 'GET');
        return $result;
    }
    /*
     * 群组批量加人
     * @param $groupId 必填 群组ID Stirng
     * @param $users 必填    用户名  mix(String,array)
     */
    public function addGroupUsers($groupId, $users)
    {
        if(gettype($users) != 'array'){
            $users[] = $users;
        }
        $data['usernames'] = $users;
        $result = $this->request('chatgroups'.'/'.$groupId.'/users', $data, 'POST');
        return $result;
    }

    /*
     * 获取一个或者多个群组的详情
     * @param $groupId 必填 群组ID array
     */
    public function getGroupInfo($groupId)
    {
        $result = $this->request('chatgroups'.'/'.$groupId, '', 'POST');
        return $result;
    }

    /*
     * 群组减人：从群中移除某个成员。
     * @param $groupId 群组id 必填 String
     * @param $user 用户名 必填 String
     */
    public function deleteGroupUser($groupId, $user)
    {
        $result = $this->request('chatgroups'.'/'.$groupId.'/users/'.$user, '', 'DELETE');
        return $result;
    }
    /*
     * 群组黑名单添加：将群成员加入黑名单。(单个)
     * @param $groupId 群组id 必填 String
     * @param $user 用户名 必填 String
     */
    public function addGroupBlocksUser($groupId, $user)
    {
        $result = $this->request('chatgroups/'.$groupId.'/blocks/users/'.$user, '', 'POST');
        return $result;
    }
    /*
     * 群组黑名单删除：将群成员从黑名单中删除。(单个)
     * @param $groupId 群组id 必填 String
     * @param $user 用户名 必填 String
     */
    public function deleteGroupBlocksUser($groupId, $user)
    {
        $result = $this->request('chatgroups/'.$groupId.'/blocks/users/'.$user, '', 'DELETE');
        return $result;
    }
    /*
     * 获取一个用户参与的所有群组
     * $user String 用户名 必填
     */
    public function getUserGroups($user){
        $result = $this->request('users/'.$user.'/joined_chatgroups', '', 'GET');
        return $result;
    }

    /*
     * 删除用户（单个）
     * $user String 用户名 必填
     */
    public function deleteUser($user){
        $result = $this->request('users/'.$user, '', 'DELETE');
        return $result;
    }
    /*
    * 账号禁用
    *  @param $username 必填 用户名
    */
    public function deactivateUser($username)
    {
        $result = $this->request('users/'.$username.'/deactivate', '');
        return $result;
    }

    /*
    * 用户账号解禁
    *  @param $username 必填 用户名
    */
    public function activateUser($username)
    {
        $result = $this->request('users/'.$username.'/activate', '');
        return $result;
    }

    /*
    * 移除群组成员[单个]
    * @param $group_id 必填 群组id
    * @param $uid 必填 用户名
    */
    public function removeGroupUser($group_id, $uid)
    {
        $result = $this->request('chatgroups/'.$group_id.'/users/'.$uid, '', 'DELETE');
        return $result;
    }

    /*
    * 群组转让（非当前群成员也可以群组转让，黑名单用户转成群主后，会自动解除黑名单，转让后原来群主变成该群普通用户）
    * @param $group_id 必填 群组id
    * @param $userInfo 必填  {"newowner":"username1"}
    */
    public function groupTransfer($group_id, $userInfo)
    {
        $result = $this->request('chatgroups/'.$group_id, $userInfo, 'PUT');
        return $result;
    }

    /*
     * ------------------------------------
     * 群组方法             结束
     * ------------------------------------
     */

    /*
    * 导出聊天数据
    * $user String 用户名 必填
    */
    public function exportChatMsg($where = ''){
        $result = $this->request('chatmessages'.$where, '', 'GET');
        return $result;
    }

    /*
    * 发送聊天信息
    * $msgInfo array 发送的消息的所有属性内容 必填
    * $msgInfo = {"target_type" : "users","target" : ["stliu1", "jma3", "stliu", "jma4"],"msg" : {"type" : "txt","msg" : "hello from rest"},"from" : "jma2", "ext" : {"attr1" : "v1","attr2" : "v2"} }
    */
    public function sendChatMsg($msgInfo){
        $result = $this->request('messages', $msgInfo, 'POST');
        return $result;
    }

    /*
    * 修改IM用户密码
    * $password mix  String
    *  demo: $password = array('newpassword'=>123456)
    */
    public function updatePassword($username, $password)
    {
        $result = $this->request('users/'.$username.'/password', $password, 'PUT');
        return $result;
    }

    /*
    * 发送群组透传消息
    * $param int $uid 用户ID
    * $param string $groupId 群组ID
    * $param string $type 标识符
    */
    public function sendCmd($uid, $groupId, $type)
    {
        $condition = array('groupid' => $groupId);
        $group = D('chat/group')->getChatGroup($condition);
        $info = array(
            'target_type' => 'chatgroups',
            'target' => array($groupId),
            'msg' => array('type' => 'cmd', 'action' => $type),
            'from' => $groupId,
            'ext' => array('user_id' => $uid, 'group_name' => $group['name'])
        );
        $result = $this->request('messages', $info, 'POST');
        $this->sendCmdToUsers($uid, $groupId, $type);
        return $result;
    }

    /*
    * 发送用户透传消息
    * $param int $uid 用户ID
    * $param string $groupId 群组ID
    * $param string $type 标识符
    */
    public function sendCmdToUsers($uid, $groupId, $type)
    {
        $condition = array('groupid' => $groupId);
        $group = D('chat/group')->getChatGroup($condition);
        $info = array(
            'target_type' => 'users',
            'target' => array($uid),
            'msg' => array('type' => 'cmd', 'action' => $type),
            'from' => $groupId,
            'ext' => array('user_id' => $uid, 'group_name' => $group['name'])
        );
        $result = $this->request('messages', $info, 'POST');
        return $result;
    }
}