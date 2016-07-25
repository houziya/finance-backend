<?php
/**
 * 
 * 文章分类       控制器
 * @author liurengang
 * @date   2015.03.09   
 * 
 */
class controller_admin_articletype extends controller_admin_abstract{

	/**
	 * 
	 * 文章分类列表 
	 * @author liurengang
	 * @date   2015/3/9 星期一
	 * 
	 */
	public function actionIndex() {
		
		$lists = D('ArticleType')->getChildList(0);
		
		$this->assign('lists',$lists);
		$this->display();
	}

	/**
	 * 
	 * 添加文章分类 
	 * @author liurengang
	 * @date   2015/3/9 星期一
	 * 
	 */
	public function actionAdd() {
		
		$articleType = D('ArticleType');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data    = $this->_post('data');			
			if ($data['pid'] != 0) {
				// 第4级文章分类下面禁止添加子文章分类
				$res = $articleType->getParentList($data['pid']);
				if ($res && count($res) >= 3) {
					$this->error('错误！此文章分类下面禁止添加子文章分类！');
				}
			}
			
			$articleType->data($data)->add();
			$articleType->cacheDelete();
			$this->setReUrl('articleType_add_pid', $data['pid']);
			$this->savelog('添加文章分类【id:' . $data['id'] . '】');
			$this->success('文章分类添加成功！');
		}
		$var['page_description_editor'] = helper_form::editor('data[page_description]','page_description','','full');
	
		$pid = $this->_get('pid') ? $this->_get('pid') : (int) $this->getReurl('articleType_add_pid');
		$var['articleTypeSelect']       = $articleType->getSelect($pid);
		
		$this->assign($var);
		$this->display();
	}
	/**
	 * 
	 * 修改文章分类 
	 * @author liurengang
	 * @date   2015/3/9 星期一
	 * 
	 */
	public function actionEdit() {
		$articleType = D('ArticleType');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			if ($data['pid'] != 0) {
				// 第4级文章分类下面禁止添加子文章分类
				$res = $articleType->getParentList($data['pid']);
				if ($res && count($res) >= 3) {
					$this->error('错误！此文章分类下面禁止添加子文章分类！');
				}
			}
			$articleType->where(array('id' => $data['id']))->save($data);
			$articleType->cacheDelete();
			$this->savelog('修改文章分类【id:' . $data['id'] . '】');
			$this->success('文章分类修改成功！', url('index'));
		}

		//排序处理
		if (isset($_POST['do']) && $_POST['do'] == 'sort') {
			$rows = $this->_post('sort');
			foreach ($rows as $k => $v) {
				$data = array();
				$data['sort'] = $v;
				$articleType->where(array('id' => $k))->save($data);
			}
			$articleType->cacheDelete();
			$this->savelog('批量更新文章分类排序');
			$this->success('排序更新成功');
		}

		$var = $this->_get();
		$id  = $this->_get('id');
		if (empty($id)) $this->error('参数错误');
		$var['info']                    = $articleType->getInfo($id);
		$var['page_description_editor'] = helper_form::editor('data[page_description]','page_description',$var['info']['page_description'],'full');
		$var['articleTypeSelect']       = $articleType->getSelect($var['info']['pid']);
		
		$this->assign($var);
		$this->display();
	}
	
	/**
	 * 
	 * 删除文章分类 
	 * @author liurengang
	 * @date   2015/3/9 星期一
	 * 
	 */
	public function actionDelete() {
		if (empty($_GET['id'])) $this->error('参数错误');
		$id = $this->_get('id');
		D('ArticleType')->articleTypeDelete($id);
		D('ArticleType')->cacheDelete();
		$this->savelog('删除文章分类【id:' . $id . '】');
		$this->success('文章分类删除成功！');
	}
	
}