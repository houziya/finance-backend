<?php
/**
 * 关于公司控制器
 * @author Baijiansheng
 */
class controller_app_help extends controller_app_abstract {

    public function __construct() {
        parent::__construct();
    }

    /*
     * 新手指引
     * @return array
     * @author Baijiansheng
     */
    public function actionNewbieGuide(){
        $condition = array('id'=>8008);
        // 项目列表
        $fields = '*, finance_amount / finance_total AS progress';

        $cacheKey = "model_project_getProjects_" . helper_cache::makeKey($condition, 0, 1, $fields);
        $callback = array(D('project'), 'getProjects');
        $projects = helper_cache::getSmartCache($cacheKey, $callback, 30, array($condition, 0, 1, $fields, true));

        $cycleTime = $projects[0]['amount_begin_time'] + $projects[0]['funding_cycle'] * 3600 * 24;
        $projects[0]['days'] = ceil(($cycleTime - time()) / (3600 * 24));
        $projects[0]['address2'] = D('project/project')->getProjectArea($projects[0]['id']);
        // 获取项目所属行业
        $projects[0]['trade'] = D('project/project')->getProjectTrade($projects[0]['id']);

        $projects[0]['percent'] = $projects[0]['finance_total'] > 0 ? floor($projects[0]['finance_amount'] * 100 / $projects[0]['finance_total']) : 0;

        helper_view::addCss('v2/css/app_base/news.css');
        $this->setTitle('积分规则');
        $this->assign('projects', $projects);
        $this->display();
    }

}
