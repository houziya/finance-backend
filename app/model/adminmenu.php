<?php

/**
 * 菜单模型
 */
class model_AdminMenu extends model_abstruct {
	
	protected $tableName = 'admin_menu';
	public $tree; //节点类
	protected $_menus = array(); //全部菜单数组
	protected $cachename = 'model_adminmenu_list'; //菜单缓存名称

	/*
	 * 得到单条菜单详情
	 * @param string $var info
	 * @return return
	 */
	public function getInfo($id, $field = '') {
		$map = array();
		if (is_numeric($id)) {
			$map['id'] = $id;
		} elseif (is_array($id) && $id) {
			$map = $id;
		} else {
			return array();
		}
		$info = $this->where($map)->find();
		return $field ? $info[$field] : $info;
	}
	
	/**
	 * 得到菜单select下拉框
	 * @param string|int $ids 需要选中的ID
	 * @param string $rule html规则
	 * @return string 
	 */
	public function getSelect($ids = '', $rule = '') {
		if ($rule == '') $rule = "<option value='\$id'\$selected>\$spacer \$name</option>";
		$tpl = $this->tree()->getTpl($ids, $rule, 0);
		return $tpl;
	}

	/**
	 * 得到菜单全部分类Checkbox勾选框数据
	 * @param string|int $ids 需要选中的ID
	 * @param string $rule html规则
	 * @return string 
	 */
	public function getCheckbox($ids = '', $rule = '') {
		if ($rule == '') $rule = "<tr><td class='text-l'>\$spacer <input level='\$level' type='checkbox' name='menu_ids[]' value='\$id' onclick='javascript:checknode(this);'\$checked /> \$name</td></tr>";

		if ($ids && !is_array($ids)) {
			$ids = explode(',', $ids);
		}
		$rows = $this->getChildList(0);
		$tpl = '';
		foreach ($rows as $id => $row) {
			$selected = $checked = '';
			if ($ids && in_array($id, $ids)) {
				$selected = ' selected="selected"';
				$checked = ' checked="checked"';
			}
			$row['level'] = $row['level'] - 1;
			@extract($row);			
			eval("\$nstr = \"$rule\";");
			$tpl .= $nstr;
		}
		return $tpl;
	}

	/**
	 * 得到菜单全部分类Checkbox勾选框数据
	 * @param array $p 需要选中的ID
	 * @param string $name checkbox表单name值
	 * @return string 
	 */
	public function getMenuCheckbox2($p = array(), $name = '') {
		if (!is_array($p)) $p = explode(',', trim($p, ','));
		$name = $name ? $name : 'menu_ids';

		// 全部菜单
		$nodes = $this->getChildTree(0);
		if (!$nodes) {
			return '';
		}
		$tmp = '';
		foreach ($nodes as $app) {
			$tmp .= '<div class="purview">';
			if (in_array($app['id'], $p)) {
				$check = ' checked="checked"';
			}
			$tmp .= '<div class="tit"><input type="checkbox" name="' . $name . '[]" value="' . $app['id'] . '" ' . $check . ' />' . $app['name'] . '</div>';
			unset($check);
			if ($app['child_count'] > 0) {
				foreach ($app['child'] as $module) {
					if (in_array($module['id'], $p)) {
						$check = ' checked="checked"';
					}
					$tmp .= '<div class="tit1"><input type="checkbox" name="' . $name . '[]" value="' . $module['id'] . '" ' . $check . ' />' . $module['name'] . '</div>';
					$tmp .= '<ul>';
					unset($check);
					if ($module['child_count'] > 0) {
						foreach ($module['child'] as $action) {
							if (in_array($action['id'], $p)) {
								$check = ' checked="checked"';
							}
							$tmp .= '<li><input type="checkbox" name="' . $name . '[]" value="' . $action['id'] . '" ' . $check . ' />' . $action['name'] . '</li>';
							unset($check);
						}
					}
					$tmp .= '</ul>';
				}
			}
			$tmp .= '</div>';
		}
		return $tmp;
	}

	/**
	 * 删除菜单
	 * @param int $id 菜单ID
	 * @return int
	 */
	public function menuDelete($id) {
		//此节点下的所有子节点
		$sun = $this->tree()->getChildList($id);
		$in = '';
		if ($sun) {
			foreach ($sun as $v) {
				$in .= $v['id'] . ',';
			}
		}
		$in = $in . $id;
		$res = $this->where("id in($in)")->delete();
		$this->cacheDelete();
		return $res;
	}

	/**
	 * 权限标识转换为ID
	 * @param string $code 权限标识
	 * @return int
	 */
	public function code2id($code) {
		$codes = $this->getCodes();
		return isset($codes[$code]) ? $codes[$code] : '';
	}

	/**
	 * 得到所有未排序的节点
	 * @return array
	 */
	public function getNodes() {
		return $this->tree()->nodes;
	}

	/**
	 * 得到所有权限标识符
	 * @return array
	 */
	public function getCodes() {
		$nodes = $this->tree()->nodes;
		$res = array();
		foreach ($nodes as $node) {
			if (!$node['module']) continue;
			if (!$node['controller']) $node['controller'] = 'default';
			if (!$node['action']) $node['action'] = 'default';
			if (!isset($node['args'])) $node['args'] = '';
			$codename = $node['module'] . '_' . $node['controller'] . '_' . $node['action'];
			$res[$codename] = $node['id'];
		}
		return $res;
	}

	/**
	 * 得到某个节点
	 * @return array
	 */
	public function getNode($id) {
		$nodes = $this->tree()->nodes;
		return isset($nodes[$id]) ? $nodes[$id] : array();
	}

	/**
	 * 节点id转换成名称
	 * @param int $id 菜单节点ID
	 * @return string
	 */
	public function id2name($id) {
		$nodes = $this->tree()->nodes;
		if (isset($nodes[$id]['name'])) {
			return $nodes[$id]['name'];
		} else {
			return '';
		}
	}

	/**
	 * 得到父列表
	 * @param int $id 菜单节点ID
	 * @return array
	 */
	public function getParentList($id) {
		return $this->tree()->getParentList($id);
	}

	/**
	 * 得到子列表
	 * @param int $id 菜单节点ID
	 * @return array
	 */
	public function getChildList($id) {
		return $this->tree()->getChildList($id);
	}

	/**
	 * 得到子树
	 * @param int $id 菜单节点ID
	 * @return array
	 */
	public function getChildTree($id) {
		return $this->tree()->getChildTree($id);
	}

	//更改树状节点空格和图标
	public function setIcon($arr = '', $s = '') {
		if ($arr) {
			$this->tree()->icon = $arr;
		}
		if ($s) {
			$this->tree()->nbsp = $s;
		}
	}

	public function tree() {
		if (!$this->tree) {
			$menus = $this->getMenuCache();
			$this->tree = new helper_tree($menus, 'id', 'pid');
			unset($menus);
		}
		return $this->tree;
	}

	//获取菜单分类数据源
	public function getMenuCache() {
		if (empty($this->_menus)) {
			$this->_menus = S($this->cachename);
			if (empty($this->_menus)) {
				$rows = $this->order('`sort` ASC,id ASC')->findAll();
				foreach ($rows as $k => $v) {
					if (empty($v['url']) && $v['module']) {
						$url = $v['module'] . '-' . (($v['controller'] ? $v['controller'] : C('sys_default_controller')) . '/' . ($v['action'] ? $v['action'] : C('sys_default_action')));
						$query = "menu_id=" . $v['id'];
						if ($v['args']) {
							$query .= '&' . trim($v['args'], '?');
						}
						parse_str($query,$output);
						$v['url'] = url($url,$output);
					}
					$this->_menus[] = $v;
				}
				S($this->cachename, $this->_menus);
				unset($rows);
			}
		}
		return $this->_menus;
	}

	//删除全部缓存
	public function cacheDelete() {
		S($this->cachename, null);
	}
}
