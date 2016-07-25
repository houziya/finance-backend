<?php
// +----------------------------------------------------------------------
// | Widget类 抽象类
// +----------------------------------------------------------------------

abstract class widget {

	// 使用的模板引擎 每个Widget可以单独配置不受系统影响
	protected $template =  '';

	/**
     +----------------------------------------------------------
     * 渲染输出 render方法是Widget唯一的接口
     * 使用字符串返回 不能有任何输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data  要渲染的数据
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	abstract public function render($data);

	/**
     +----------------------------------------------------------
     * 渲染模板输出 供render方法内部调用
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile  模板文件
     * @param mixed $var  模板变量
     * @param string $charset  模板编码
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	protected function renderFile($templateFile='',$var='',$charset='utf-8') {
		ob_start();
		ob_implicit_flush(0);
		if(!file_exists($templateFile)){
			// 自动定位模板文件
			$name   = substr(get_class($this),0,-6);
			$filename   =  empty($templateFile)?$name:$templateFile;
			$templateFile = APP_PATH.'/'.MODULE_NAME.'/widget/'.$name.'/'.$filename.C('sys_tpl_suffix');
			if(!file_exists($templateFile))
				throw_exception(L('_TEMPLATE_NOT_EXIST_').'['.$templateFile.']');
		}

		$view = getInstanceOf('view');
		if(!$view->checkCache($templateFile)) {
			// 缓存有效 直接载入模板缓存 模板阵列变量分解成为独立变量
			if(!empty($var)) extract($var, EXTR_OVERWRITE);
			//载入模版缓存文件
			include C('app_tpl_cachepath').'/'.md5($templateFile).C('sys_tpl_cache_suffix');
		}else{
			// 缓存无效 重新编译
			$tpl = getInstanceOf('template');
			// 编译并加载模板文件
			$tpl->load($templateFile,$var,$charset);
		}

		$content = ob_get_clean();
		return $content;
	}
}
?>