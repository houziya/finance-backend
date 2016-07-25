<?php

/**
 * 软件后台管理
 */
class controller_admin_software extends controller_admin_abstract {

     /*
     * 软件列表
     * @author tianxiang
     * @return 无
     */
    public function actionIndex() {
        $var = $this->_get();
        //搜索参数处理
        $map = $this->_search (array('sortware'));
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
        
        $res = M("sortware")->where($map2)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                $arr = model_finance_software::$status_arr[$v['status']];
	        $v['status_tips']  = "<span {$arr['style']}>{$arr['name']}</span>";
            }
        }
        
        //处理时间区间段input表单
        $var['input_add_time'] = helper_form::date('search[add_time]',$this->_get('add_time'));
        $var['input_update_time'] = helper_form::date('search[update_time]',$this->_get('update_time'));
        
         //状态下拉框 
        $var['status_select'] = helper_form::select($this->_get('status'), model_finance_software::$status_arr, 'name="search[status]"','全部');
        $var = array_merge($var,$res);
        //print_r( $var['status_select']);
        $this->assign($var);
        $this->setReUrl();
        $this->display();
    }

    
    /*
     * 软件激活码列表
     * @author tianxiang
     * @return 无
     */
    public function actionQueryConfig() {
        //搜索参数处理
        $id = $this->_get("id");
        $map['vid'] = $id;
        $res = M("sortware_version_queryconfig")->where($map)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                $arr = model_finance_softwareVersionQueryConfig::$status_arr[$v['status']];
	        $v['status_tips']  = "<span {$arr['style']}>{$arr['name']}</span>";
            }
        }
        
        $row = M("sortware_version")->where(array("id"=>$id))->order('id desc')->find();
        $this->assign("row",$row);
        $this->assign($res);
        $this->setReUrl();
        $this->display("vqcindex");
    }
    
    //修改查询配置
    public function actionQueryConfigEdit() {
        //Post提交数据处理
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['update_time'] = time();
            $id = $this->_post('id');
            D('finance/softwareVersionQueryConfig')->update($id, $data);
            //处理缓存
            $vid = $this->_post('vid');
            $sort_id = $this->_post('sort_id');
            D('finance/softwareVersionQueryConfig')->getQueryConfigListById($vid,null,true); 
            D('finance/software')->getVersionListById($sort_id,null,true);
            
            //日志返回
            $this->savelog('修改软件版本查询配置【id:' . $data['id'] . '】');
            $this->success('软件版本查询配置修改成功', url("QueryConfig?id=".$vid."&menu_id=37"));
        }
        $id = $this->_get('id');
        $sort_id = $this->_get('sort_id');
        if (empty($id)) $this->error('参数错误');
        //版本详情
        $var['info'] =   D('finance/softwareversionqueryconfig')->getInfo($id);
        //模板赋值
        $this->assign("sort_id",$sort_id);
        $this->assign($var);
        $this->display("vqcedit");
    }
    
     //添加软件版本查询
    public function actionQueryConfigAdd() {
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['add_time'] = time();
            $id = D('finance/softwareversionqueryconfig')->save($data);
            
            //处理缓存
            $vid = $data['vid'];
            $sort_id = $this->_post('sort_id');
            D('finance/softwareVersionQueryConfig')->getQueryConfigListById($vid,null,true); 
            D('finance/software')->getVersionListById($sort_id,null,true);
            
            $this->savelog('添加软件【id:' . $id . '】');
            $this->success('软件版本添加成功', url("QueryConfig?id=".$data['vid']."&menu_id=37"));
        }
        $vid = $this->_get('vid');
        $this->assign("vid",$vid);
        $this->assign($var);
        $this->display("vqcadd");
    }
    
     /*
     * 软件版本列表
     * @author tianxiang
     * @return 无
     */
    public function actionVersionIndex() {
        //搜索参数处理
        $var = $this->_get();
        $map = $this->_search (array('sortware_version'));
     
        $map2 = $map;
        //按添加时间搜素
        if (!empty($map2)) {
            $add_time = $this->_get('add_time');
            if (!empty($add_time)) {
                $map2['add_time'] = array(array('egt', strtotime($add_time)), array('elt', strtotime($add_time) + 86400));
            }
        }
        //按更新时间搜素
        if (!empty($map2)) {
            $update_time = $this->_get('update_time');
            if (!empty($update_time)) {
                $map2['update_time'] = array(array('egt', strtotime($update_time)), array('elt', strtotime($update_time) + 86400));
            }
        }

        $res = M("sortware_version")->where($map2)->order('id desc')->page();
        if($res['lists']){
            foreach($res['lists'] as $k => &$v){
                //处理状态
                $arr = model_finance_softwareVersion::$status_arr[$v['status']];
	        $v['status_tips']  = "<span {$arr['style']}>{$arr['name']}</span>";
            }
        }
       
        $id = $this->_get('sort_id');
        $row = M("sortware")->where(array("id" => $id))->order('id desc')->find();
        //处理时间区间段input表单
        $var['input_add_time'] = helper_form::date('search[add_time]', $this->_get('add_time'));
        $var['input_update_time'] = helper_form::date('search[update_time]', $this->_get('update_time'));

        //状态下拉框 
        $var['status_select'] = helper_form::select($this->_get('status'), model_finance_software::$status_arr, 'name="search[status]"', '全部');
        
        $var = array_merge($var, $res);
        $this->assign("row", $row);
        $this->assign($var);
        $this->setReUrl();
        $this->display("vindex");
    }

    //修改版本
    public function actionVersionEdit() {
        //修改版本
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['update_time'] = time();
            $id = $this->_post('id');
            $sort_id = $this->_post('sort_id');
            D('finance/softwareversion')->update($id, $data);
             //处理缓存
            D('finance/softwareVersion')->getVersionListById($sort_id,null,true);
            D('finance/software')->getVersionListById($sort_id,null,true);
            //日志返回
            $this->savelog('修改软件版本【id:' . $data['id'] . '】');
            $this->success('软件版本修改成功', url("versionIndex?id=".$sort_id));
        }
        $id = $this->_get('id');
        if (empty($id)) $this->error('参数错误');
        //版本详情
        $var['info'] =   D('finance/softwareversion')->getInfo($id);
        //模板赋值
        $this->assign($var);
        $this->display("vedit");
    }
    
     //添加软件版本
    public function actionVersionAdd() {
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['add_time'] = time();
            $id = D('finance/softwareversion')->save($data);
            
             //处理缓存
            $sort_id = $data['sort_id'];
            D('finance/softwareVersion')->getVersionListById($sort_id,null,true);
            D('finance/software')->getVersionListById($sort_id,null,true);
            
            $this->savelog('添加软件【id:' . $id . '】');
            $this->success('软件版本添加成功', url("versionIndex?id=".$data['sort_id']));
        }
        $sort_id = $this->_get('sort_id');
        $this->assign("sort_id",$sort_id);
        $this->assign($var);
        $this->display("vadd");
    }
    
     //修改版本
    public function actionEdit() {
        //Post提交
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['update_time'] = time();
            $id = $this->_post('id');
  
            D('finance/software')->update($id, $data);
            //日志返回
            $this->savelog('修改软件【id:' . $data['id'] . '】');
            $this->success('修改软件成功', url('index'));
        }
        $id = $this->_get('id');
        if (empty($id)) $this->error('参数错误');
        //软件详情
        $var['info'] =   D('finance/software')->getInfo($id);
        //模板赋值
        $this->assign($var);
        $this->display();
    }
    
     //添加软件
    public function actionAdd() {
        ///Post提交
        if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
            $data = $this->_post('data');
            $data['add_time'] = time();
            $id = D('finance/software')->save($data);
            //写日志返回
            $this->savelog('添加软件【id:' . $data['id'] . '】');
            $this->success('软件版本添加成功', url('Index'));
        }
        //模板赋值
        $this->display();
    }
    
    
    
     //设置厂商状态
    public function actionSetStatus() {
        $ids = $this->_post("ids");
        $status = $this->_post("status");
        if($status == 0){
            $set = "关闭";
        }else{
            $set = "开启"; 
        }
        if (empty($ids))
            $this->error('请选择待设置的软件');
        foreach ($ids as $id) {
            $data['status'] = $status;
            D('finance/software')->update($id, $data);
        }
        $ids = implode(',', $_POST['ids']);
        $this->savelog("{$set}软件ID【id:{$ids}】");
        $this->success("{$set}软件成功");
    }

    //设置版本状态
    public function actionSetVersionStatus() {
        $ids = $this->_post("ids");
        $status = $this->_post("status");
        $sort_id = $this->_post("sort_id"); 
        if($status == 0){
            $set = "禁用";
        }else{
            $set = "启用"; 
        }
        if (empty($ids))
            $this->error('请选择待设置的软件');
        foreach ($ids as $id) {
            $data['status'] = $status;
            D('finance/softwareVersion')->update($id,$data);
        }
        //处理缓存
        D('finance/softwareVersion')->getVersionListById($sort_id,null,true);
        D('finance/software')->getVersionListById($sort_id,null,true);
        
        $ids = implode(',', $_POST['ids']);
        $this->savelog("{$set}版本ID【id:{$ids}】");
        $this->success("{$set}版本操作成功");
    }
    
     //设置版本查询状态
    public function actionSetQueryConfigStatus() {
        $ids = $this->_post("ids");
        $status = $this->_post("status");
        $vid = $this->_post("vid");
        $sort_id = $this->_post("sort_id"); 
        if($status == 0){
            $set = "禁用";
        }else if($status == 1){
            $set = "启用"; 
        }else{
            $set = "设置默认"; 
        }
        if (empty($ids))
            $this->error('请选择待设置的软件');
        //如果是禁用或者启用
        if($status == 0 || $status ==1){
            foreach ($ids as $id) {
                $data['status'] = $status;
                D('finance/softwareVersionQueryConfig')->update($id,$data);
            }
        //如果是设置默认
        }else{
           //接收版本ID
           M('sortware_version_queryconfig')->where(array("vid"=>$vid,"status"=>2))->save(array("status"=>1));
           foreach ($ids as $id) {
                $data['status'] = $status;
                D('finance/softwareVersionQueryConfig')->update($id,$data);
           }
        }
        
        D('finance/softwareVersionQueryConfig')->getQueryConfigListById($vid,null,true); 
        D('finance/software')->getVersionListById($sort_id,null,true);
        
        $ids = implode(',', $_POST['ids']);
        $this->savelog("{$set}查询配置ID【id:{$ids}】");
        $this->success("{$set}查询配置操作成功");
    }

}
