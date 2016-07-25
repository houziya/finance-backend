<?php
/**
 * 财务插件接口上传
 */
class controller_api_finance extends controller_api_abstract {

	private $request;
	private $response;
	private $softwareinfo; //当前财务软件信息

	/**
	 * 初始化默认返回值
	 */

	public function __construct() {
		$this->request = array();
		$this->response = array(
			'status' => 0,
			'msg' => '',
		);
	}

	public function actionIndex() {
		exit();
	}

	/**
	 * 网络联通性测试
	 */
	public function actionPing() {
		if ($this->getRequestData()) {
			$this->response['status'] = 1;
			$this->response['msg'] = '接口测试通过';
		}
		$this->sendResponse();
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

	/**
	 * 获取功能性的配置
	 * 获取数据库查询的SQL
	 */
	public function actionConfig() {
		if ($this->getRequestData()) {			
			$info = D('finance/pluginConfig')->getInfo($this->softwareinfo['code']);
			if(empty($info)){
				$this->response['status'] = -3;
				$this->response['msg'] = '财务软件配置不存在';
				$this->sendResponse();exit;
			}
			
			$data = array();			
			//获取前10分钟的时间段
			$tt = substr(date('Y-m-d H:i'),0,-1).'0:00';
			$data['begintime'] = date('Y-m-d H:i:s', strtotime($tt) - 600);
			$data['endtime'] = $tt;
			$data['dbname'] = $info['dbname'];
			$data['sql'] = D('finance/pluginConfig')->getSqlTpl($info['sqltpl'],$data['begintime'],$data['endtime']);
			$this->setResponse($data);
			
			$this->response['status'] = 1;
			$this->response['msg'] = 'success';	
		}
		$this->sendResponse();
	}
	
	/**
	 * 财务插件安装
	 */
	public function actionInstall() {
		if ($this->getRequestData()) {
			if(empty($this->request['dbtype']) || !isset($this->request['dbusername']) || !isset($this->request['dbpassword']) || empty($this->request['dbname'])){
				$this->response['status'] = -5;
				$this->response['msg'] = '安装失败，请确认参数是否正确';
				$this->sendResponse();exit;
			}
			if(!empty($this->softwareinfo['dbtype'])){
//				$this->response['status'] = -6;
//				$this->response['msg'] = '安装失败，请不要进行多次安装';
//				$this->sendResponse();exit;
			}
			
			$data = array();
			$data['dbtype'] = $this->request['dbtype'];
			$data['dbhost'] = $this->request['dbhost'];
			$data['dbusername'] = $this->request['dbusername'];
			$data['dbpassword'] = $this->request['dbpassword'];
			$data['dbname'] = $this->request['dbname'];
			D('finance/pluginSoftware')->setInstall($this->request['projectid'],$this->request['storeid'],$data);
			
			$this->response['status'] = 1;
			$this->response['msg'] = '安装成功';	
		}
		$this->sendResponse();
	}

	/**
	 * 任务接口
	 * 财务插件通过这里获取任务信息
	 */
	public function actionTask() {
		if ($this->getRequestData()) {
			$task = D('finance/pluginTask')->getWaitTask($this->request['projectid'],$this->request['storeid']);
			if(empty($task)){
				$this->response['status'] = 0;
				$this->response['msg'] = '没有新任务';
				$this->setResponse(array('task_num' => 0));
				$this->sendResponse();exit;
			}
			$this->response['status'] = 1;
			$this->response['msg'] = '获取新任务成功';
			$this->setResponse($task);
		}
		$this->sendResponse();
	}

	/**
	 * 更新任务状态
	 */
	public function actionTaskover() {
		if ($this->getRequestData()) {
			if(empty($this->request['task_id'])){
				$this->response['status'] = -7;
				$this->response['msg'] = '任务ID错误';
				$this->sendResponse();exit;
			}
			
			$map = array();
			$map['id'] = $this->request['task_id'];
			$map['pid'] = $this->request['projectid'];
			$map['store_id'] = $this->request['storeid'];

			D('finance/pluginTask')->where($map)->save(array('status' => 1));		
			$this->response['status'] = 1;
			$this->response['msg'] = '任务状态已处理';
		}
		$this->sendResponse();
	}

	/**
	 * 接收 plugin 发送过来的数据
	 */
	public function actionSend() {
		if ($this->getRequestData()) {
			if(!preg_match('/^201[0-9]{1}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{1}0:00$/i', $this->request['begintime']) || !preg_match('/^201[0-9]{1}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{1}0$/i', $this->request['endtime'])){
				$this->response['status'] = -4;
				$this->response['msg'] = '日期格式错误';
				$this->sendResponse();exit;
			}

			//参数获取
			$data = array();
			$data['srid'] = $this->request['endtime'];
			$data['pid'] = intval($this->request['projectid']);
			$data['store_num'] = intval($this->request['storeid']);
			$data['status'] = 0;
			$data['sysname'] = D('finance/pluginConfig')->getInfo($this->softwareinfo['code'],'sysname');
			$data['addtime'] = date('Y-m-d H:i:s');
			$data['rawdata'] = $this->request['rawdata'];
			if(empty($data['rawdata'])) $data['rawdata'] = array();
			$data['rawdata'] = json_encode($data['rawdata']);

			//更新存档数据
			D('finance/pluginSoftware')->updateSoftwareLastTime($data['pid'], $data['store_num'],$data['addtime'],$data['srid']);
			
			//检查数据是否重复提交
			$id = M("financial_income_log_raw")->where(array('pid' => $data['pid'], 'store_num' => $data['store_num'], 'srid' => $data['srid']))->getField('id');

			if($id){
				M('financial_income_log_raw')->where(array('id' => $id))->save($data);
			}else{
				M('financial_income_log_raw')->add($data);
			}
			
			$this->response['status'] = 1;
			$this->response['msg'] = '数据提交成功';	
		}
		$this->sendResponse();
	}
	
	/**
	 * 错误消息反馈接口
	 */
	public function actionErrorFeedback() {
		if ($this->getRequestData()) {
			//参数获取
			$data = array();
			$data['pid'] = intval($this->request['projectid']);
			$data['store_num'] = intval($this->request['storeid']);
			$data['code'] = D('finance/PluginSoftware')->getInfo("{$data['pid']}_{$data['store_num']}",'code');
			$data['data'] = serialize($this->request['data']);
			$data['add_time'] = date('Y-m-d H:i:s');
			$data['ip'] = getIp();
			$data['status'] = 0;
			M('financial_plugin_error')->add($data);
			$this->response['status'] = 1;
			$this->response['msg'] = '错误反馈提交成功';	
		}
		$this->sendResponse();
	}

	/**
	 * 活取request数据
	 * @return bool
	 */
	private function getRequestData() {
//		$info = $this->_post();
		$info = file_get_contents("php://input");
//		$rs = file_put_contents(DATA_PATH.'/log/1.txt', $info, FILE_APPEND);
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
		if (is_numeric($info['projectid']) && is_numeric($info['storeid']) && preg_match('/^201[0-9]{1}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/i', $info['time'])) {
			$this->softwareinfo = D('finance/PluginSoftware')->getInfo("{$info['projectid']}_{$info['storeid']}");
			if (empty($this->softwareinfo) || $this->softwareinfo['status'] <> 1) {
				$this->response['status'] = -1; // 项目ID或店铺编号不存在
				$this->response['msg'] = '项目ID或店铺编号不存在';
				return false;
			}
			//加密验证检查
			$headSign = $info['projectid'] . $info['storeid'] . $info['time'] . $this->softwareinfo['token'];
			$headSign = md5($headSign);
			//head 验证
			if ($headSign <> $info['sign']) {
				$this->response['status'] = -2; // 验证错误
				$this->response['msg'] = '加密验证错误';
				return false;
			}
			$this->request = $info;			
			return true;
		} else {
			$this->response['status'] = 0; //参数错误
			$this->response['msg'] = '参数错误';
			return false;
		}
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

}