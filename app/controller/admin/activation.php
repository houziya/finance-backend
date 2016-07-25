<?php

/**
 * 软件后台管理
 */
class controller_admin_activation extends controller_admin_abstract {
    /*
     * 激活码批次列表
     * @author tianxiang
     * @return 无
     */

    public function actionIndex() {
        $var = $this->_get();
        //搜索参数处理
        $map = $this->_search (array('client_code_batch'));
        $map2  =  $map;
        unset($map['start_time']);
        unset($map['end_time']);
        //按开始时间搜素 结束时间搜素
        if(!empty($map2)){
           $start_time = $this->_get('start_time');
           $end_time = $this->_get('end_time');
           if(!empty($start_time) && !empty($end_time)){
            $map['start_time']  =  array(array('egt', strtotime($start_time)));
            $map['end_time']  =  array(array('elt', strtotime($end_time)));
           }
        }
        //搜索参数处理
        $res = M("client_code_batch")->where($map)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                $arrA = model_finance_softwareCodeBatch::$mstatus_arr[$v['mstatus']];
	        $v['mstatus_tips']  = "<span {$arrA['style']}>{$arrA['name']}</span>";
                
                $arrB = model_finance_softwareCodeBatch::$cstatus_arr[$v['cstatus']];
	        $v['cstatus_tips']  = "<span {$arrB['style']}>{$arrB['name']}</span>";
                
                $arrC = model_finance_softwareCodeBatch::$sstatus_arr[$v['sstatus']];
	        $v['sstatus_tips']  = "<span {$arrC['style']}>{$arrC['name']}</span>";
            }
        }
        
         //处理时间区间段input表单
        $var['input_start_time'] = helper_form::date('search[start_time]',$this->_get('start_time'));
        $var['input_end_time'] = helper_form::date('search[end_time]',$this->_get('end_time'));
        
         //状态下拉框 
        $var['mstatus_select'] = helper_form::select($this->_get('mstatus'), model_finance_softwareCodeBatch::$mstatus_arr, 'name="search[mstatus]"','全部');
        $var['cstatus_select'] = helper_form::select($this->_get('cstatus'), model_finance_softwareCodeBatch::$cstatus_arr, 'name="search[cstatus]"','全部');
        $var['sstatus_select'] = helper_form::select($this->_get('sstatus'), model_finance_softwareCodeBatch::$sstatus_arr, 'name="search[sstatus]"','全部');
        $var = array_merge($var,$res);
        // print_r($res);
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }

    /*
     * 激活码查看批次
     * @author tianxiang
     * @return 无
     */

    public function actionBatchIndex() {
        $var = $this->_get();
        //搜索参数处理
        $map = $this->_search (array('client_code_batchinfo'));
        $map2  =  $map;
        unset($map['start_time']);
        unset($map['end_time']);
        //按开始时间搜素 结束时间搜素
        if(!empty($map2)){
           $start_time = $this->_get('start_time');
           $end_time = $this->_get('end_time');
           if(!empty($start_time) && !empty($end_time)){
            $map['start_time']  =  array(array('egt', strtotime($start_time)));
            $map['end_time']  =  array(array('elt', strtotime($end_time)));
           }
        }
       
        //$table2 = 'client_code_batch';
        $res = M('client_code_batchinfo')->where($map)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                $arrA = model_finance_softwareCodeBatchInfo::$status_arr[$v['status']];
	        $v['status_tips']  = "<span {$arrA['style']}>{$arrA['name']}</span>";
                
                $arrB = model_finance_softwareCodeBatchInfo::$cstatus_arr[$v['cstatus']];
	        $v['cstatus_tips']  = "<span {$arrB['style']}>{$arrB['name']}</span>";
                
                $arrC = model_finance_softwareCodeBatchInfo::$sstatus_arr[$v['sstatus']];
	        $v['sstatus_tips']  = "<span {$arrC['style']}>{$arrC['name']}</span>";
            }
        }
        $this->assign("bid", $bid);
        //处理时间区间段input表单
        $var['input_start_time'] = helper_form::date('search[start_time]',$this->_get('start_time'));
        $var['input_end_time'] = helper_form::date('search[end_time]',$this->_get('end_time'));
        
         //状态下拉框 
        $var['status_select'] = helper_form::select($this->_get('status'), model_finance_softwareCodeBatchInfo::$status_arr, 'name="search[status]"','全部');
        $var['cstatus_select'] = helper_form::select($this->_get('cstatus'), model_finance_softwareCodeBatchInfo::$cstatus_arr, 'name="search[cstatus]"','全部');
        $var['sstatus_select'] = helper_form::select($this->_get('sstatus'), model_finance_softwareCodeBatchInfo::$sstatus_arr, 'name="search[sstatus]"','全部');
        $var = array_merge($var,$res);
        // print_r($res);
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }

    /*
     * 生成激活码
     * @author tianxiang
     * @return 无
     */

    public function actionAdd() {
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            //处理时间
            $start_time = strtotime($data['start_time']);
            $end_time = strtotime($data['end_time']);
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $data['mstatus'] = 1;
            $data['cstatus'] = 0;
            $data['sstatus'] = 0;
            //软件ID
            $sort_id = $this->_post('sort_id');
            $uid = $this->_post('uid');
            //批次号
            $batch_id = $data['batch_id'] = date("YmdHis") . sprintf("%04d", $sort_id) . rand(0000, 9999);
            $id = D('finance/softwareCodeBatch')->save($data);
            $num = $data['num'];
            for ($i = 1; $i <= $num; $i++) {
                unset($dataA);
                $dataCode['bid'] = $id;
                $dataCode['sort_id'] = $sort_id;
                $dataCode['uid'] = $uid;
                $dataCode['code'] = $this->create_guid();
                $dataCode['start_time'] = $start_time;
                $dataCode['end_time'] = $end_time;
                $dataCode['add_time'] = time();
                D('finance/softwareCodeBatchInfo')->save($dataCode);
            }
            //写日志，跳转成功页面
            $this->savelog('新增激活批次【id:' . $id . '】');
            $this->success('新增激活批次成功', url("Index"));
        }
        $var['sort_id_select'] = D('finance/software')->getSelect();
        $var['u_id_select'] = D('user/user')->getSelect();
        $var['info']['start_time'] = helper_form::date('data[start_time]', date('Y-m-d'), 0);
        $var['info']['end_time'] = helper_form::date('data[end_time]', date('Y-m-d', strtotime('+1 year')), 0);
        
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }
    
    /*
     * 编辑批次
     * @author tianxiang
     * @return 无
     */

    public function actionCodeEdit() {
        $var = $this->_get();
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $start_time = strtotime($data['start_time']);
            $end_time = strtotime($data['end_time']);
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time; 
            $id = $this->_post('id');
            $bid = $this->_post('bid');
            $code = $this->_post('code');
            //编辑激活码
            D('finance/softwareCodeBatchInfo')->update($id,$code,$data);
            //写日志 跳转成功页面
            $this->savelog('编辑激活码【id:' . $id . '】');
            $this->success('编辑激活码成功', url("BatchIndex?bid=".$bid));
        }
        //批次详情
        $var['info'] =   D('finance/softwareCodeBatchInfo')->getInfoByCode($var['code']);
        $var['info']['start_time'] = helper_form::date('data[start_time]', date("Y-m-d",$var['info']['start_time']), 0);
        $var['info']['end_time'] = helper_form::date('data[end_time]', date("Y-m-d",$var['info']['end_time']), 0);
        $this->assign($var);
        $this->setReUrl();
        $this->display("cedit");
    }
    
    /*
     * 激活码批次编辑
     * @author tianxiang
     * @return 无
     */
    public function actionEdit() {
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $id = $this->_post('id');
            //处理批次状态
            D('finance/softwareCodeBatch')->update($id,$data);
            //处理批次详情状态
            $result = M('client_code_batchinfo')->where(array("bid"=>$id))->field("id,code")->findAll();
            if(!empty($result)){
                foreach ($result as $key => $value) {
                     D('finance/softwareCodeBatchInfo')->update($value['id'],$value['code'],$data);
                }
            }
            //处理用户角色
            $this->savelog('编辑激活批次【id:' . $id . '】');
            $this->success('编辑激活批次成功', url("Index"));
        }
        $id = $this->_get('id');
        //批次详情
        $var['info'] =   D('finance/softwareCodeBatch')->getInfo($id);
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }

    //设置激活码状态
    public function actionSetStatus() {
        $id = $this->_get("id");
        $ids = $this->_post("ids");//来自批量
        //获取激活卡号
        $code = $this->_get("code");
        
        //获取状态
        $status = $this->_get("status");
        if(empty($status)){
             $status = $this->_post("status");
        }
        if ($status == -1) {
            $set = "删除";
        } else if ($status == 2) {
            $set = "禁用";
        } else if ($status == 1) {
            $set = "启用";
        }
       
        if (empty($id) && empty($ids))
            $this->error('请选择待设置的激活码');
        $data['status'] = $status;
        if(!empty($id)){
           D('finance/softwareCodeBatchInfo')->update($id, $code, $data); 
        }else{
             $map['id'] = array ('in',$ids);
             $result = M('client_code_batchinfo')->where($map)->field("id,code")->findAll();
             if(!empty($result)){
                 foreach ($result as $key => $value) {
                  D('finance/softwareCodeBatchInfo')->update($value['id'],$value['code'], $data); 
                } 
             }
        }
        $this->savelog("{$set}激活码ID【id:{$id}】");
        $this->success("{$set}激活码操作成功");
    }
    
     //设置批次状态
    public function actionSetBatchStatus() {
        $ids = $this->_post("ids");
        $sstatus = $this->_post("sstatus");
        $cstatus = $this->_post("cstatus");
        if($sstatus == 1){
            $set = "批次出售";
            $data['sstatus'] = 1;
        }
        if($cstatus == 1){
            $set = "批次制卡";
            $data['cstatus'] = 1;
        }
        if (empty($ids))
            $this->error('请选择待设置的批次');
       
        foreach ($ids as $id) {
            D('finance/softwareCodeBatch')->update($id, $data);
             //处理批次详情状态
            $result = M('client_code_batchinfo')->where(array("bid"=>$id))->field("id,code")->findAll();
            if(!empty($result)){
                foreach ($result as $key => $value) {
                     D('finance/softwareCodeBatchInfo')->update($value['id'],$value['code'],$data);
                }
            }
        }
        $ids = implode(',', $_POST['ids']);
        $this->savelog("{$set}软件ID【id:{$ids}】");
        $this->success("{$set}软件成功");
    }

    /*
     * 生成唯一激活码
     * @author tianxiang
     * @return $guid
     */

    public function create_guid($namespace = null) {
        static $guid = '';
        $uid = uniqid("", true);

        $data = $namespace;
        $data .= $_SERVER ['REQUEST_TIME'];  // 请求那一刻的时间戳
        $data .= $_SERVER ['HTTP_USER_AGENT'];  // 获取访问者在用什么操作系统
        $data .= $_SERVER ['SERVER_ADDR'];   // 服务器IP
        $data .= $_SERVER ['SERVER_PORT'];   // 端口号
        $data .= $_SERVER ['REMOTE_ADDR'];   // 远程IP
        $data .= $_SERVER ['REMOTE_PORT'];   // 端口信息

        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);
        return $guid;
    }

}
