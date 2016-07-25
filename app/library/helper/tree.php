<?php
// +----------------------------------------------------------------------
// | 用于将数据库中的数组按照从属关系整理成树或列表
// +----------------------------------------------------------------------
// | Author: liufei <wee2008@qq.com>
// +----------------------------------------------------------------------

class helper_tree {
	protected $id;
	protected $pid;
	protected $child_tree	= array();
	protected $parent_tree	= array();
	public $nodes			= array(); //全部数组
	public $icon = array('│','├─','└─'); //生成树型结构所需修饰符号，可以换成图片
	public $nbsp = "&nbsp;&nbsp;&nbsp;&nbsp;";

	public function __construct($arr, $id='id', $pid='pid') {
		$this->init($arr,$id,$pid);
	}

	// 初始化运行
	public function init($arr,$id,$pid){
		if(!is_array($arr)) return false;
		$this->id = $id;
		$this->pid = $pid;
		$nodes = array();
		foreach($arr as $key=>$value){
			$nodes[$value[$this->id]] = $value;
		}

		foreach ($nodes as $key=>$value) {
			$this->child_tree[$value[$this->pid]][$value[$this->id]] = $value;
			if(isset($nodes[$value[$this->pid]])){
				$this->parent_tree[$value[$this->id]] = $nodes[$value[$this->pid]];
			}			
		}
		unset($arr,$nodes,$id,$pid);
		$this->nodes = $this->getChildList(0);
	}

	// 返回某个节点
	public function getNode($node_id = '') {
		return is_numeric($node_id)&&isset($this->nodes[$node_id]) ? $this->nodes[$node_id] : array();
	}

	// 返回全部节点
	public function getNodes(){
		return $this->nodes;
	}

	//根据模版字符得到HTML展示信息
	//$tpl  = "<li>\$spacer\$name</li>\n";
	//$str = $tree->getTpl(2,$tpl);
	public function getTpl($ids='', $str='', $node_id=0){
		$tpl = '';
		$rows = $this->getChildList($node_id);
		
		if($ids && !is_array($ids)){
			$ids = explode(',',$ids);
		}
		foreach($rows as $id => $row){
			$selected = $checked = '';
			if($ids && in_array($id,$ids)){
				$selected = ' selected="selected"';
				$checked = ' checked="checked"';
			}
			@extract($row);
			eval("\$nstr = \"$str\";");
			$tpl .= $nstr;
		}
		return $tpl;
	}

	// 返回父列表
	public function getParentList($node_id = 0) {
		return $this->_parent($node_id);
	}

	// 返回子列表
	public function getChildList($node_id = 0 , $level = 0) {
		return $this->_child($node_id , $level);
	}

	// 返回子树
	public function getChildTree($node_id = 0 , $level = 0) {
		return $this->_child($node_id , $level , 'tree');
	}

	protected function _child($node_id , $level = 0, $type = 'list' , $this_level = 0, $spacer = '') {
		$arr	= $this->child_tree[$node_id];
		$new_arr	= array();	
		if ($arr) {
			$this_level++;
			$count = count($arr);
			$number = 1;
			foreach ($arr as $id => $node) {
				$arr[$id]['level']	= $this_level;
				$arr[$id]['child_count'] = isset($this->child_tree[$id]) ? count($this->child_tree[$id]) : 0;
				$j = $k = '';				
				if($number == $count){
					$j .= $this->icon[2];
				}else{
					$j .= $this->icon[1];
					$k = $spacer ? $this->icon[0] : '';
				}
				$arr[$id]['spacer'] = $spacer ? $spacer.$j : '';
				if ($type == 'list') {
					$new_arr	= $new_arr + array($id => $arr[$id]);
				}
				if ($level == 0 || $this_level < $level) {
					if (isset($this->child_tree[$id]) && $this->child_tree[$id]) {
						$child	= $this->_child($id , $level , $type , $this_level, $spacer.$k.$this->nbsp);
						if ($type == 'tree') {
							$arr[$id]['child']	= $child;
						} else  {
							$new_arr	= $new_arr + $child;
						}
					}
				}
				$number++;
			}
			if (count($new_arr)) {
				return $new_arr;
			}
			return $arr;
		}
	}

	protected function _parent($node_id , $level = 0) {
		$t	= $this->parent_tree[$node_id];
		$parent_id	= $t[$this->id];
		$parent[$parent_id]	= $t;
		if (!$parent[$parent_id]) return null;
		if ($this->parent_tree[$parent_id])
		{
			$node	= $this->_parent($parent_id);
			if ($node)
			{
				$parent	= $node + $parent;
			}
		}
		return $parent;
	}
}

?>