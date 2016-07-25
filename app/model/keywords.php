<?php
/**
 * 
 * 关键词管理      model 模型 
 * @author  liurengang
 * @date    2015.03.17
 * 
 */
class model_keywords extends model_abstruct {

	protected $tableName = 'keywords';
	
	
	//文章关键词显示状态
	static public $status_isshow = array(
		0  => array('id' => 0, 'name' => '隐藏', 'style' => 'class="red"'),
		1  => array('id' => 1, 'name' => '显示', 'style' => 'class="green"'),
	);
	
	//是否是热门标签
	static public $status_ishot = array(
		0  => array('id' => 0, 'name' => '非热门', 'style' => 'class="red"'),
		1  => array('id' => 1, 'name' => '热门', 'style' => 'class="green"'),
	);
	
	//是否开启标签的url替换
	static public $status_isreplace = array(
		0  => array('id' => 0, 'name' => '未开启', 'style' => 'class="red"'),
		1  => array('id' => 1, 'name' => '已开启', 'style' => 'class="green"'),
	);
	
	/**
	 * 删除页面关键词
	 * @param int $id 关键词ID
	 * @param boole $clear 是否物理删除 0否 1是
	 * @return int
	 */
	public function keywordDelete($id, $clear = false) {
		if ($clear == 1) {
			$res = $this->where("id = '{$id}'")->delete();
			if ($res) {
				//todo 
			}
		} else {
			$data['is_delete'] = 1;
			$res = $this->where("id = '{$id}'")->save($data);
		}
		
		return $res;
	}
	
	
	/**
	 * 删除文章关键词
	 * @param int $id 关键词ID
	 * @param boole $clear 是否物理删除 0否 1是
	 * @return int
	 */
	public function artKeywordsDelete($id, $clear = false) {
		if ($clear == 1) {
			$res = M('articleKeywords')->where("id = '{$id}'")->delete();
			if ($res) {
				//todo 
			}
		} else {
			$data['is_show'] = -1;
			$res = M('articleKeywords')->where("id = '{$id}'")->save($data);
		}
		
		return $res;
	}
	
	

	
}
