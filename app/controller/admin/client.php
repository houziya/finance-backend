<?php

/**
 * 软件后台管理
 */
class controller_admin_client extends controller_admin_abstract {

     /*
     * 软件客户端列表
     * @author tianxiang
     * @return 无
     */
    public function actionIndex() {
        $var = $this->_get();
        //搜索参数处理
        $map = $this->_search (array('sortware_client'));
        $map2  =  $map;
        //按添加时间搜素
        if(!empty($map2)){
           $add_time = $this->_get('add_time');
           if(!empty($add_time)){
            $map2['add_time']  =  array( array('egt', strtotime($add_time)), array('elt', strtotime($add_time)+86400) );
           }
        }
         //按更新时间搜素
        if(!empty($map2)){
           $update_time = $this->_get('update_time');
           if(!empty($update_time)){
            $map2['update_time']  =  array( array('egt', strtotime($update_time)), array('elt', strtotime($update_time)+86400) );
           }
        }
        
        $res = M("sortware_client")->where($map2)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                $arrA = model_finance_softwareClient::$status_arr[$v['status']];
	        $v['status_tips']  = "<span {$arrA['style']}>{$arrA['name']}</span>";
                
                $arrB = model_finance_softwareClient::$online_status_arr[$v['online_status']];
	        $v['online_status_tips']  = "<span {$arrB['style']}>{$arrB['name']}</span>";
            }
        }
         //处理时间区间段input表单
        $var['input_add_time'] = helper_form::date('search[add_time]',$this->_get('add_time'));
        $var['input_update_time'] = helper_form::date('search[update_time]',$this->_get('update_time'));
        
         //状态下拉框 
        $var['status_select'] = helper_form::select($this->_get('status'), model_finance_softwareClient::$status_arr, 'name="search[status]"','全部');
        
        //离在线状态下拉框 
        $var['online_status_select'] = helper_form::select($this->_get('online_status'), model_finance_softwareClient::$online_status_arr, 'name="search[online_status]"','全部');
        
        $var = array_merge($var,$res);
        // print_r($res);
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }
    
    /*
     * 软件客户端查询配置列表
     * @author tianxiang
     * @return 无
     */
    public function actionQueryConfig() {
        //搜索参数处理
        $id = $this->_get("id");
        if(!empty($id)){
           $map['cid'] = $id; 
        }
        
        $code = $this->_post("code");
        if (!empty($code)) {
            $map['code'] = $code;
        }
        
        $mobile = $this->_post("mobile");
        if (!empty($mobile)) {
            $map['mobile'] = $mobile;
        }
        $res = M("sortware_client_queryconfig")->where($map)->order('id desc')->page();
        $this->assign($res);
         $this->assign("cid",$id);
        $this->setReUrl();
        $this->display("qcindex");
    }
    
    /*
     * 修改软件客户端
     * @author tianxiang
     * @return 无
     */
    public function actionEdit() {
          //修改客户端
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $id = $this->_post('id');
            D('finance/softwareClient')->update($id, $data);
            //更新缓存
            D('finance/softwareClientQueryconfig')->getInfoByCid($id,null,true); 
            //处理用户角色
            $this->savelog('修改客户端【id:' . $data['id'] . '】');
            $this->success('软件客户端修改成功', url("index?id=".$sort_id));
        }
        
        //搜索参数处理
        $id = $this->_get("id");
        if (!empty($id)) {
            $map['id'] = $id;
        }
        $res = M("sortware_client")->where($map)->order('id desc')->find();
        // print_r($res);
        $this->assign("info",$res);
        $this->setReUrl();
        $this->display();
    }
    
    /*
     * 查询配置编辑
     * @author tianxiang
     * @return 无
     */
    public function actionQueryConfigEdit() {
         //修改客户端
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $id = $this->_post('id');
            $cid = $this->_post('cid');
            D('finance/softwareClientQueryconfig')->update($id, $data);
           //更新缓存
            D('finance/softwareClientQueryconfig')->getInfoByCid($cid,null,true); 
            //日志返回
            $this->savelog('修改客户端查询配置【id:' . $data['id'] . '】');
            $this->success('修改客户端查询配置成功', url("QueryConfig?id=".$cid."&menu_id=39"));
        }
        //搜索参数处理
        $id = $this->_get("id");
        if (!empty($id)) {
            $map['id'] = $id;
        }
        $res = M("sortware_client_queryconfig")->where($map)->order('id desc')->find();
        $this->assign("info",$res);
        $this->setReUrl();
        $this->display("qcedit");
    }
    
    /*
     * 查询配置添加
     * @author tianxiang
     * @return 无
     */
    public function actionQueryConfigAdd() {
         //修改客户端
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            D('finance/softwareClientQueryconfig')->save($data);
             //更新缓存
            D('finance/softwareClientQueryconfig')->getInfoByCid($data['cid'],null,true);
            //日志返回
            $this->savelog('修改客户端查询配置【id:' . $data['id'] . '】');
            $this->success('修改客户端查询配置成功', url("QueryConfig?id=".$data['cid']."&menu_id=39"));
        }
        //搜索参数处理
        $cid = $this->_get("cid");
        $this->assign("cid",$cid);
        $this->setReUrl();
        $this->display("qcadd");
    }
    
     //设置客户端状态
    public function actionSetQueryConfigStatus() {
        $ids = $this->_post("ids");
        $status = $this->_post("status");
        if($status == 0){
            $set = "禁用";
        }else{
            $set = "开启"; 
        }
        if (empty($ids))
            $this->error('请选择待设置的客户端查询配置');
        //查询客户端
        $map['id']  = array('in',$ids);
        $row  = M("sortware_client_queryconfig")->where($map)->field("cid")->find();
        foreach ($ids as $id) {
            $data['status'] = $status;
            D('finance/softwareClientQueryconfig')->update($id, $data);
             //更新缓存
            D('finance/softwareClientQueryconfig')->getInfoByCid($row['cid'],null,true);
        }
        $ids = implode(',', $_POST['ids']);
        $this->savelog("{$set}客户端查询配置ID【id:{$ids}】");
        $this->success("{$set}客户端查询配置成功");
    }
    
     //设置客户端状态
    public function actionSetStatus() {
        $ids = $this->_post("ids");
        $status = $this->_post("status");
        if($status == 0){
            $data['status'] = 0;
            $set = "禁用";
        }else{
            $data['status'] = 1;
            $set = "开启"; 
        }
        if (empty($ids))
            $this->error('请选择待设置的客户端');
        foreach ($ids as $id) {
            D('finance/softwareClient')->update($id, $data);
            //更新缓存
            D('finance/softwareClientQueryconfig')->getInfoByCid($id,null,true);
        }
        $ids = implode(',', $_POST['ids']);
        $this->savelog("{$set}客户端ID【id:{$ids}】");
        $this->success("{$set}客户端成功");
    }
}
