<?php
/**
 * 
 * 附件管理       controller 控制器
 * @author liurengang
 * @date   2015.03.24
 * 
 */
class controller_admin_attachment extends controller_admin_abstract{

	/**
	 * 
	 * 附件列表
	 * @author liurengang
	 * @date   2015/3/24 星期二
	 * 
	 */
	public function actionIndex() {
		$var = $this->_get();
		//搜索参数处理
		$map = $this->_search(array('attachment'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time']     = array( array('egt', strtotime($this->_get('start_time'))), array('elt', strtotime($this->_get('end_time')) + 86400) );
		}
		$res = M('attachment')->where($map)->order('add_time desc')->page();
		if(!empty($res['lists'])){
			foreach($res['lists'] as $key => &$val){
				$val['add_time']    = date('Y-m-d H:i:s',$val['add_time']);
				$arr                = model_attachment::$status_arr[$val['status']];
				$val['status_tips'] = '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				$val['filepath2'] = model_attachment::getImgUrl($val['filepath']);
			}
			unset($val);
		}
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime']   = helper_form::date('search[end_time]',$this->_get('end_time'));
		//留言状态 是否支持回复
		$var['status_select']   = helper_form::select($this->_get('status'),model_attachment::$status_arr,'name="search[status]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	
	/**
	 * 
	 * 删除附件
	 * @author liurengang
	 * @date   2015/3/24  星期二
	 * 
	 */
	public function actionDelete() {
		
		if(isset($_GET['do']) && $_GET['do']=='all'){
			$ids = $this->_post('ids');
			if(!empty($ids)){
				foreach($ids as $id){
					D('attachment')->attachmentDelete($id,true);
				}
			}
			$ids = implode(',',$ids);
			$this->savelog('批量删除见附件【id:' . $ids . '】');
			$this->success('批量删除成功！');
		}else{
			if (empty($_GET['id'])) $this->error('参数错误');
			$id = $this->_get('id');
			if(D('attachment')->attachmentDelete($id,true)){
				$this->savelog('删除附件【id:' . $id . '】');
				$this->success('删除成功！');
			}
		}		
	}
	
	/**
	 * 
	 * 审核附件
	 * @author liurengang
	 * @date   2015/3/24  星期二
	 * 
	 */
	public function actionAudite() {
		
		if(isset($_GET['do']) && $_GET['do']=='all'){
			$ids = $this->_post('ids');
			if(!empty($ids)){
				foreach($ids as $id){
					D('attachment')->attachmentAudite($id);
				}
			}
			$ids = implode(',',$ids);
			$this->savelog('批量审核附件【id:' . $ids . '】');
			$this->success('批量审核成功！');
		}else{
			if (empty($_GET['id'])) $this->error('参数错误');
			$id = $this->_get('id');
			if(D('attachment')->attachmentAudite($id)){
				$this->savelog('审核附件【id:' . $id . '】');
				$this->success('审核成功！');
			}
		}		
	}
	
	

}