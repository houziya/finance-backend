<?php
class controller_app_callback extends controller_app_abstract {
    private $feemode;
    public function __construct() {
        parent::__construct();
        $pay = C('pay');
        $this->feemode = $pay['feemode'];
    }
/**
	 * 支付回调页面
	 * 浏览器网关模式
	 */
	public function actionCallback() {
		$results = $_REQUEST['resp'];
		$sign = $_REQUEST['sign'];
		$res = Helper_xml::xml_to_array2($results);
		$payYeepay = new helper_yeepay();
		if ($res) {
//            if($re = $payYeepay->signcontentCheck($results,$sign))
//            {
                $feemode = $this->feemode;       
                if($res['service'] == 'TRANSACTION'){
                    $project_transfer =  S('project_transfer_'.$res['requestNo']); 
                    if(!empty($project_transfer)){
                        $feemode =  strtoupper($project_transfer['type']);
                    }
                }    
                 
                $callbackModeel = new model_pay_callback();
                $callbackModeel->init($feemode, $res['service'], 'callback');
                $data['requestNo'] = $res['requestNo'];
                $data['code'] = $res['code'];
                $returnInfo = $callbackModeel->callback($data, $res, $res['requestNo']);
                if ($returnInfo) {
                    $this->success($returnInfo['message']);
                }
                else $this->error("您的操作失败");
//            }
//            else
//                $this->error("您的操作失败");
		}else {
			$this->error("您操作的太快，请稍后再试");
		}
	}

    public function actionNotify() {
        $results = $_REQUEST['notify'];
        $sign = $_REQUEST['sign'];
        $res = Helper_xml::xml_to_array2($results);
        if($res)
        {
            $payYeepay = new helper_yeepay();
//            if($re = $payYeepay->signcontentCheck($results,$sign))
//            {
                if($res['bizType']=='FREEZE')
                    $service = 'TRANSFER';
                else
                    $service = $res['bizType'];
                $feemode = $this->feemode;       
                if($res['bizType'] == 'TRANSACTION'){
                    $project_transfer =  S('project_transfer_'.$res['requestNo']); 
                    if(!empty($project_transfer)){
                        $feemode =  strtoupper($project_transfer['type']);
                    }
                }        
                $callbackModeel = new model_pay_callback();
                $callbackModeel->init($feemode,$service,NOTIFY);
                $data['requestNo'] = $res['requestNo'];
                $data['code'] = $res['code'];
                if($returnInfo = $callbackModeel->callback($data,$res,$res['requestNo']))
                {
                    echo 'SUCCESS';
                }
//            }
        }
    }
}