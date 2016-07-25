<?php
// 分页处理

class helper_page {
    // 是否是ajax方式
    public $ajaxClick = " onclick='return true'";
	// 起始行数
	public $firstRow;
	// 列表每页显示行数
	public $listRows;
	// 页数跳转时要带的参数
	public $parameter;
	// 分页总页面数
	public $totalPages;
	// 总行数
	protected $totalRows;
	// 当前页数
	protected $nowPage;
	// 分页栏每页显示的页数
	protected $rollPage;
	// 分页配置信息
	protected $config  = array();
	// 分页条主题
	protected $theme  = array(
		'cn'=> array( 'prev'=>'上一页', 'next'=>'下一页', 'first'=>'首页', 'last'=>'末页', 'theme'=>' %totalRow% 条记录 %nowPage%/%totalPage% 页 %firstPage%  %prePage%  %linkPage%  %nextPage%  %lastPage%'),
		'en'=> array( 'prev'=>'Prev', 'next'=>'Next', 'first'=>'First', 'last'=>'Last', 'theme'=>' %totalRow% records %nowPage%/%totalPage% pages %firstPage%  %prePage%  %linkPage%  %nextPage%  %lastPage%'),
	);

	/**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------------------
     * @param int $totalRows  总的记录数
	 * @param int $nowpage  当前第几页
     * @param int $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     * @param boolean $ajax  是否是ajax方式，如果true，那么连接将不进行跳转
     +----------------------------------------------------------------------
     */
	public function __construct($totalRows,$nowpage='',$listRows='',$parameter=array(), $ajax = false) {
		$this->setTheme('cn');
		$this->init($totalRows,$nowpage,$listRows,$parameter,$ajax);
	}

	//初始化
	public function init($totalRows,$nowpage='',$listRows='',$parameter=array(), $ajax = false){
		$this->setParameter($parameter);
		$this->totalRows = (int)$totalRows; //总条数

        // 如果当前页变量不大于0，那么就获取GET中的
        if ($nowpage < 1) {
            $page = isset($_GET[C('var_page')]) ? intval($_GET[C('var_page')]) : 1;
            $page = $page > 0 ? $page : 1;
            $nowpage = $page;
        }

		$this->nowPage  = $nowpage; //当前页
		$this->rollPage = C('sys_page_rollpage'); //分页条显示页数
		$this->listRows = !empty($listRows)?$listRows:C('sys_page_listrows'); //每页条数
		$this->totalPages = (int)ceil($this->totalRows/$this->listRows);     //总页数
		//$this->totalPages = $this->totalPages ? $this->totalPages : 1;
		
		if($this->totalPages && $this->nowPage>$this->totalPages) {
			$this->nowPage = $this->totalPages;
		}
		$this->firstRow = $this->listRows*($this->nowPage-1);
        if ($ajax) {
            $this->ajaxClick = " onclick='return false'";
        }
	}

	//设置分类参数
	public function setConfig($name,$value) {
		$this->config[$name] = $value;
	}

	//设置分类条主题样式
	public function setTheme($name){
		if(!isset($this->theme[$name])) return false;
		foreach($this->theme[$name] as $k=>$v){
			$this->config[$k] = $v;
		}
        return true;
	}

	//获取分页数组
	public function getInfo(){
		$arr = array();
		$arr['firstRow'] = $this->firstRow;
	}

	/**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
	public function show($type='string') {
		$firstNum = 1;
		$preNum = $this->nowPage - 1;
		$nextNum = $this->nowPage + 1;
		$lastNum = $this->totalPages;
		$linkPage = $this->pageNum();

		if ($this->nowPage == 1) {
			$firstPage = '<span>'.$this->config['first'].'</span>';
			$prePage = '';//'<span>'.$this->config['prev'].'</span>';
		}else{
			$firstPage = '<a href="'.$this->getUrl($firstNum).'"' . $this->ajaxClick . '>'.$this->config['first'].'</a>';
			$prePage = '<a href="'.$this->getUrl($preNum).'"' . $this->ajaxClick . '>'.$this->config['prev'].'</a>';
		}

		if ($this->totalPages == $this->nowPage || $this->totalPages == 0) {
			$nextPage = '';//'<span>'.$this->config['next'].'</span>';
			$lastPage = '<span>'.$this->config['last'].'</span>';
		}else{
			$nextPage = '<a href="'.$this->getUrl($nextNum).'"' . $this->ajaxClick . '>'.$this->config['next'].'</a>';
			$lastPage = '<a href="'.$this->getUrl($lastNum).'"' . $this->ajaxClick . '>'.$this->config['last'].'</a>';
		}

        $pageStr = '';
        $result = array();

        // 如果获取的是字符串
        if ($type == 'string') {
            // 如果没有数据根本无需返回首页、尾页的字符串，太丑了
            if ($this->totalRows) {
                $pagerTheme = array(
                    '%nowPage%',
                    '%totalRow%',
                    '%totalPage%',
                    '%firstPage%',
                    '%prePage%',
                    '%linkPage%',
                    '%nextPage%',
                    '%lastPage%'
                );
                $themeEntity = array(
                    $this->nowPage,
                    $this->totalRows,
                    $this->totalPages,
                    $firstPage,
                    $prePage,
                    $linkPage,
                    $nextPage,
                    $lastPage
                );
                $pageStr = str_replace($pagerTheme, $themeEntity, $this->config['theme']);
            }
        }
        // 如果获取的是数组
        else {
            $result['first'] = array('page'=>$firstNum,'url'=>$this->getUrl($firstNum));
            $result['pre'] = array('page'=>$preNum,'url'=>$this->getUrl($preNum));
            $result['link'] = $linkPage;
            $result['next'] = array('page'=>$nextNum,'url'=>$this->getUrl($nextNum));
            $result['last'] = array('page'=>$lastNum,'url'=>$this->getUrl($lastNum));
            $result['nowpage'] = $this->nowPage;
            $result['totalpage'] = (int)$this->totalPages;
            $result['total'] = $this->totalRows;
            $result['limit1'] = $this->firstRow;
            $result['limit2'] = $this->listRows;
        }

		return $type=='string' ? $pageStr : $result;
	}

	//对页数进行循环显示，如 1 2 3 4 5
	public function pageNum(){

		//循环显示处理
		$page = $this->nowPage;//当前页
		$rollPage = $this->rollPage;//每次分页数
		$totalPages = $this->totalPages;//总页数
		$start = $page-$rollPage;
		$end = $page+$rollPage-1;
		//处理分页开始数
		if ($start <= 0) {
			$end = $end + abs($start) + 1;
			$start = 1;
		}
		//处理分页结束数
		if ($end > $totalPages) {
			$num = $end - $totalPages;//差数
			$end = $end - $num;
			$start = $start - $num;
			if ($start <= 0) {
				$start = 1;
			}
		}
		$urlStr = "";
		for ($i=$start; $i<=$end; $i++){
			if ($i == $page) {
				$urlStr.='<span class="current">'.$i.'</span> ';
			}else{
				$urlStr.='<a href="'.$this->getUrl($i).'"' . $this->ajaxClick . '>'.$i.'</a> ';
			}
		}
		return $urlStr;
	}

	//得到url地址
	protected function getUrl($page){
        $get = $this->parameter;

        if($page <> 1){
            //默认第一页不用带分页
            $get[C('var_page')] = $page;
        }
        //获取MVC名称
        $_M = MODULE_NAME;
        if(!empty($get[C('var_module')])){
            $_M = $get[C('var_module')];
            unset($get[C('var_module')]);
        }
        $_C = CONTROLLER_NAME;
        if(!empty($get[C('var_controller')])){
            $_C = $get[C('var_controller')];
            unset($get[C('var_controller')]);
        }
        $_A = ACTION_NAME;
        if(!empty($get[C('var_action')])){
            $_A = $get[C('var_action')];
            unset($get[C('var_action')]);
        }
		return url("{$_M}-{$_C}/{$_A}",$get);
	}

	//设置当前get参数
	protected function setParameter($parameter){
        $get = array();
		//合并自定参数
		if (is_array($parameter)) {
			$get = array_merge(array_map('safeHtml', $_GET),$parameter);
		}
        unset($get[C('var_page')]); //删除分页参数
        $this->parameter = $get;
		return $get;
	}
}
?>