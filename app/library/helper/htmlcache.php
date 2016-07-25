<?php
// +----------------------------------------------------------------------
// | 静态缓存类 支持静态缓存规则定义
// +----------------------------------------------------------------------

class helper_htmlcache {
    static private $cacheTime = null; //缓存有效期（支持函数）
    static private $requireCache = false;    //是否需要缓存

    // 判断是否需要静态缓存
    static private function requireHtmlCache() {
        // 分析当前的静态规则
         $htmls = C('_html_'); //读取静态规则
         if(!empty($htmls)) {
            // 静态规则文件定义格式 actionName=>array(‘静态规则’,’缓存时间’,’附加规则')
            // 'read'=>array('{id},{name}',60,'md5') 必须保证静态规则的唯一性 和 可判断性
            // 检测静态规则
            if(isset($htmls[CONTROLLER_NAME.':'.ACTION_NAME])) {
                $html   =   $htmls[CONTROLLER_NAME.':'.ACTION_NAME];   // 某个控制器的操作的静态规则
            }elseif(isset($htmls[CONTROLLER_NAME.':'])){// 某个控制器的静态规则
                $html   =   $htmls[CONTROLLER_NAME.':'];
            }elseif(isset($htmls[ACTION_NAME])){
                $html   =   $htmls[ACTION_NAME]; // 所有操作的静态规则
            }elseif(isset($htmls['*'])){
                $html   =   $htmls['*']; // 全局静态规则
            }elseif(isset($htmls['empty:index']) && !class_exists(CONTROLLER_NAME.'_controller')){
                $html   =    $htmls['empty:index']; // 空模块静态规则
            }elseif(isset($htmls[CONTROLLER_NAME.':_empty']) && self::isEmptyAction(CONTROLLER_NAME,ACTION_NAME)){
                $html   =    $htmls[CONTROLLER_NAME.':_empty']; // 空操作静态规则
            }
            if(!empty($html)) {
                self::$requireCache = true; // 需要缓存
                // 解读静态规则
                $rule    = $html[0];
                // 以$_开头的系统变量
                $rule  = preg_replace('/{\$(_\w+)\.(\w+)\|(\w+)}/e',"\\3(\$\\1['\\2'])",$rule);
                $rule  = preg_replace('/{\$(_\w+)\.(\w+)}/e',"\$\\1['\\2']",$rule);
                // {ID|FUN} GET变量的简写
                $rule  = preg_replace('/{(\w+)\|(\w+)}/e',"\\2(\$_GET['\\1'])",$rule);
                $rule  = preg_replace('/{(\w+)}/e',"\$_GET['\\1']",$rule);
                // 特殊系统变量
                $rule  = str_ireplace(
                    array('{:app}','{:controller}','{:action}','{:module}'),
                    array(APP_NAME,CONTROLLER_NAME,ACTION_NAME,MODULE_NAME),
                    $rule);
                // {|FUN} 单独使用函数
                $rule  = preg_replace('/{|(\w+)}/e',"\\1()",$rule);
                if(!empty($html[2])) $rule    =   $html[2]($rule); // 应用附加函数
                self::$cacheTime = isset($html[1])?$html[1]:C('sys_html_cachetime'); // 缓存有效期
                // 当前缓存文件
                define('HTML_FILE_NAME',C('sys_html_path').'/'. $rule.C('sys_html_suffix'));
                return true;
            }
        }
        // 无需缓存
        return false;
    }


    //读取静态缓存
    static function readHTMLCache()
    {
        if(self::requireHtmlCache() && self::checkHTMLCache(HTML_FILE_NAME,self::$cacheTime)) { //静态页面有效
            if(C('sys_html_read')==1) {
                // 重定向到静态页面
                redirect(str_replace(array(realpath($_SERVER["DOCUMENT_ROOT"]),"\\"),array('',"/"),realpath(HTML_FILE_NAME)));
            }else {
                // 读取静态页面输出
                readfile(HTML_FILE_NAME);
                exit();
            }
        }
        return ;
    }

    /**
     * 写入静态缓存
     * @param string $content 页面内容
     */
    static public function writeHTMLCache($content)
    {
        if(self::$requireCache) {
            //静态文件写入
            // 如果开启HTML功能 检查并重写HTML文件
            // 没有模版的操作不生成静态文件
            if(!is_dir(dirname(HTML_FILE_NAME)))
                mk_dir(dirname(HTML_FILE_NAME));
            if( false === file_put_contents( HTML_FILE_NAME , $content ))
                throw_exception(L('_CACHE_WRITE_ERROR_'));
        }
        return ;
    }

    /**
     * 检查静态HTML文件是否有效 如果无效需要重新更新
     * @param string $cacheFile  静态文件名
     * @param integer $cacheTime  缓存有效期
     * @return boolen
     */
    static public function checkHTMLCache($cacheFile='',$cacheTime='')
    {
        if(!is_file($cacheFile)){
            return false;
        }elseif (filemtime(C('app_tpl_filename')) > filemtime($cacheFile)) {
            // 模板文件如果更新静态文件需要更新
            return false;
        }elseif(!is_numeric($cacheTime) && function_exists($cacheTime)){
            return $cacheTime($cacheFile);
        }elseif ($cacheTime != -1 && time() > filemtime($cacheFile)+$cacheTime) {
            // 文件是否在有效期
            return false;
        }
        //静态文件有效
        return true;
    }

    //检测是否是空操作
    static private function isEmptyAction($controller,$action){
        $className =  $controller.'_controller';
        $class = new $className;
        return !method_exists($class,$action);
    }
}
?>