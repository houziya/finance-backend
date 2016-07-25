<?php
// +----------------------------------------------------------------------
// | 后台用户模型
// +----------------------------------------------------------------------

class model_admin_AdminUser extends model_AdminUser {
	
	//网站待处理信息
	public function websiteInformationProcessed($clear=false){
		$cachename   =  'model_websiteInformationProcessed_list';
		$websiteInfo  =  S($cachename);
		if( empty($websiteInfo) || $clear ){
	
			$websiteInfo   =  array(
				'the_first_line' => array(
					array('identifying' => 'real_name',    'name' => '待审核实名用户', 'url'  => url('user/index?is_idcard=1') ),
					array('identifying' => 'attachment',   'name' => '待审核附件',    'url'  => url('attachment/index?status=0')),
					array('identifying' => 'comments',     'name' => '待审核评论',    'url'  => url('comment/index?status=0')),
					array('identifying' => 'comment_back', 'name' => '待审核评论回复',  'url' => url('comment/answercomment?status=0')),
					array('identifying' => 'feedback',     'name' => '待审核意见反馈',  'url' => url('feedback/index?status=0')),
					array('identifying' => 'mall_orders',  'name' => '待发货商城订单',  'url' => url('order/index?status=0')),
				),
				
				'the_second_line' => array(
					array('name' => '待审核项目','url'=> url('project/index?status=1')),
					array('name' => '投资待放款用户','url'=>url('tender/index?status=1')),
				),
			);
			
			foreach($websiteInfo['the_first_line'] as $key=>&$val){
				switch($val['identifying']){
					//待审核实名用户
					case 'real_name': 
						$val['pending_sums']   =  M('user')->where(array('is_idcard'=>1))->count();
						break;
					//待审核附件
					case 'attachment': 
						$val['pending_sums']   =  M('attachment')->where(array('status'=>0))->count();
						break;
					//待审核评论
					case 'comments': 
						$val['pending_sums']   =  M('comment')->where(array('status'=>0))->count();
						break;
					//待审核评论回复
					case 'comment_back': 
						$val['pending_sums']   =  M('user')->where(array('is_idcard'=>1))->count();
						break;
					//待审核意见反馈
					case 'feedback': 
						$val['pending_sums']   =  M('feedback')->where(array('status'=>0))->count();
						break;
					//待发货商城订单
					case 'mall_orders': 
						$val['pending_sums']   =  M('giftsOrder')->where(array('status'=>0))->count();
						break;
					default :
							break;
						
				}
				if(empty($val['pending_sums'])) $val['pending_sums']  =  0;
				$val['pending_sums_tips']  =  '<span style="color:red; font-size:14px;">'.$val['pending_sums'].'</span>';
			}
			
			foreach($websiteInfo['the_second_line'] as $key2=>&$val2){
				switch($val2['identifying']){
					//待审项目
					case 'project': 
						$val['pending_sums']   =  M('project')->where(array('status'=>1))->count();
						break;
					//待放款
					case 'lending': 
						$val['pending_sums']   =  M('ProjectInvestment')->where(array('status'=>1))->count();
						break;
					
					default :
							break;
						
				}
				if(empty($val2['pending_sums'])) $val2['pending_sums']  =  0;
				$val2['pending_sums_tips']  =  '<span style="color:red; font-size:14px;">'.$val2['pending_sums'].'</span>';
			}
			S($cachename,$websiteInfo,60);
		}
		
		return $websiteInfo;
		
	}

}
