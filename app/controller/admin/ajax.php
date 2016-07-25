<?php
// +----------------------------------------------------------------------
// | ajax 公共控制器
// +----------------------------------------------------------------------

class controller_admin_ajax extends controller_admin_abstract {
	
	//ajax获取地区表单数据
	public function actionAjaxAreaSelect(){
		$id = (int)$this->_get('id');
		$type = (int)$this->_get('type');
		$rs = D('area')->getAjaxAreaSelect($id,$type);
		exit(json_encode($rs));
	}
	
	//ajax获取地区表单数据
	public function actionAjaxCategorySelect(){
		$id = (int)$this->_get('id');
		$type = (int)$this->_get('type');
		$rs = D('rrtCreditCompanyInfoType')->getAjaxCategorySelect($id,$type);
		exit(json_encode($rs));
	}
}