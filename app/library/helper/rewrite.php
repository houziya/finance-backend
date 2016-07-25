<?php

/**
 *  URL重写规则类
 * @author Jimmy Fu
 *  2015-11-26
 */

class helper_rewrite
{


    /**
     *  执行重写规则，主函数
     *  @param array  $u = dispatcher::formatUrlStr($url); //格式化url字符串
     *  @param array  $param  url的参数
     *  @return string  返回除域名外的URL链接，如 /company_1.html
     * 
     */
    static public function getReWriteUrl($u, $param = array())
    {

        $moduleName = strtolower($u['m']);
        $method = $moduleName . 'Rewrite'; //模块方法
        if (false == method_exists('helper_rewrite', $method))
        {
            $url = self::defaultRewrite($u, $param); //调用默认重写规则
            //容错，如果url为空
        } else
        {
            $url = self::$method($u, $param);  //重写规则制定到不同的以模块加Rewrite的方法里边，将来拓展类
            if (empty($url))
            {
                $url = self::defaultRewrite($u, $param);
            }
        }
       
        return $url;

    }

    /**
	 *  默认重写
	 */
	static public function defaultRewrite($u, $params) {
		$s = C('sys_url_depr');
		if (empty($u['get'])) {
			$u['get'] = array();
		}
		$u['get'] = array_merge($u['get'], $params);
		$get = '';
		if (!empty($u['get'])) {
			foreach ($u['get'] as $k => $v) {
				if ($v === '') continue;
				$get .= $s . $k . $s . $v;
			}
		}
		$url = '/' . $u['c'] . $s . $u['a'] . $get;
		if (C('sys_url_suffix')) {
			$url = rtrim($url, $s);
			$url .= C('sys_url_suffix');
		}

		return $url;
	}

	/**
     *  www模块的重写规则
     */
    static public function wwwRewrite($u, $params)
    {
       
        if (self::isArticle($u))
        {
            $url = self::reWriteUrl($u);
        } else
        { //匹配不到，则调用默认重写规则
            $url = self::defaultRewrite($u, $params);
        }
        return $url;
    }

    /**
     *  search模块重写部分
     *  把URL重写部分拆分到不同的方法
     */
    static public function searchRewrite($u, $params)
    {
        
        $controllerName = strtolower($u['c']); //控制器名称
        //重写项目
        if( $controllerName ==  'project'){  //是否是项目
            $url = self::reWriteSearchProjectUrl($u);
        }
        //重写黑名单
        if($controllerName ==  'black'){
            $url = self::reWriteSearchBlackUrl($u);
        }
        //重写诚信查询
        if($controllerName ==  'rrtcredit'){
            $url = self::reWriteSearchRrtcreditUrl($u);
        }
        //重写明星榜
        if($controllerName ==  'star'){
            $url = self::reWriteSearchStartUrl($u);
        }
        
        return $url;

    }
    /**
     *  wap重写部分
     *  把URL重写部分拆分到不同的方法
     */
    static public function wapRewrite($u, $params)
    {
        return  $url = self::defaultRewrite($u, $params);
    }


    // =============== 文章url重写 ================================================================

    static function reWriteUrl($u)
    {
        $actionName = strtolower($u['a']);
        $urlGetParams = $u['get'];

        if ($actionName == 'index')
        {
            $urlInfo = array('/article');

            if ($urlGetParams['type_id'])
            {
                $urlInfo[] = $urlGetParams['type_id'];
            }
            $urlInfo[] = $urlGetParams['p'] ? $urlGetParams['p'] : 1;
            $url = implode('_', $urlInfo) . '/';
        } elseif ($actionName == 'hycontent')
        {
            $id = isset($urlGetParams['id']) ? $urlGetParams['id'] : 0;
            $url = "/article_{$id}.html";
        }

        return $url;
    }

    static public function isArticle($u)
    {
        $actions = array('industryinfo' => array('index', 'hycontent'));
        $controllerName = strtolower($u['c']);
        $actionName = strtolower($u['a']);
        if (!$controllerName)
        {
            return false;
        }
        if (!$actionName)
        {
            $actionName = C('sys_default_action');
        }
        if (!isset($actions[$controllerName]))
        {
            return false;
        }
        if (!in_array($actionName, $actions[$controllerName]))
        {
            return false;
        }
        return true;
    }

   
     
     /**
     *  黑名单URL重写 
     *  Jimmy Fu 2015-11-26
     *  
     */ 
    static public function reWriteSearchBlackUrl($u)
    {
         //需要重写的actions
        $actions = array(
                'index', //需要重写的action
                'detail', //需要重写的action
                );
        $actionName = strtolower($u['a']);
        $urlGetParams = $u['get'];
        if (!$actionName)
        {
            $actionName = C('sys_default_action');
        }
        if(!in_array($actionName,$actions)){
            return ''; //如果不在重写范围，那么返回空
        }
        $url = ''; //返回重写后的url
        //如果是公司列表
        if ($actionName == 'index')
        {
            if($urlGetParams['type']){
                
               if($urlGetParams['p']){ //如果带分页
                
                    if($urlGetParams['keyword']){
                         $url = "/black-{$urlGetParams['type']}-{$urlGetParams['p']}/{$urlGetParams['keyword']}";
                    }else{  //如果带分页，不含关键字
                         $url = "/black-{$urlGetParams['type']}-{$urlGetParams['p']}";
                    } 
               }else{
                    
                    if($urlGetParams['keyword']){ //带关键字
                        $url = "/black-{$urlGetParams['type']}/{$urlGetParams['keyword']}";
                   }else{
                       $url = "/black-{$urlGetParams['type']}/";
                   }
               }
                
            }else{
                $url = "/black/";
            }
           
        }
        if ($actionName == 'detail')
        {
            $id = isset($urlGetParams['id']) ? $urlGetParams['id'] : 0;
            $url = "/black_{$id}.html";
        }

        return $url;
    }



    
     /**
     *  明星榜URL重写 
     *  Jimmy Fu 2015-11-26
     *  
     */ 
    static public function reWriteSearchStartUrl($u)
    {
         //需要重写的actions
        $actions = array(
                'index', //需要重写的action
                //'projectdetail',
                //'investordetail',
                //'subwebdetail'
                );
        $actionName = strtolower($u['a']);
        $urlGetParams = $u['get'];
        if (!$actionName)
        {
            $actionName = C('sys_default_action');
        }
        if(!in_array($actionName,$actions)){
            return ''; //如果不在重写范围，那么返回空
        }
        $url = ''; //返回重写后的url
        
        if ($actionName == 'index'){
            if($urlGetParams['platform']){ //如果指定平台
                if($urlGetParams['starType']){ //如果指定类型
                    if($urlGetParams['p']){
                         $url = "/star-{$urlGetParams['platform']}/top-{$urlGetParams['starType']}-{$urlGetParams['p']}/";
                    }else{
                         $url = "/star-{$urlGetParams['platform']}/top-{$urlGetParams['starType']}/";
                    }
                      
                }else{  //如果没有选类型
                       $url = "/star-{$urlGetParams['platform']}/";
                }
                
            }else{
                  $url = "/star/";
            }
            
        }
        return $url;
    }
    // ===============================================================================

    /**
     *  众筹诚信URL重写 
     *  Jimmy Fu 2015-11-26
     *  
     */ 
    static public function reWriteSearchRrtcreditUrl($u)
    {
         //需要重写的actions
        $actions = array(
                'companylist', //需要重写的action
                'companydetail', 
                'personalsearchresult'  //个人结果页
                );
        $actionName = strtolower($u['a']);
        $urlGetParams = $u['get'];
        if (!$actionName)
        {
            $actionName = C('sys_default_action');
        }
        if(!in_array($actionName,$actions)){
            return ''; //如果不在重写范围，那么返回空
        }
        $url = ''; //返回重写后的url

        //如果是公司列表
        if ($actionName == 'companylist')
        {
            if($urlGetParams['p']){ //如果带分页
                $url = "/companylist_{$urlGetParams['p']}/{$urlGetParams['keyword']}";
            }else{
                $url = "/companylist/{$urlGetParams['keyword']}"; 
            }
           
        } elseif ($actionName == 'companydetail')
        {
            $id = isset($urlGetParams['id']) ? $urlGetParams['id'] : 0;
            $keyword = '';
            $url = empty($keyword) ? "/company_{$id}.html" : "/company_{$id}_{$keyword}.html";
        } elseif ($actionName == 'personalsearchresult')
        {
            //生成个人搜索详情页URL规则
            $keyword = isset($urlGetParams['keyword']) ? $urlGetParams['keyword'] : '';
            $url = "/person/{$keyword}.html";
        }

        return $url;
    }

    /** 
     *  重写项目URL
     *  
     */ 
    static public function reWriteSearchProjectUrl($u)
    {
        //需要重写的actions
        $actions = array(
                'projectsearch', //需要重写的action
                );
        $controllerName = strtolower($u['c']);
        $actionName = strtolower($u['a']);
        $urlGetParams = $u['get'];
        if (!$actionName)
        {
            $actionName = C('sys_default_action');
        }
        if(!in_array($actionName,$actions)){
            return ''; //如果不在重写范围，那么返回空
        }
        $url = ''; //返回重写后的url
        //如果是公司列表
        if ($actionName == 'projectsearch')
        {
            //如果没有参数
            if(empty($urlGetParams)){
                 $url = "/projectlist/";
            }
            if(!empty($urlGetParams['p']) && empty($urlGetParams['keyword'])){
                $url = "/projectlist-{$urlGetParams['p']}/";
            }
            if(!empty($urlGetParams['keyword']) ){
                if(empty($urlGetParams['p'])){
                    $url = "/projectlist/{$urlGetParams['keyword']}";
                }else{
                    $url = "/projectlist-{$urlGetParams['p']}/{$urlGetParams['keyword']}";
                }
                
            }
           
        }
        return $url;
    }


}

?>