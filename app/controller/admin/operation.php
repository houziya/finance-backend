<?php

/**
 * 软件后台管理 操作管理
 */
class controller_admin_operation extends controller_admin_abstract {

     /*
     * 软件客户端操作列表
     * @author tianxiang
     * @return 无
     */
    public function actionIndex() {
        $var = $this->_get();
        //初始化
        $var['id'] =  empty($var['id']) ? "":$var['id'];
        $var['type'] =  empty($var['type']) ? "":$var['type'];
        $var['cid'] =  empty($var['cid']) ? "":$var['cid'];
        //搜索参数处理
        $map = $this->_search (array('sortware_client_oparation'));
        $map2  =  $map;
       
        
        $res = M("sortware_client_oparation")->where($map2)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                $arr = model_finance_softwareClientOparation::$status_arr[$v['status']];
	        $v['status_tips']  = "<span {$arr['style']}>{$arr['name']}</span>";
            }
        }
         //状态下拉框 
        $var['status_select'] = helper_form::select($this->_get('status'), model_finance_softwareClientOparation::$status_arr, 'name="search[status]"','全部');
        
        $var = array_merge($var,$res);
        // print_r($res);
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }
    
     //修改操作
    public function actionEdit() {
        //Post提交数据处理
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['update_time'] = time();
            $id = $this->_post('id');
            D('finance/softwareclientoparation')->update($id, $data);
            //处理缓存
             D('finance/softwareclientoparation')->getSoftwareOparationList(null,true);
            //日志返回
            $this->savelog('修改操作【id:' . $id . '】');
            $this->success('软件操作修改成功', url("Index?menu_id=55"));
        }
        $id = $this->_get('id');
        if (empty($id)) $this->error('参数错误');
        //版本详情
        $var['info'] =   D('finance/softwareclientoparation')->getInfoById($id);
        //模板赋值
        $this->assign($var);
        $this->display("edit");
    }
    
    //添加操作
    public function actionAdd() {
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['add_time'] = time();
            $id = D('finance/softwareclientoparation')->save($data);
            
            $this->savelog('添加操作【id:' . $id . '】');
            $this->success('软件操作添加成功', url("Index?&menu_id=55"));
        }
        $this->assign($var);
        $this->display("add");
    }
    
    
    
     //设置客户端状态
    public function actionSetStatus() {
        $ids = $this->_post("ids");
        $status = $this->_post("status");
        if($status == 0){
            $data['status'] = 0;
            $set = "关闭";
        }else{
            $data['status'] = 1;
            $set = "开启"; 
        }
        if (empty($ids))
            $this->error('请选择待设置的操作ID');
        foreach ($ids as $id) {
            D('finance/softwareclientoparation')->update($id, $data);
            //更新缓存
            D('finance/softwareclientoparation')->getSoftwareOparationList(null,true);
        }
        $ids = implode(',', $ids);
        $this->savelog("{$set}客户端ID【id:{$ids}】");
        $this->success("{$set}客户端成功");
    }
}
