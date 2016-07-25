<?php
/**
 * 短信管理
 */

class controller_admin_sms extends controller_admin_abstract{

	//提醒项列表
	public function actionIndex(){
		$var = $this->_get();
		if(!empty($_POST)){
			$res = $this->_post('data');
			if( count(explode(",", $res['mobile']))>1){
				if(explode(",", $res['mobile'])){
					$phone = explode(",", $res['mobile']);
				}
			}else{
				$phone = $res['mobile'];
			}
			if($res['server']==1){
				$server = "";
			}elseif ($res['server']==2){
				$isvoice = 1;
			}
			if(!empty($res['content'])){
				if(count($phone)>1){
					foreach($phone as $key=>$val){
						$status_send = model_remind::sendMobile($val,$res['content'],'mobile_reg',$server,$isvoice);
					}
					
				}else{
					$status_send = model_remind::sendMobile($phone,$res['content'],'mobile_reg',$server,$isvoice);
				}
			}
			if($status_send)
			{
// 				$info['status'] = '1';
// 				$info['msg'] = '发送成功';
				$this->success("发送成功！",url("sms/index"));
				die();
			}else{
// 				$info['status'] = '-12';
// 				$info['msg'] = '发送失败';
				$this->error("发送失败，请检查后重新发送",url("sms/index"));
				die();
			}
		}
		$this->display();
	}


}
