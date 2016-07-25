<?php

/**
 * 财务插件接口上传
 */
class controller_api_software extends controller_api_abstract {

    private $request;
    private $response;
    private $softwareinfo; //当前财务软件信息
    private $signKey = 'a9e925c17555bc3b05866c2d679f89cc';
    private $apiSite;
    private static $oparationVerifyMobile = 16;//安装验证手机号
    private static $oparationVerifyActivationCode = 17;//验证激活码是否可用
    private static $statusCodeSuccess = 1;
    private static $statusCodeError = -5;

    /**
     * 初始化默认返回值
     */
    public function __construct() {
        $this->request = array();
        $this->apiSite = "http://api.caiwu.renrentou.com";
        $this->response = array(
            'status' => 0,
            'msg' => '',
        );
    }

    public function actionIndex() {
        exit();
    }

    /**
     * 测试
     */
    public function actionTest() {
        print_r($_POST);
        $info = file_get_contents("php://input");
        //$rs = file_put_contents(DATA_PATH.'/log/1.txt', $info, FILE_APPEND);     
        $info = json_decode(trim($info), true);
        print_r($info);
        return $this->decodeRequest($info);
    }

    /**
     * 网络联通性测试
     */
    public function actionPing() {
        $this->actionObtainSoftwareOparationList();
    }

    //检查更新包 开放接口不检查加密
    public function actionUpdate() {
        $this->response['status'] = 1;
        $this->response['msg'] = 'success';
        $this->response['data'] = array(
            'version' => '1.0',
            'url' => 'http://www.dev2.renrentou.com/s/download/finance/rrtcw_v1.0.exe',
        );
        $this->sendResponse();
    }

    /*
     * 修改软件客户端更新
     * @author tianxiang
     * @return jsonData
     */

    public function actionClientUpdate() {
        if ($this->getRequestData()) {
            //接受参数
            $request = $this->request;
            $id = $request['id'];
            $qid = $request['qid'];

            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 11) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }

            if (empty($id) || !isset($qid) || empty($typeId) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '修改失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            
            //验证签名
            $row = D('finance/softwareClient')->getInfo($id);
            $statusA = $this->signVerify($request,$row['token']);
            if (empty($statusA)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }

            $info = D('finance/softwareClient')->getInfo($id);
            //客户端主表 进行比对
            $data = array();
            $sort_ver_id = $this->request['sort_ver_id'];
            if ($info['sort_ver_id'] != $sort_ver_id) {
                $data['sort_ver_id'] = $sort_ver_id;
            }
            //分店号
            $subbranch = $this->request['subbranch'];
            if ($info['subbranch'] != $subbranch) {
                $data['subbranch'] = $subbranch;
            }
            
            //店名
            $company = $this->request['company'];
            if ($info['company'] != $company) {
                $data['company'] = $company;
            }

            $data['update_time'] = time();
            $data['install_num'] = $info['install_num'] + 1;
            $status = D('finance/softwareClient')->update($id, $data);

            $dataQ = array();
            $qinfo = D('finance/softwareClientQueryconfig')->getInfo($id);
            //客户端查询配置表
            $dbtype = $this->request['dbtype'];
            if ($dbtype != $qinfo['db_type']) {
                $dataQ['db_type'] = $dbtype;
            }
            $dbhost = $this->request['dbhost'];
            if ($dbhost != $qinfo['db_host']) {
                $dataQ['db_host'] = $dbhost;
            }
            $dbusername = $this->request['dbusername'];
            if ($dbusername != $qinfo['db_username']) {
                $dataQ['db_username'] = $dbusername;
            }
            $dbpwd = $this->request['dbpwd'];
            if ($dbpwd != $qinfo['db_pwd']) {
                $dataQ['db_pwd'] = $dbpwd;
            }
            $dbaddress = $this->request['dbaddress'];
            if ($dbaddress != $qinfo['db_address']) {
                $dataQ['db_address'] = $dbaddress;
            }
            $dbname = $this->request['dbname'];
            if ($dbname != $qinfo['db_name']) {
                $dataQ['db_name'] = $dbname;
            }
            $dbsql = $this->request['dbsql'];
            if ($dbsql != $qinfo['db_sql']) {
                $dataQ['db_sql'] = $dbsql;
            }
            $qu_type = $this->request['qu_type'];
            if ($qu_type != $qinfo['qu_type']) {
                $dataQ['qu_type'] = $qu_type;
            }

            $qu_frequency = $this->request['qu_frequency'];
            if ($qu_frequency != $qinfo['qu_frequency']) {
                $dataQ['qu_frequency'] = $qu_frequency;
            }
            $dataQ['update_time'] = time();

            $statusQ = D('finance/softwareClientQueryconfig')->update($qid, $dataQ);
            if (!empty($status) || !empty($statusQ)) {
                $this->response['status'] = 1;
                $this->response['msg'] = '修改成功';
            } else {
                $this->response['status'] = -1;
                $this->response['msg'] = '修改失败';
            }
        }
        $this->sendResponse();
    }

    /*
     * 获取软件客户端配置
     * @author tianxiang
     * @return jsonData
     */

    public function actionObtainClientConfig() {
        if ($this->getRequestData()) {
            $request = $this->request;
            $id = $request['id'];

            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 13) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }

            if (empty($id) || empty($typeId) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            //取客户端软件查询配置
            $result = D('finance/softwareClient')->getInfo($id);
            if (empty($result)) {
                $this->response['status'] = -3;
                $this->response['msg'] = '客户端软件配置不存在';
                $this->sendResponse();
                exit;
            }
            //验证签名
            $statusA = $this->signVerify($request);
            if (empty($statusA)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }

            if (!empty($result)) {
                $data['id'] = $result['id'];
                $data['code'] = $result['code'];
                $data['mobile'] = $result['mobile'];
                $data['uid'] = $result['uid'];
                $data['token'] = $result['token'];
                $data['identification'] = $result['identification'];
                $data['company'] = $result['company'];
                $data['subbranch'] = $result['subbranch'];
                $data['status'] = $result['status'];
            }
            $this->setResponse($data);
            $this->response['status'] = 1;
            $this->response['msg'] = 'success';
        }
        $this->sendResponse();
    }

    /*
     * 获取软件客户端查询配置
     * @author tianxiang
     * @return jsonData
     */
    public function actionObtainClientQueryConfig() {
        if ($this->getRequestData()) {
            $request = $this->request;
            $id = $request['id'];

            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 12) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }

            if (empty($id) || empty($typeId) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            //验证签名
              //客户端查询配置
            $row = D('finance/softwareClient')->getInfo($id);     
            $statusA = $this->signVerify($request,$row['token']);
            if (empty($statusA)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }
            
            //客户端查询配置
            $result = D('finance/softwareClientQueryconfig')->getInfoByCid($id);
            if(!empty($result)){
                $data = array();
                foreach ($result as $key => $value) {
                if ($value['qu_type'] >= 1 && $value['qu_type'] <= 6) {
                         $dataA = $this->getQueryConfig($value);
                          if(!empty($dataA)){
                             $data = array_merge($data,$dataA);
                          }
                   }
                }
            }
            
            $this->setResponse($data);
            $this->response['status'] = 1;
            $this->response['msg'] = 'success';
        }
        $this->sendResponse();
    }
  
  /*
   * 获取查询配置按小时
   * @qinfo 查询配置信息
   * @author tianxiang
   * @return $data
   */
    private function getQueryConfig($qinfo) {
      $num = $qinfo['qu_num'];
      $qu_frequency = $qinfo['qu_frequency'];
      $qu_type = $qinfo['qu_type'];
      if($qu_type == 1){
          $diff = 1 * 60 * $qu_frequency; 
          $qu_frequency = 1;
        
      }
      if($qu_type == 2){
          $diff = 1 * 60 * 60;
       }elseif ($qu_type == 3) {
          $diff = 1 * 60 * 60 *24;  
       }
       elseif ($qu_type == 4) {
          $diff = 1 * 60 * 60 *24 * 7;  
       }
       elseif ($qu_type == 5) {
          $diff = 1 * 60 * 60 *24 * 7 * 30;  
       }else{
          $num = 1;
       }
       
       if($qu_type >=1 && $qu_type < 6){
            // $num = $qu_frequency * 6;
           $tt = substr(date('Y-m-d H:i'), 0, -1) . '0:00';
           $begintime = strtotime($tt) - $diff;
       }
      $data = array();
      for($i=1;$i<=$num;$i++){
            $qdata = array();
            $qdata['id'] = $qinfo['id'];
             if($qu_type == 6){
                preg_match_all("/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/", $qinfo['db_sql'], $timeArr);
                $qdata['begintime'] = $timeArr[0][0];
                $qdata['endtime'] = $timeArr[0][1];
            }else{
                $endtime= $begintime+$diff*$qu_frequency;
                $qdata['begintime'] = date('Y-m-d H:i'.':00', $begintime);
                $qdata['endtime'] = date('Y-m-d H:i'.':00',$endtime);
            }
            $qdata['qu_type'] = $qinfo['qu_type'];
            $qdata['dbtype'] = $qinfo['db_type'];
            $qdata['dbname'] = $qinfo['db_name'];
            $qdata['dbusername'] = $qinfo['db_username'];
            $qdata['dbpwd'] = $qinfo['db_pwd'];
            $qdata['dbaddress'] = $qinfo['db_address'];
            $qdata['frequency'] = $qinfo['qu_frequency'];
            $qdata['dbsql'] = D('finance/softwareClientQueryconfig')->getSqlTpl($qinfo['db_sql'], $qdata['begintime'], $qdata['endtime']);
            $begintime = strtotime($qdata['endtime']);
            $data[] = $qdata;
      }
      return $data;
  }
    /*
     * 财务软件客户端安装
     * @author tianxiang
     * @return jsonData
     */

    public function actionClientInstall() {
        if ($this->getRequestData()) {
            //接受参数
            $request = $this->request;

            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 10) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
            
            $sort_id = $this->request['sort_id'];
            $sort_ver_id = $this->request['sort_ver_id'];
            $sort_query_id = $this->request['sort_query_id'];
            
            $mobile = $this->request['mobile'];
            $subbranch = $this->request['subbranch'];
            $company = $this->request['company'];
            $identification = $this->request['identification'];
            //验证必须参数
            if (empty($sort_id) || empty($sort_ver_id)|| empty($sort_query_id) ||  empty($subbranch) || empty($company) || empty($identification) || empty($typeId) || empty($mobile) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '安装失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }

            //验证签名
            $status = $this->signVerify($request);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }
            
            //客户端安装主表
            $data = array();
            $data['sort_id'] = $sort_id;
            $data['sort_ver_id'] = $sort_ver_id;
            $data['subbranch'] = $subbranch;
            $token = sha1($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'].time() . rand());
            $data['token'] = $token;
            $data['mobile'] = $mobile;
            $data['company'] = $company;
            $data['identification'] = $identification;
            $data['add_time'] = time();
            $id = D('finance/softwareClient')->save($data);
            if (!empty($id)) {
                $info = D('finance/softwareVersionQueryConfig')->getInfo($sort_query_id);
                $qdata = array();
                //客户端查询配置表
                $qdata['cid'] = $id;
                $qdata['sort_query_id'] =  $sort_query_id;
                $qdata['db_type'] = $info['db_type'];
                $qdata['db_host'] = $info['db_host'];
                $qdata['db_username'] = $info['db_username'];
                $qdata['db_pwd'] = $info['db_pwd'];
                $qdata['db_address'] = $info['db_address'];
                $qdata['db_name'] = $info['db_name'];
                $qdata['db_sql'] = $info['db_sql'];
                $qdata['qu_type'] = $info['qu_type'];
                $qdata['qu_num'] = empty($info['qu_num']) ? $info['qu_num']:1;
                $qdata['status'] = 1;
                $qdata['add_time'] = time();
                $qdata['qu_frequency'] = $info['qu_frequency'];
                $qid = D('finance/softwareClientQueryconfig')->save($qdata);
                if (!empty($id) && !empty($qid)) {
                    $dataB['id'] = $id;
                    $dataB['token'] = $token;
                    $this->setResponse($dataB);
                    $this->response['status'] = 1;
                    $this->response['msg'] = '安装成功';
                } else {
                    $this->response['status'] = -1;
                    $this->response['msg'] = '安装失败';
                }
            } else {
                $this->response['status'] = -1;
                $this->response['msg'] = '安装失败';
            }
        }
        $this->sendResponse();
    }

    /*
     * 获取软件厂商列表
     * @author tianxiang
     * @return jsonData
     */

    public function actionObtainFactoryList() {
        if ($this->getRequestData()) {
            //接受参数
            $request = $this->request;

            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 8) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
            //验证必须请求参数
            if (empty($typeId) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            //验证签名
            $status = $this->signVerify($request);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }

            $lists = D('finance/software')->getSoftwareFactory();
            if (!empty($lists)) {
                foreach ($sflists as $key => $value) {
                    unset($tempArr);
                    $tempArr['id'] = $value['id'];
                    $tempArr['softname'] = $value['name'];
                    $tempArr['company'] = $value['company'];
                    $tempArr['status'] = $value['status'];
                    $softList[] = $tempArr;
                }
                $this->setResponse($lists);
                $this->response['status'] = 1;
                $this->response['msg'] = '获取列表成功';
            } else {
                $this->response['status'] = -1;
                $this->response['msg'] = '无数据';
            }
        }
        $this->sendResponse();
    }

    /*
     * 获取获取软件版本列表
     * @author tianxiang
     * @return jsonData
     */

    public function actionObtainSoftwareListById() {
        if ($this->getRequestData()) {
            //接受参数
            $request = $this->request;
            $id = $request['id'];

            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 5) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
            //验证必须请求参数
            if (empty($typeId) || empty($request_time) || empty($sign) || empty($id)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            //验证签名
            $status = $this->signVerify($request);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }

            $vlists = $this->getSoftwareListById($id);
            if (!empty($vlists)) {
                $this->setResponse($vlists);
                $this->response['status'] = 1;
                $this->response['msg'] = '获取列表成功';
            } else {
                $this->response['status'] = -1;
                $this->response['msg'] = '无数据';
            }
        }
        $this->sendResponse();
    }

    /*
     * 获取软件操作列表
     * @author tianxiang
     * @return jsonData
     */

    public function actionObtainSoftwareOparationList() {
        if ($this->getRequestData()) {
            
            //接受参数
            $request = $this->request;
            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];

            //验证操作类型
            if ($typeId != 9) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
            
            //验证必须请求参数
            if (empty($typeId) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            
            //验证签名
            $status = $this->signVerify($request);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }

            $results = D('finance/softwareClientOparation')->getSoftwareOparationList();
            
            $list = "";
            if (!empty($results)) {
                foreach ($results as $key => $value) {
                    unset($tempArr);
                    $tempArr['id'] = $value['id'];
                    $tempArr['type'] = $value['type'];
                    if (!empty($value['action'])) {
                        $action = $this->apiSite . $value['action'];
                    }
                    if (!empty($value['action'])) {
                        $backurl = $this->apiSite . $value['backurl'];
                    }
                    $tempArr['action'] = $action;
                    $tempArr['backurl'] = $backurl;
                    $tempArr['field'] = $value['field'];
                    $list[] = $tempArr;
                }
            }
            if (!empty($list)) {
                $this->setResponse($list);
                $this->response['status'] = 1;
                $this->response['msg'] = '获取列表成功';
            } else {
                $this->response['status'] = -1;
                $this->response['msg'] = '无数据';
            }
        }
        $this->sendResponse();
    }

    /*
     * 获取软件版本列表
     * @author tianxiang
     * @return jsonData
     */

    private function getSoftwareListById($id) {
        if ($id) {
            $vresult = D('finance/software')->getVersionListById($id);
            $vlists = "";
            if (!empty($vresult)) {
                $tt = substr(date('Y-m-d H:i'), 0, -1) . '0:00';
                $begintime = date('Y-m-d H:i:s', strtotime($tt) - 600);
                $endtime = $tt;
                foreach ($vresult as $key => $value) {
                   if($value['status']!=2) continue;
                    unset($tempArr);
                    $tempArr['id'] = $value['id'];
                    $tempArr['version'] = $value['version'];
                    $tempArr['qid'] = $value['qid'];
                    $tempArr['db_type'] = $value['db_type'];
                    $tempArr['db_name'] = $value['db_name'];
                    $tempArr['db_username'] = $value['db_username'];
                    $tempArr['db_pwd'] = $value['db_pwd'];
                    if(!empty($value['db_sql'])){
                       $tempArr['db_sql'] = D('finance/softwareVersionQueryConfig')->getSqlTpl($value['db_sql'], $begintime, $endtime,"test");
                    }
                    $vlists[] = $tempArr;
                }
            }
            return $vlists;
        }
    }

    /*
     * 激活客户端软件
     * @author tianxiang
     * @return jsonData
     */

    public function actionClientActivation() {
        if ($this->getRequestData()) {
            //接受参数
            $request = $this->request;
            //验证必填参数
            $code = $request['code'];
           // $sort_var_id = $request['sort_var_id'];
            //$uid = $request['uid'];
            $sort_client_id = $request['sort_client_id'];
            $typeId = $request['typeId'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];
            //验证操作类型
            if ($typeId != 14) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
            //验证必须请求参数
            if (empty($code) || empty($sort_client_id) || empty($typeId) || empty($request_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            //验证签名
            $status = $this->signVerify($request);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }
            $info = D('finance/softwareCodeBatchInfo')->getInfoByCode($code);
            $now = time();
            if (!empty($info)) {
              if($info['cstatus']!=1 || $info['sstatus'] !=1 ||  $info['status'] ==2){
                   $this->response['status'] = -8; // 验证错误
                   $this->response['msg'] = '激活码无效';
              }
              else if ($now < $info['start_time']) {
                    $this->response['status'] = -6; // 验证错误
                    $this->response['msg'] = '激活码不能提前使用！';
                } else if ($now > $info['end_time']) {
                    $this->response['status'] = -7; // 验证错误
                    $this->response['msg'] = '激活码已过期！';
                } else {
                    //激活
                    $status = D('finance/softwareClient')->clientActivation($info['uid'],$sort_client_id,$code);
                    if ($status) {
                        $this->response['status'] = 1; // 验证错误
                        $this->response['msg'] = '激活成功';
                    } else {
                        $this->response['status'] = -1; // 验证错误
                        $this->response['msg'] = '激活失败';
                    }
                }
            } else {
                $this->response['status'] = -8; // 验证错误
                $this->response['msg'] = '激活码无效！';
            }
            $this->sendResponse();
            exit;
        }
    }

    /*
     * 获取软件激活码
     * @author tianxiang
     * @return jsonData
     */

    public function actionClientQuerySendData() {
        if ($this->getRequestData()) {
            //接受参数
            $request = $this->request;
            //验证必填参数
            $typeId = $request['typeId'];
            $code = $request['code'];
            $source = $request['source'];
            $db_sql = $request['db_sql'];
            $sort_client_id = $request['sort_client_id'];
            $qid = $request['qid'];
            $qu_type = $request['qu_type'];
            $start_time = $request['start_time'];
            $end_time = $request['end_time'];
            $request_time = $request['request_time'];
            $sign = $request['sign'];
            //验证操作类型
            if ($typeId != 15) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
           
            //验证必须请求参数
            if (empty($code) || empty($db_sql) || empty($sort_client_id) || empty($qid) || empty($qu_type) || empty($typeId) || empty($request_time) || empty($start_time) ||  empty($end_time) || empty($sign)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
            //验证签名
            $row = D('finance/softwareClient')->getInfo($sort_client_id);
            $status = $this->signVerify($request,$row['token']);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }
            $data = array();
            $data['sort_client_id'] = $sort_client_id;
            $data['qid'] = $qid;
            $data['qu_type'] = $qu_type;
            $data['code'] = $code;
            $data['db_sql'] = $db_sql;
            $data['source'] = $source;
            $data['start_time'] = strtotime($start_time);
            $data['end_time'] = strtotime($end_time);
            $data['add_time'] = time();
            $consumption =$request['consumption'];
            if(!empty($consumption)){
                $i=0;
                $amount = 0;
                foreach ($consumption as  $value) {
                    $i++;
                    $amount = $amount + $value['amount'];
                }
                $data['data'] = serialize($consumption);
                $data['amount'] = $amount;
                $statusA = D('finance/expense')->save($data);
                 //插入
                if (!empty($statusA)) {
                    $this->response['status'] = 1;
                    $this->response['msg'] = '保存成功';
                } else {
                    $this->response['status'] = -1;
                    $this->response['msg'] = '保存失败';
                }
            }else{
                 $this->response['status'] = -1;
                 $this->response['msg'] = '保存失败';
                 
            }
            $this->sendResponse();
            exit;
        }
    }

    /*
     * 返回软件故障
     * @author tianxiang
     * @return jsonData
     */

    public function actionReturnFault() {
        if ($this->getRequestData()) {
             //验证操作类型
            $request = $this->request;
            $typeId = $request['typeId'];
            $sort_client_id = $request['sort_client_id'];
            $description = $request['description'];
            $code = $request['code'];
            if ($typeId != 7) {
                $this->response['status'] = -4;
                $this->response['msg'] = '对不起，操作类型错误';
                $this->sendResponse();
                exit;
            }
            //验证必填参数
            if (empty($code) || empty($sort_client_id) || empty($description)) {
                $this->response['status'] = -5;
                $this->response['msg'] = '请求失败，请确认参数是否正确';
                $this->sendResponse();
                exit;
            }
             //验证签名
            $status = $this->signVerify($request);
            if (empty($status)) {
                $this->response['status'] = -2; // 验证错误
                $this->response['msg'] = '签名验证错误';
                $this->sendResponse();
                exit;
            }
            //数据准备
            $data = array();
            $data['typeId'] = $typeId;
            $data['sort_client_id'] = $sort_client_id;
            $data['code'] = $code;
            $data['description'] = $description;
            $data['add_time'] = time();
            $data['status'] = 0;
            //插入
            $status = D('finance/softwarefault')->returnFault($data);
            if (!empty($status)) {
                $this->response['status'] = 1;
                $this->response['msg'] = '插入成功';
            }
        }
        $this->sendResponse();
    }

    /*
     * 验证激活码是否可用
     * 王萌 2016/06/30
     * */
    public function actionVerifyActivationCode() {
        try{
            if ($this->getRequestData()) {
                if(!$this->signVerify($this->request)) {
                    $statusCode = -2;
                    throw_exception('签名错误');
                }
                if(intval($this->request['typeId']) !== self::$oparationVerifyActivationCode) {
                    throw_exception('非法请求');
                }
                if(!D('finance/softwareInstallVerify') -> vefifyCodeIsExpire($this->request['code'])) {
                    throw_exception('激活码不存在');
                }
                if(D('finance/softwareInstallVerify') -> verifyCode($this->request['code'], $this->request['identification'])) {
                    D('finance/softwareInstallVerify') -> save(array('code' => $this->request['code'], 'identification' => $this->request['identification'], 'type' => model_finance_softwareInstallVerify
                        ::$typeVerifyCode));
                }else {
                    throw_exception('激活码不可用');
                }
                $this->response['status'] = self::$statusCodeSuccess;
                $this->response['msg'] = 'success';
            }
            $this->sendResponse();
        }catch (Exception $e) {
            $this->response['status'] = isset($statusCode) ? $statusCode : self::$statusCodeError;
            $this->response['msg'] = $e -> getMessage();
            $this->sendResponse();
        }
    }

    /*
     * 验证手机号是否注册过
     * 王萌 2016/06/30
     * */
    public function actionVerifyMobile() {
        try {
            if ($this->getRequestData()) {
                if(!$this->signVerify($this->request)) {
                    $statusCode = -2;
                    throw_exception('签名错误');
                }
                if (empty($this->request['identification']) || empty($this->request['mobile']) || empty($this->request['typeId'])) {
                    throw_exception('请求失败，请确认参数是否正确');
                }else{
                    if(intval($this->request['typeId']) !== self::$oparationVerifyMobile) {
                        throw_exception('非法请求');
                    }
                    if(strlen($this->request['mobile']) != 11) {
                        throw_exception('请输入正确手机号');
                    }
                    if(D('finance/softwareInstallVerify') -> verifyMobile($this->request['mobile'], $this->request['identification'])) {
                        D('finance/softwareInstallVerify') -> save(array('mobile' => $this->request['mobile'], 'identification' => $this->request['identification'], 'type' => model_finance_softwareInstallVerify
::$typeVerifyMobile));
                    }else {
                        throw_exception('手机号已使用过,请绑定新的手机号');
                    }
                    $this->response['status'] = self::$statusCodeSuccess;
                    $this->response['msg'] = 'success';
                }
            };
            $this->sendResponse();
        }catch (Exception $e){
            $this->response['status'] = isset($statusCode) ? $statusCode : self::$statusCodeError;
            $this->response['msg'] = $e -> getMessage();
            $this->sendResponse();
        }
    }

    /**
     * 活取request数据
     * @return bool
     */
    private function getRequestData() {
        //$info = $this->_post();
        $info = file_get_contents("php://input");
        //$rs = file_put_contents(DATA_PATH.'/log/1.txt', $info, FILE_APPEND);     
        $info = json_decode(trim($info), true);
        return $this->decodeRequest($info);
    }

    /**
     * 发送response信息
     */
    private function sendResponse() {
        header('Content-Type:application/json; charset=utf-8');
        echo $this->encodeResponse();
    }

    /**
     * 解析并验证request数据
     * @param $info
     * @return bool
     */
    private function decodeRequest($info) {
        //签名检测
        $this->request = $info;
        return true;
    }

    /**
     * 设置 response body data
     * @param $dat
     */
    private function setResponse($dat) {
        if ($dat) {
            $this->response['data'] = $dat;
        }
    }

    /**
     * 编码并加入验证信息
     */
    private function encodeResponse() {
        return urldecode(json_encode(urlEncodeJson($this->response)));
    }

    /**
     * 检查签名认证
     * @param $params 待签名的数组,
     * @author tianxiang
     * @return 1 签名通过，-1签名不通过
     */
    private function signVerify($params,$token = "") {
        $typeId = $params['typeId'];
        $row = D('finance/softwareClientOparation')->getInfoById($typeId); 
        $fieldA = "," . $row['field'];
        $fieldArr = explode(",", $fieldA);
        array_shift($fieldArr);
        $fieldstr = "";
        $flag = 0;
        if (!empty($fieldArr)) {
            foreach ($fieldArr as $field) {
                if($field == 'token') continue;
                $fieldstr = $fieldstr . "&" . $params[$field];
            }
        }
        $field = substr($fieldstr, 1);
        $sign = $params['sign'];
        if(!empty($token)){
           $key = $token; //用token做签名秘钥 
        }else{
           $key = $this->signKey; //用signKey做签名秘钥
        }
        $sequence = $key . "&" . $field; //待签名字符串
        //签名验证
        if (!empty($sequence)) {
            $wsign = md5($sequence); //加密
            if ($sign != $wsign) { //签名检测
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

}