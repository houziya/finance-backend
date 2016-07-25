<?php
/**
 * 网站黑名单
 * @author quanzhijie
 */
class controller_admin_black extends controller_admin_abstract
{
	/**
     * 黑名单列表
     */
	public function actionIndex()
    {
        $keyword = trim($this->_request('keyword'));
        $type = intval($this->_request('type'));
        $platform = intval($this->_request('cooperation_id'));
        $isShow = intval($this->_request('is_show'));
        $page = intval($this->_request('p', 1));
        $pageSize = 10;
        $start = ($page - 1) * $pageSize;

        $condition = array();
        if ($isShow) {
            $condition['is_show'] = $isShow - 1;
        }
        if ($type) {
            $condition['type'] = $type;
        }
        if ($platform) {
            $condition['cooperation_id'] = $platform;
        }
        if ($keyword) {
            switch ($type) {
                case 1:
                case 2:
                    $condition['project_name'] = array('like', "%{$keyword}%");
                    break;
                case 3:
                    $condition['username'] = array('like', "%{$keyword}%");
                    break;
                default:
                    $condition['_complex'] = array(
                        'project_name' => array('like', "%{$keyword}%"),
                        'username' => array('like', "%{$keyword}%"),
                        '_logic' => 'or'
                    );
            }
        }

        // 黑名单数量
        $totalCount = M('black')->where($condition)->count();
        $totalPage = ceil($totalCount / $pageSize);
        $page = $page > $totalPage ? $totalPage : $page;
        $page = $page > 0 ? $page : 1;
        $pager = new helper_page($totalCount, $page, $pageSize);
	    // 分页条主题
        $pager->setConfig('theme', ' %firstPage%  %prePage%  %linkPage%  %nextPage%  %lastPage%');
        $pager->setTheme('theme');
        $pagerHtml = $pager->show();

        // 黑名单列表
        $start = --$page * $pageSize;
        $blacks = M('black')->where($condition)->order('id DESC')->limit("{$start}, {$pageSize}")->select();

		// 类型下拉框
        $types = array(
            1 => array('name' => '高风险项目'),
            2 => array('name' => '企业黑名单'),
            3 => array('name' => '投资人黑名单')
        );
		$typeSelect = helper_form::select($type, $types, 'id="type"', '全部');

		// 是否显示下拉框
        $isShows = array(1 => array('name' => '隐藏'), 2 => array('name' => '显示'));
		$isShowSelect = helper_form::select($isShow, $isShows, 'id="is_show"', '全部');

        // 平台下拉框
        $platformSelect = model_cooperationPlatform::platformSelect($platform, '全部');

        $assignData = array(
            'types' => $types,
            'typeSelect' => $typeSelect,
            'isShowSelect' => $isShowSelect,
            'blacks' => $blacks,
            'pagerHtml' => $pagerHtml,
            'params' => $this->_request(),
            'platforms' => D('cooperationPlatform')->getValidPlatforms(),
            'platformSelect' => $platformSelect
        );
        $this->assign($assignData);
		$this->display();
	}

    /**
     * 添加黑名单
     */
    public function actionAdd()
    {
        $id = intval($this->_request('id'));
        $do = intval($this->_request('do', 0));

        $conf = array(
            'table' => 'black', 
            'table_field' => 'cover', 
            'exts' => array('jpg', 'jpeg', 'png'),
            'savepath' => 'black/' . date('Y/md'),
        );

        // 增加操作
        if ($do) {
            $isShow = intval($this->_request('is_show'));
            $type = intval($this->_request('type'));
            $cooperationId = intval($this->_request('cooperation_id'));
            $username = trim($this->_request('username'));
            $relation = trim($this->_request('relation'));
            $pid = intval($this->_request('pid'));
            $legal = trim($this->_request('legal'));
            $projectName = trim($this->_request('project_name'));
            $province = trim($this->_request('province'));
            $city = trim($this->_request('city'));
            $cardId = trim($this->_request('card_id'));
            $gender = intval($this->_request('gender'));
            $amount = trim($this->_request('amount'));
//            $reason = trim($this->_request('reason'));
            $reason = trim($_REQUEST['reason']);
//            $content = trim($this->_request('content'));
            $content = trim($_REQUEST['content']);
            $projectStatus = trim($this->_request('project_status'));
//            $projectDesc = trim($this->_request('project_desc'));
            $projectDesc = trim($_REQUEST['project_desc']);
//            $problem = trim($this->_request('problem'));
            $problem = trim($_REQUEST['problem']);
//            $progress = trim($this->_request('progress'));
            $progress = trim($_REQUEST['progress']);
//            $followupProgress = trim($this->_request('followup_progress'));
            $followupProgress = trim($_REQUEST['followup_progress']);
            $recover = trim($_REQUEST['recover']);
            $regTime = strtotime(trim($this->_request('reg_time')));
            $publicityTime = strtotime(trim($this->_request('publicity_time')));
            $problemTime = strtotime(trim($this->_request('problem_time')));
            $progressTime = strtotime(trim($this->_request('progress_time')));
            $cover = trim($this->_request('cover_val'));
            $norm = $this->_request('black_norm') ? trim(implode(',', $this->_request('black_norm'))) : '';

            $data = array(
                'is_show' => $isShow,
                'type' => $type,
                'cooperation_id' => $cooperationId,
                'username' => $username,
                'relation' => $relation,
                'pid' => $pid,
                'project_name' => $projectName,
                'legal' => $legal,
                'province' => $province,
                'city' => $city,
                'card_id' => $cardId,
                'gender' => $gender,
                'amount' => $amount,
                'reg_time' => $regTime,
                'publicity_time' => $publicityTime,
                'reason' => $reason,
                'norm' => $norm,
                'content' => $content,
                'project_status' => $projectStatus,
                'project_desc' => $projectDesc,
                'problem' => $problem,
                'progress' => $progress,
                'followup_progress' => $followupProgress,
                'recover' => $recover,
                'problem_time' => $problemTime,
                'progress_time' => $progressTime,
                'add_time' => time()
            );
            if ($cover) {
                $data['cover'] = $cover;
            }
            // 编辑
            if ($id) {
                $condition = array('id' => $id);
                M('black')->where($condition)->save($data);
            }
            // 新增加
            else {
                $id = M('black')->data($data)->add();
            }

            // 更新诚信体系信息
            if ($type == 2) {
                $this->setCredibility();
            }

            $this->success('保存成功');
        }


        if ($id) {
            $condition = array('id' => $id);
            $black = M('black')->where($condition)->find();
            if (!$black) {
                $this->error('您所编辑的黑名单不存在');
            }
            $black['norm'] = explode(',', $black['norm']);
            $this->assign('black', $black);

            // 获取项目名称
            if ($black['pid']) {
                $condition = array('id' => $black['pid']);
                $project = M('project')->where($condition)->field('name')->find();
                if ($project) {
                    $this->assign('project', $project);
                }
            }
        }

		// 类型下拉框
        $types = array(
            1 => array('name' => '高风险项目'),
            2 => array('name' => '企业黑名单'),
            3 => array('name' => '投资人黑名单')
        );
		$typeSelect = helper_form::select(isset($black) ? $black['type'] : 0, $types, 'name="type"');
        $coverUpload = helper_form::uploadfile('cover', $conf);

		// 性别下拉框
        $types = array(
            1 => array('name' => '男'),
            2 => array('name' => '女')
        );
        $genderSelect = helper_form::select(isset($black) ? $black['gender'] : 0, $types, 'name="gender"');

        // 注册时间插件
		$regTimeInput = helper_form::date('reg_time', date('Y-m-d H:i', isset($black) ? $black['reg_time'] : time()), "size='100'");
        // 公示时间插件
		$publicityTimeInput = helper_form::date('publicity_time', date('Y-m-d H:i', isset($black) ? $black['publicity_time'] : time()), "size='100'");
        // 问题出现时间插件
		$problemTimeInput = helper_form::date('problem_time', date('Y-m-d H:i', isset($black) ? $black['problem_time'] : time()), "size='100'");
        // 处理进度时间插件
		$progressTimeInput = helper_form::date('progress_time', date('Y-m-d H:i', isset($black) ? $black['progress_time'] : time()), "size='100'");
        // 黑名单原因及详情
        $reasonEditor = helper_form::editor('reason', 'reason', isset($black) ? $black['reason'] : '', 'base');
        // 现阶段问题
        $problemEditor = helper_form::editor('problem', 'problem', isset($black) ? $black['problem'] : '', 'base');
        // 问题进度
        $progressEditor = helper_form::editor('progress', 'progress', isset($black) ? $black['progress'] : '', 'base');
        // 后续进展及项目处理方案
        $followupProgressEditor = helper_form::editor('followup_progress', 'followup_progress', isset($black) ? $black['followup_progress'] : '', 'base');
        // 富文本(用来编辑企业黑名单详情及证据图)
        $blackEditor = helper_form::editor('content', 'content', isset($black) ? $black['content'] : '', 'base');
        // 富文本(追讨报道)
        $recoverEditor = helper_form::editor('recover', 'recover', isset($black) ? $black['recover'] : '', 'base');
        // 获取省列表
		$provinces = D('area')->getChildTree(0);
        // 获取市列表
        $citys = array();
        if (!empty($black) && $black['province']) {
            $citys = D('area')->getChildTree(isset($black) ? $black['province'] : 0);
        }
        // 平台下拉框
        $platformSelect = model_cooperationPlatform::platformSelect($black ? $black['cooperation_id'] : 1);
        
        $this->initCredibility();

        $assignData = array(
            'typeSelect' => $typeSelect,
            'genderSelect' => $genderSelect,
            'coverUpload' => $coverUpload,
            'publicityTimeInput' => $publicityTimeInput,
            'regTimeInput' => $regTimeInput,
            'problemTimeInput' => $problemTimeInput,
            'progressTimeInput' => $progressTimeInput,
            'provinces' => $provinces,
            'citys' => $citys,
            'blackEditor' => $blackEditor,
            'reasonEditor' => $reasonEditor,
            'problemEditor' => $problemEditor,
            'progressEditor' => $progressEditor,
            'followupProgressEditor' => $followupProgressEditor,
            'recoverEditor' => $recoverEditor,
            'norms' => model_black_black::$norms,
            'platformSelect' => $platformSelect
        );
        $this->assign($assignData);
        helper_view::addCss('v2/css/jquery-ui/jquery-ui-1.9.2.custom.css');
        helper_view::addJs('v2/js/common/jquery-ui-1.9.2.custom.min.js');
        $this->display();
    }

    /**
     * 删除
     */
    public function actionDel()
    {
        $ids = trim($this->_request('ids'), '-');

        if (!$ids) {
            $this->error('请选择您要删除的记录');
        }
        if (strpos($ids, '-') === false) {
            $ids = array(intval($ids));
        } else {
            $ids = explode('-', $ids);
            $arr = array();
            foreach ($ids as $val) {
                $arr[] = intval($val);
            }
            $ids = $arr;
        }

        $condition = array('id' => array('in', $ids));
        if (M('black')->where($condition)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 设置诚信体系信息
     */
    public function setCredibility()
    {
        $pid = intval($this->_request('pid'));
        $allows = array('base', 'stockright', 'directors', 'branch', 'change', 'followup');
        $list = array();
        foreach ($allows as $val) {
            $arr = $this->_request($val);
            foreach ($arr as $key => $val2) {
                $val2 = trim($val2);
                if (strpos($key, 'time') !== false) {
                    $val2 = strtotime($val2);
                }
                $list[$val][$key] = $val2;
            }
        }
        $data = array('pid' => $pid, 'is_delete' => 0, 'add_time' => time());
        foreach ($list as $key => $val) {
            $data["{$key}_info"] = serialize($val);
        }

        // 如果存在则更新
        $condition = array('is_delete' => 0, 'pid' => $pid);
        if (M('credibility')->where($condition)->find()) {
            return M('credibility')->where($condition)->save($data);
        } else {
            return M('credibility')->data($data)->add();
        }
    }

    /**
     * 获取诚信体系信息
     */
    public function initCredibility()
    {
        $id = intval($this->_request('id'));
        $allows = array('base', 'stockright', 'directors', 'branch', 'change', 'followup');

        $condition = array('id' => $id);
        $black = M('black')->where($condition)->find();

        $condition = array('is_delete' => 0, 'pid' => $black['pid']);
        $credibility = M('credibility')->where($condition)->find();

        if ($credibility) foreach ($allows as $val) {
            $key = "{$val}_info";
            $credibility[$key] = unserialize(trim($credibility[$key]));
        }
        $establishTime = $credibility ? $credibility['base_info']['establish_time'] : 0;
        $businessTime = $credibility ? $credibility['base_info']['business_time'] : 0;
        $registrationTime = $credibility ? $credibility['base_info']['registration_time'] : 0;
        $actualTime = $credibility ? $credibility['stockright_info']['actual_time'] : 0;
        $time = $credibility ? $credibility['change_info']['time'] : 0;

        $establishTimer = helper_form::date("base[establish_time]", $establishTime ? date('Y-m-d H:i', $establishTime) : '', "size='100'");
        $businessTimer = helper_form::date("base[business_time]", $businessTime ? date('Y-m-d H:i', $businessTime) : '', "size='100'");
        $registrationTimer = helper_form::date("base[registration_time]", $registrationTime ? date('Y-m-d H:i', $registrationTime) : '', "size='100'");
        $actualTimer = helper_form::date("stockright[actual_time]", $actualTime ? date('Y-m-d H:i', $actualTime) : '', "size='100'");
        $timer = helper_form::date("change[time]", $time ? date('Y-m-d H:i', $time) : '', "size='100'");

        $this->assign('establishTimer', $establishTimer);
        $this->assign('businessTimer', $businessTimer);
        $this->assign('registrationTimer', $registrationTimer);
        $this->assign('actualTimer', $actualTimer);
        $this->assign('timer', $timer);
        $this->assign('credibility', $credibility);
    }

    public function actionAjaxCitys()
    {
        $province = intval($this->_request('province', 0));
        // 获取市列表
		$citys = D('area')->getChildTree($province);
        $this->ajaxReturn(array('citys' => $citys));
    }

    /**
     * 项目搜索下拉框
     */
    public function actionAjaxSearch()
    {
        $name = trim($this->_request('name', ''));

        $list = array();
        if ($name) {
            $ids = implode(',', model_area_area::$specialCitys);
            $condition = array(
                'name' => array('like', "{$name}%"),
                'status' => array('in', array(2, 4, 5, 6)),
            );
            $list = M('project')->where($condition)->field('id, name, status')->select();
        }
        exit(json_encode($list));
    }
}
