<?php
// +----------------------------------------------------------------------
// | 表单处理类
// +----------------------------------------------------------------------

class helper_form {

	//加载编辑器(Name, ID, 值, 格式, 宽度, 高度, 上传处理URL)
	public static function editor($name = 'editor', $id = 'editor', $value = '', $toolbar = 'base', $width = '100%', $height = '300px', $upload = '') {
		$url = C('url'); 
		if (!defined('INI_EDITOR')) {
			define('INI_EDITOR', true);
			$js = '<script type="text/javascript" charset="utf-8" src="' . $url['img'] . '/s/v2/js/common/editor/kindeditor-min.js?_v=' . C('web_version') .'"></script>';
		}		
		if ($toolbar == 'base') {
			$items = "[
		'source', '|', 'undo', 'redo', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
		'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
		'insertunorderedlist', '|', 'emoticons',  'link', 'unlink', 'image', 'flv','|', 'pagebreak'
		]";
		} elseif ($toolbar == 'member') {
			$items = "[
		'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
		'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
		'insertunorderedlist', '|', 'emoticons','|', 'pagebreak'
		]";
		} elseif ($toolbar == 'admin_image') {
			$items = "[
		'source', '|', 'undo', 'redo', '|', 'print', 'template', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'clearhtml', '|', 'table', 'hr', 'emoticons', 'map', 'code', 'anchor', 'link', 'unlink','|', 'pagebreak'
		]";
		} elseif ($toolbar == 'admin_article') {
			$items = "[
		'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'clearhtml', 
		'|', 'swfupload', 'pagebreak', 'table', 'hr', 'emoticons', 'map', 'code', 'anchor', 'link', 'unlink'
		]";
		} elseif ($toolbar == 'admin_software') {
			$items = "[
		'source', '|', 'undo', 'redo', '|', 'print', 'template', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'clearhtml', '|', 'table', 'hr', 'emoticons', 'map', 'code', 'anchor', 'link', 'unlink','|', 'pagebreak'
		]";
		} elseif ($toolbar == 'admin') {
			$items = "[
		'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'clearhtml', 
		'|', 'swfupload', 'pagebreak', 'table', 'hr', 'emoticons', 'map', 'code', 'anchor', 'link', 'unlink'
		]";
		} elseif ($toolbar == 'full') {
			$items = "['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
        'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
        'anchor', 'link', 'unlink', '|', 'about']";
		} else {
			$items = "[
		'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
		'insertunorderedlist', '|', 'emoticons'
		]";
		}
		$editor = 'editor_' . $id;

		$str = "<textarea name='" . $name . "' id='$id' style='width:0; height:0;visibility:hidden;'>$value</textarea>\r\n$js\r\n";
		$str .= "<script type=\"text/javascript\">\r\n";
		$str .= "var $editor;
				KindEditor.ready(function(K){
					$editor = K.create('#$id', {";
		if($toolbar == 'admin')
		{
			$str .= "filterMode: false,";//是否开启过滤模式
		}			
			$str .=		"items : $items,
						width : '$width',
						height : '$height',
						allowImageUpload : true,
						allowFlashUpload : true,
						allowMediaUpload : true,
						allowFileUpload : true,
						uploadJson : '".url('file/publiceditorupload')."'//上传处理URL
					});
				});";
		$str .= "</script>";
		return $str;
	}

	/*
	 * 上传表单
	 * @param string $field 上传表单名
	 * @param array $conf 上传参数
	 * array(
		'table' => 'project', //附件对应的表 （必填）
		'table_field' => 'img_logo', //附件对应表的字段（必填）
		'table_id' => '0', //附件对应表的自增ID
		'exts'=>'jpg|jpeg|png|gif',//附件后缀
		'saverule' => '', //文件名
		'savepath' => '', //文件路径
		'maxsize'=> 2048, //附件大小
		'thumb'=> 0, //是否开启缩略图裁剪
		'thumbwidth'=> 300, //缩略图宽
		'thumbheight'=> 300, //缩略图高
		'thumbcut'=> 0, //缩略图裁剪模式 0自动等比例裁剪 1自动按原图中心裁剪 array()手工裁剪
		'private_file' => false, //是否私密文件
	   )
	 * $param array $parame 配置参数
	 * array(
		'resize' => 0, //是否启用缩略图  0不启用  800,600启用缩略图插件
		'input_value' => '', //input框默认值
		'input_style' => '', //input框样式
		'img_show' => true, //是否展示上传图片
		'js_callback_success' => '', //上传成功 js回调函数
		'js_callback_fail' => '', //上传失败 js回调函数
	   )
	 * @return string
	 */
	static public function  uploadfile($field = '', $conf = array(), $param = array()){
		if(empty($field)) return '';
		//配置处理
		$param['resize'] = !empty($param['resize']) ? "[{$param['resize']}]" : '[0]';
		$param['input_value'] = !empty($param['input_value']) ? $param['input_value'] : '';
		$param['input_style'] = !empty($param['input_style']) ? $param['input_style'] : '';
		$param['img_show'] = !empty($param['img_show']) ? true : false;
		$param['js_callback_success'] = !empty($param['js_callback_success']) ? $param['js_callback_success'] : '';
		$param['js_callback_fail'] = !empty($param['js_callback_fail']) ? $param['js_callback_fail'] : '';
		
		$str = '';
		if(!defined('INI_UPLOADFILE')) {
			define('INI_UPLOADFILE', 1);
			helper_view::addJs(array('v2/js/common/jquery-ajaxfileupload.js','v2/js/common/global-upload.js'));
		}
		$str .= "<script type=\"text/javascript\">
			var uploadData = {
			'data': '".authcode(json_encode($conf),'ENCODE')."',
			'field': '{$field}',
			'resize':{$param['resize']}
			};
			$('#{$field}').uploadFile(uploadData,function(res){
				var rand = Math.ceil(Math.random()*10);
				if(res.status == 1) {
					var _test = res.data.url2;
						_test = _test.replace(/\&amp;/g,'&');
					$('#{$field}_val').val(res.data.url);

					res.data.url2 = res.data.url2.replace(/\&amp;/g,'&');
					$('#{$field}_img').attr('src',_test);
					{$param['js_callback_success']}
					return true;
				}else{
					alert(res.msg)
					{$param['js_callback_fail']}
					return false;
				}
			});
			</script>
			<input type=\"hidden\" name=\"{$field}_val\" value=\"{$param['input_value']}\" id=\"{$field}_val\" />
			<input type='file' name='{$field}' id='{$field}' {$param['input_style']} />";
		return $str;
	}
	
	/**
	 * 地区JS联动菜单
	 *
	 * @param int $province 默认值省
	 * @param int $city 默认值市
	 * @param int $area 默认值区
	 * @param string $province 省份select参数 name='data[province]' id='province'
	 * @param string $city 城市select参数 name='data[city]' id='city'
	 * @param string $area 地区select参数 name='data[area]' id='area'
	 * @param bool $default 增加 请选择区域 默认框
	 */
	public static function area($province = 0, $city = 0, $area = 0, $province_option = '', $city_option = '', $area_option = '', $default = false){
		if(empty($province_option)) $province_option = " name='data[province]' id='province'";
		if(empty($city_option)) $city_option = " name='data[city]' id='city'";
		if(empty($area_option)) $area_option = " name='data[area]' id='area'";
		
		//提取id参数
		preg_match_all('/id\s*=\s*([^ ]+)/is', $province_option, $match);
		if(empty($match[1][0])) return '';
		$province_id = str_replace(array("'",'"'), '', $match[1][0]);
		preg_match_all('/id\s*=\s*([^ ]+)/is', $city_option, $match);
		if(empty($match[1][0])) return '';
		$city_id = str_replace(array("'",'"'), '', $match[1][0]);
		preg_match_all('/id\s*=\s*([^ ]+)/is', $area_option, $match);
		if(empty($match[1][0])) return '';
		$area_id = str_replace(array("'",'"'), '', $match[1][0]);
		
		$str = '';
		if(!defined('AREADATA_INIT')) {
			define('AREADATA_INIT', 1);
			$url = C('url');
			$js_str = D('area')->getJsdata($default);
			$str .= '<script type="text/javascript" src="'.$url['img'].'/s/v2/js/common/area.js?_v=' . C('web_version') .'"></script>
			<script type="text/javascript">'.$js_str.'</script>';
		}
		$str .= "<select {$province_option}></select><select {$city_option}></select><select {$area_option}></select>
		<script type=\"text/javascript\">addressInit('{$province_id}','{$city_id}','{$area_id}','{$province}','{$city}','{$area}');</script>";
		return $str;
	}
	
	/**
	 * ajax地区JS联动菜单
	 * @param int $province 默认省份ID
	 * @param int $city 默认城市ID
	 * @param int $area 默认地区ID
	 * @param int $province 省份select参数 name='data[province]' id='province'
	 * @param int $city 城市select参数 name='data[city]' id='city'
	 * @param int $area 地区select参数 name='data[area]' id='area'
     * @param int $field 地区option参数，如：name（地区全名称）| shortname（地区短名称）
	 */
	public static function ajaxarea($province = 0, $city = 0, $area = 0,$province_option = '', $city_option = '', $area_option = '', $field = 'name') {
		if(empty($province_option)) $province_option = " name='data[province]' id='province'";
		if(empty($city_option)) $city_option = " name='data[city]' id='city'";
		if(empty($area_option)) $area_option = " name='data[area]' id='area'";
		$str = '';
		
		//提取id参数
		preg_match_all('/id\s*=\s*([^ ]+)/is', $province_option, $match);
		if(empty($match[1][0])) return '';
		$province_id = str_replace(array("'",'"'), '', $match[1][0]);
		preg_match_all('/id\s*=\s*([^ ]+)/is', $city_option, $match);
		if(empty($match[1][0])) return '';
		$city_id = str_replace(array("'",'"'), '', $match[1][0]);
		preg_match_all('/id\s*=\s*([^ ]+)/is', $area_option, $match);
		if(empty($match[1][0])) return '';
		$area_id = str_replace(array("'",'"'), '', $match[1][0]);
		
		if (!defined('AREADATA2_INIT')) {
			define('AREADATA2_INIT', 1);
			$url = C('url');			
		//	$str .= '<script type="text/javascript" src="' . $url['img'] . '/s/v2/js/common/area2.js?_v=' . C('web_version') . '"></script>
			$str .= '<script type="text/javascript">
			function ajaxChangeArea(id, type, city, area){
				$.get("'.url('ajax/ajaxareaselect').'?id="+id+"&type="+type, function(data){
					var sdata  = eval(\'(\' + data + \')\');
					if(sdata.status == 1){
						$(\'#\'+city).html(sdata.data);
						if(area){
							$(\'#\'+area).html("<option value=\'\'>请选择地区</option>");
						}
					}	
				}); 
			}
			</script>';
		}
		
		//获取省份列表
		$rows = D('area')->getChildTree(0);
		$str .= "<select {$province_option} onchange='ajaxChangeArea(this.value,1,\"{$city_id}\",\"{$area_id}\")'><option value=''>请选择省份</option>";
		foreach($rows as $v){
			$selected = $province == $v['id'] ? ' selected="selected"' : '';
			$str .= "'<option value='{$v['id']}'{$selected}>{$v[$field]}</option>";
		}
		$str .= "</select>";
		
		//获取城市列表
		$str .= "<select {$city_option} onchange='ajaxChangeArea(this.value,2,\"{$area_id}\")'><option value=''>请选择城市</option>";
		if($province){
			$rows = D('area')->getChildTree($province);
			foreach($rows as $v){
				$selected = $city == $v['id'] ? ' selected="selected"' : '';
				$str .= "'<option value='{$v['id']}'{$selected}>{$v[$field]}</option>";
			}			
		}
		$str .= "</select>";
		
		//获取地区列表
		$str .= "<select {$area_option}><option value=''>请选择地区</option>";
		if($city){
			$rows = D('area')->getChildTree($city);
			foreach($rows as $v){
				$selected = $area == $v['id'] ? ' selected="selected"' : '';
				$str .= "'<option value='{$v['id']}'{$selected}>{$v[$field]}</option>";
			}			
		}
		$str .= "</select>";				
		return $str;
	}

	/**
	 * 日期时间控件
	 *
	 * @param $name 控件name，id
	 * @param $value 选中值
	 * @param $isdatetime 是否显示时间
	 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
	 * @param $showweek 是否显示周，使用，true | false
	 * @param $placeholder 提示文字内容
	 */
	public static function date($name, $value = '', $isdatetime = 0, $loadjs = 0, $showweek = 'true',$placeholder='') {
		if($value == '0000-00-00 00:00:00' || empty($value)) $value = '';
		$id = preg_match("/\[(.*)\]/", $name, $m) ? $m[1] : $name;
		if($isdatetime) {
			$size = 21;
			$format = '%Y-%m-%d %H:%M:%S';
			$showsTime = 24;
		} else {
			$size = 10;
			$format = '%Y-%m-%d';
			$showsTime = 'false';
		}
		$str = '';
		$url = C('url');
		if($loadjs || !defined('CALENDAR_INIT')) {
			define('CALENDAR_INIT', 1);
			$str .= '<link rel="stylesheet" type="text/css" href="'.$url['admin'].'/s/admin/js/calendar/jscal2.css"/>
			<link rel="stylesheet" type="text/css" href="'.$url['admin'].'/s/admin/js/calendar/win2k.css"/>
			<script type="text/javascript" src="'.$url['admin'].'/s/admin/js/calendar/calendar.js"></script>
			<script type="text/javascript" src="'.$url['admin'].'/s/admin/js/calendar/lang/cn.js"></script>';
		}
		$s_placeholder = "";
		if(!empty($placeholder))
		{
            $s_placeholder = 'placeholder="'.$placeholder.'"';
		}
		if(empty($value))
		{ 
			$str .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" class="input-text date"  '.$s_placeholder.'>&nbsp;';
		}
		else
		{
			$str .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" class="input-text date">&nbsp;';
		}
		
		$str .= '<script type="text/javascript">
			Calendar.setup({
			weekNumbers: '.$showweek.',
		    inputField : "'.$id.'",
		    trigger    : "'.$id.'",
		    dateFormat: "'.$format.'",
		    showTime: '.$showsTime.',
			minuteStep: 1,
		    onSelect   : function() {this.hide();}
			});
        </script>';
		return $str;
	}

	/**
	 * 得到select下拉框
	 * @param string $ids 选定项，可以为ID集合，例如：(1,2,3,4)
	 * @param array $array 下拉框来源数据 array( array('id'=>0, 'name'=>'xx'),array('id'=>1, 'name'=>'xx') )
	 * @param string $option 表单选项，<select>里面的参数，例如：(name="status" id="status")
	 * @param string $default_name 表单第一项默认值，例如：(== 全部 ==)
	 * @param string $rule 表单规则模版
	 */
	public static function select($id = 0, $array = array(), $option = '', $default_name = '', $rule='') {
		if($rule=='') $rule = "<option value='\$id'\$selected>\$name</option>";
		$id = (string)$id;
		$default_selected = ($id==='' && $default_name) ? ' selected' : '';
		$string = '<select '.$option.'>';
		if($default_name) $string .= "<option value=''$default_selected>$default_name</option>";
		$string .= self::formatTpl($id, $rule, $array);
		$string .= '</select>';
		return $string;
	}

	/**
	 * 得到checkbox多选框
	 * @param string $ids 选定项，可以为ID集合，例如：(1,2,3,4)
	 * @param array $array 来源数据 array( array('id'=>0, 'name'=>'xx'),array('id'=>1, 'name'=>'xx') )
	 * @param string $option 表单选项，<input>里面的参数，例如：(name="ids[]")
	 * @param string $rule 表单规则模版
	 * @param int $width input表单的label宽度
	 */
	public static function checkbox($id = 0, $array = array(), $option = '', $rule='', $width = 0) {
		$style = $width ? $style = ' style="width:'.$width.'px"' : '';
		$s1 = '<label class="ib"'.$style.'>';
		$s2 = '</label>';
		if($rule=='') $rule = "$s1<input type='checkbox' $option value='\$id'\$checked /> \$name &nbsp;&nbsp;$s2";
		$string = self::formatTpl($id, $rule, $array);
		return $string;
	}

	/**
	 * 得到radio单选框
	 * @param string $ids 选定项
	 * @param array $array 来源数据 array( array('id'=>0, 'name'=>'xx'),array('id'=>1, 'name'=>'xx') )
	 * @param string $option 表单选项，<input>里面的参数，例如：(name="ids[]")
	 * @param string $rule 表单规则模版
	 * @param int $width input表单的label宽度
	 */
	public static function radio($id = 0, $array = array(), $option = '', $rule='', $width = 0) {
		$style = $width ? $style = ' style="width:'.$width.'px"' : '';
		$s1 = '<label class="ib"'.$style.'>';
		$s2 = '</label>';
		if($rule=='') $rule = "$s1<input type='radio' $option value='\$id'\$checked /> \$name &nbsp;&nbsp;$s2";
		$string = self::formatTpl($id, $rule, $array);
		return $string;
	}

	//根据模版字符进行格式化后输出
	//$tpl  = "<li>\$name</li>\n";
	//$str = $this->formatTpl(2,$tpl);
	//$rows = array(0=>array('id'=>1,'name'=>'test'));
	public static function formatTpl($ids='', $str='', $rows=array()){
		if(empty($rows)) return '';
		$str = str_replace('"', '\"', $str);
		if(is_numeric($ids)) $ids = (string)$ids;
		if($ids!=='' && !is_array($ids)){
			$ids = explode(',',$ids);
		}
		$__tpl = '';
		foreach($rows as $id => $row){
			$selected = $checked = '';
			if($ids && in_array($id,$ids)){
				$selected = ' selected="selected"';
				$checked = ' checked="checked"';
			}
			@extract($row);
			eval("\$nstr = \"$str\";");
			$__tpl .= $nstr;
		}
		unset($nstr);
		return $__tpl;
	}
	
	
	/**
	 * ajax行业JS联动菜单
	 * @param int $province 默认父级行业ID
	 * @param int $city 默认子级行业分类ID
	 * @param int $province 父级行业select参数 name='data[trade_one]' id='trade_one'
	 * @param int $city 子级行业select参数 name='data[trade_two]' id='trade_two'
	 */
	public static function ajaxtrade($trade_one = 0, $trade_two = 0,$trade_one_option = '', $trade_two_option = '') {
		if(empty($trade_one_option)) $trade_one_option = " name='data[trade_one]' id='trade_one'";
		if(empty($trade_two_option)) $trade_two_option = " name='data[trade_two]' id='trade_two'";
//		if(empty($area_option)) $area_option = " name='data[area]' id='area'";
		$str = '';
		
		//提取id参数
		preg_match_all('/id\s*=\s*([^ ]+)/is', $trade_one_option, $match);
		if(empty($match[1][0])) return '';
		$trade_one_id = str_replace(array("'",'"'), '', $match[1][0]);
		preg_match_all('/id\s*=\s*([^ ]+)/is', $trade_two_option, $match);
		if(empty($match[1][0])) return '';
		$trade_two_id = str_replace(array("'",'"'), '', $match[1][0]);
// 		preg_match_all('/id\s*=\s*([^ ]+)/is', $area_option, $match);
// 		if(empty($match[1][0])) return '';
// 		$area_id = str_replace(array("'",'"'), '', $match[1][0]);
	
		if (!defined('AREADATA3_INIT')) {
			define('AREADATA3_INIT', 1);
			$url = C('url');
	//		$str .= '<script type="text/javascript" src="' . $url['img'] . '/s/v2/js/common/area2.js?_v=' . C('web_version') . '"></script>
	$str .= '		<script type="text/javascript">
			function ajaxChangeTrade(id, type, trade_two){
				$.get("'.url('ajax/ajaxtradeselect').'?id="+id+"&type="+type, function(data){
					var data  = eval(\'(\' + data + \')\');
					if(data.status == 1){
						$(\'#\'+trade_two).html(data.data);
					}
				});
			}
			</script>';
		}
	
		//获取父级行业列表
		$rows = D('trade')->getChildTree(0);
		$str .= "<select {$trade_one_option} onchange='ajaxChangeTrade(this.value,1,\"{$trade_two_id}\")'><option value=''>请选择行业</option>";
		foreach($rows as $v){
			$selected = $trade_one == $v['id'] ? ' selected="selected"' : '';
			$str .= "'<option value='{$v['id']}'{$selected}>{$v['name']}</option>";
		}
		$str .= "</select>";
	
		//获取子级行业列表
		$str .= "<select {$trade_two_option}><option value=''>请选择行业</option>";
		if($trade_one){
			$rows = D('trade')->getChildTree($trade_one);
			foreach($rows as $v){
				$selected = $trade_two == $v['id'] ? ' selected="selected"' : '';
				$str .= "'<option value='{$v['id']}'{$selected}>{$v['name']}</option>";
			}
		}
		$str .= "</select>";

		return $str;
	}
	

	/**
	 * ajax征信公司个人相关信息分类JS联动菜单
	 * @param int $category 默认父级ID
	 * @param int $category2 默认子级分类ID
	 * @param int $category 父级行业select参数 name='data[category]' id='category'
	 * @param int $category2 子级行业select参数 name='data[category]' id='category2'
	 */
	public static function ajaxcategory($category = 0, $category2 = 0,$category_option = '', $category2_option = '') {
		if(empty($category_option)) $category_option = " name='data[category]' id='category'";
		if(empty($category2_option)) $category2_option = " name='data[category2]' id='category2'";
		//		if(empty($area_option)) $area_option = " name='data[area]' id='area'";
		$str = '';
	
		//提取id参数
		preg_match_all('/id\s*=\s*([^ ]+)/is', $category_option, $match);
		if(empty($match[1][0])) return '';
		$category_id = str_replace(array("'",'"'), '', $match[1][0]);
		preg_match_all('/id\s*=\s*([^ ]+)/is', $category2_option, $match);
		if(empty($match[1][0])) return '';
		$category2_id = str_replace(array("'",'"'), '', $match[1][0]);
		// 		preg_match_all('/id\s*=\s*([^ ]+)/is', $area_option, $match);
		// 		if(empty($match[1][0])) return '';
		// 		$area_id = str_replace(array("'",'"'), '', $match[1][0]);
	
		if (!defined('AREADATA4_INIT')) {
			define('AREADATA4_INIT', 1);
			$url = C('url');
			//		$str .= '<script type="text/javascript" src="' . $url['img'] . '/s/v2/js/common/area2.js?_v=' . C('web_version') . '"></script>
			$str .= '		<script type="text/javascript">
			function ajaxChangeCategory(id, type, category2){
				$.get("'.url('ajax/ajaxcategoryselect').'?id="+id+"&type="+type, function(data){
					var data  = eval(\'(\' + data + \')\');
					if(data.status == 1){
						$(\'#\'+category2).html(data.data);
					}
				});
			}
			</script>';
		}
	
		//获取父级行业列表
		$rows = D('rrtcreditcompanyinfotype')->getChildTree(0);
		$str .= "<select {$category_option} onchange='ajaxChangeCategory(this.value,1,\"{$category2_id}\")'><option value=''>请选择分类</option>";
		foreach($rows as $v){
			$selected = $category == $v['id'] ? ' selected="selected"' : '';
			$str .= "'<option value='{$v['id']}'{$selected}>{$v['name']}</option>";
		}
		$str .= "</select>";
	
		//获取子级行业列表
		$str .= "<select {$category2_option}><option value=''>请选择分类</option>";
		if($category){
			$rows = D('rrtcreditcompanyinfotype')->getChildTree($category);
			foreach($rows as $v){
				if($v['is_show'])
				{
					$selected = $category2 == $v['id'] ? ' selected="selected"' : '';
					$str .= "'<option value='{$v['id']}'{$selected}>{$v['name']}</option>";
				}
			}
		}
		$str .= "</select>";
	
		return $str;
	}
	
	/**
	 * 优酷免广告视频生成地址
	 * @param string $vid 网址或优酷唯一ID
	 * @param string $width 宽度|默认480px
	 * @param string $height 高度|默认400px
	 * @param string $id ID编号，多个视频的时候使用|默认youkuplayer
	 * @param bool $isapp 是否移动端调用，默认 false
	 * @return array
	 * @author: liufei
	 */
	public static function youkuVideo($vid = '', $width = '', $height = '', $id = '', $isapp = false) {
		if (empty($vid)) return '';
		if (empty($width)) $width = '480px';
		if(is_numeric($width)) $width = $width.'px';
		if (empty($height)) $height = '400px';
		if(is_numeric($height)) $height = $height.'px';
		if (empty($id)) $id = 'youkuplayer';
				
		//提取youku ID
		$pre = strtolower(substr($vid,0,7));
		if($pre == '<iframe'){
			preg_match_all('|http://player.youku.com/embed/([^"]+)"|is', $vid, $match);
			$vid = empty($match[1][0]) ? '' : $match[1][0];
		}elseif($pre == 'http://'){
			preg_match_all('|v_show/id_([^\.]+)\.html|is', $vid, $match);
			$vid = empty($match[1][0]) ? '' : $match[1][0];
		}
		if (empty($vid)) return '';

		$conf = C('youkucloud');
		$js = $mob = '';
		if ($isapp) {
			//手机端调用加密参数
			$time = time();
			$sign = md5("{$vid}_{$time}_{$conf['client_secret']}");
			$mob = "embsig: '1_{$time}_{$sign}',";
		}
		if (!defined('INI_YOUKUVIDEO')) {
			define('INI_YOUKUVIDEO', true);
			$js = '<script type="text/javascript" src="http://player.youku.com/jsapi"></script>';
		}

		$str = "<div id=\"{$id}\" style=\"width:{$width};height:{$height}\"></div>
			{$js}<script type=\"text/javascript\">
			player_{$id} = new YKU.Player('{$id}',{
			styleid: '0',
			client_id: '{$conf['client_id']}',
			vid: '{$vid}',{$mob}
			show_related: false				
			});
			</script>";
        $data['param'] = array('client_id' => $conf['client_id'], 'vid'=>$vid, 'embsig'=>$mob);
        $data['video'] = $str;
		return $data;
	}

}