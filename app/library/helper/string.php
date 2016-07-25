<?php

// +----------------------------------------------------------------------
// | 字符处理
// +----------------------------------------------------------------------

class helper_string {

	/**
	 * 生成UUID 单机使用
	 * @return string
	 */
	static public function uuid() {
		$charid = md5(uniqid(mt_rand(), true));
		$hyphen = chr(45); // "-"
		$uuid = chr(123)// "{"
				. substr($charid, 0, 8) . $hyphen
				. substr($charid, 8, 4) . $hyphen
				. substr($charid, 12, 4) . $hyphen
				. substr($charid, 16, 4) . $hyphen
				. substr($charid, 20, 12)
				. chr(125); // "}"
		return $uuid;
	}

	/**
	 * 生成Guid主键
	 * @return Boolean
	 */
	static public function keyGen() {
		return str_replace('-', '', substr(com_create_guid(), 1, -1));
	}

	/**
	 * 检查字符串是否是UTF8编码
	 * @param string $string 字符串
	 * @return Boolean
	 */
	static public function isUtf8($str) {
		$c = 0;
		$b = 0;
		$bits = 0;
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++) {
			$c = ord($str[$i]);
			if ($c > 128) {
				if (($c >= 254))
					return false;
				elseif ($c >= 252)
					$bits = 6;
				elseif ($c >= 248)
					$bits = 5;
				elseif ($c >= 240)
					$bits = 4;
				elseif ($c >= 224)
					$bits = 3;
				elseif ($c >= 192)
					$bits = 2;
				else
					return false;
				if (($i + $bits) > $len)
					return false;
				while ($bits > 1) {
					$i++;
					$b = ord($str[$i]);
					if ($b < 128 || $b > 191)
						return false;
					$bits--;
				}
			}
		}
		return true;
	}

	/**
	 * 字符串截取，支持中文和其他编码
	 * @param string $str 需要转换的字符串
	 * @param string $start 开始位置
	 * @param string $length 截取长度
	 * @param string $charset 编码格式
	 * @param string $suffix 截断显示字符
	 * @return string
	 */
	static public function msubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = '…') {
		if (function_exists("mb_substr")){
	        $slice =  mb_substr($str, $start, $length, $charset);
            if(( $slice != $str )){
                 $slice .= $suffix;
            }
            return $slice;
		}   
		elseif (function_exists('iconv_substr')) {
			$slice =  iconv_substr($str, $start, $length, $charset);
            if($suffix && $slice != $str){
                 $slice .= $suffix;
            }
            return $slice;
		}
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
		if ($suffix)
			return $slice . $suffix;
		return $slice;
	}

	/**
	 * 中文字符串截取，支持中文和其他编码
	 * @param string $str 需要转换的字符串
	 * @param string $slen 截取长度
	 * @param string $startdd 从第几个开始截取
	 * @param string $suffix 截断显示字符
	 * @return string
	 */
	static public function cnsubstr($str, $slen, $startdd = 0, $suffix = false) {
		$from = C('sys_default_charset');
		if ($from == 'utf-8')
			$str = self::autoCharset($str, 'utf-8', 'gbk');
		$restr = "";
		$c = "";
		$str_len = strlen($str);
		if ($str_len < $startdd + 1)
			return "";
		if ($str_len < $startdd + $slen || $slen == 0)
			$slen = $str_len - $startdd;
		$enddd = $startdd + $slen - 1;
		for ($i = 0; $i < $str_len; $i++) {
			if ($startdd == 0)
				$restr .= $c;
			else if ($i > $startdd)
				$restr .= $c;
			if (ord($str[$i]) > 127) {
				if ($str_len > $i + 1)
					$c = $str[$i] . $str[$i + 1];
				$i++;
			}
			else {
				$c = $str[$i];
			}
			if ($i >= $enddd) {
				if (strlen($restr) + strlen($c) > $slen)
					break;
				else {
					$restr .= $c;
					break;
				}
			}
		}
		if ($from == 'utf-8')
			$restr = self::autoCharset($restr, 'gbk', 'utf-8');
		if ($suffix)
			return $restr . "…";
		return $restr;
	}

	/**
	 * 自动转换字符集 支持数组转换
	 * @param string $fContents 需要转换的字符串
	 * @param string $from 原字符编码
	 * @param string $to 转后字符编码
	 * @return string
	 */
	static public function autoCharset($fContents, $from, $to) {
		$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
		$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
		if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
			//如果编码相同或者非字符串标量则不转换
			return $fContents;
		}
		if (is_string($fContents)) {
			if (function_exists('mb_convert_encoding')) {
				return mb_convert_encoding($fContents, $to, $from);
			} elseif (function_exists('iconv')) {
				return iconv($from, $to, $fContents);
			} else {
				return $fContents;
			}
		} elseif (is_array($fContents)) {
			foreach ($fContents as $key => $val) {
				$_key = self::autoCharset($key, $from, $to);
				$fContents[$_key] = self::autoCharset($val, $from, $to);
				if ($key != $_key)
					unset($fContents[$key]);
			}
			return $fContents;
		}
		else {
			return $fContents;
		}
	}

	/**
	 * 数组转xml
	 * @param array $data 数组
	 * @return string
	 */
	static public function data2xml($data) {
		$xml = '';
		foreach ($data as $key => $val) {
			is_numeric($key) && $key = "item id=\"$key\"";
			$xml.="<$key>";
			$xml.= ( is_array($val) || is_object($val)) ? self::data2xml($val) : $val;
			list($key, ) = explode(' ', $key);
			$xml.="</$key>";
		}
		return $xml;
	}

	/**
	 * xml编码
	 * @param array $data 数组
	 * @return string
	 */
	static public function xmlEncode($data, $encoding = 'utf-8', $root = 'feephp') {
		$xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
		$xml.= '<' . $root . '>';
		$xml.= self::data2xml($data);
		$xml.= '</' . $root . '>';
		return $xml;
	}

	/**
	 * 字符转换成JS格式
	 * @param string $string 待转换字符
	 * @param int $isjs 是否输出js格式
	 * @return string
	 */
	static public function str2js($string, $isjs = 1) {
		$string = addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
		return $isjs ? 'document.write("' . $string . '");' : $string;
	}

	/**
	 * 将字符串转换为数组
	 * @param	string	$data	字符串
	 * @return	array	返回数组格式，如果，data为空，则返回空数组
	 */
	static public function string2array($data) {
		if ($data == '')
			return array();
		eval("\$array = $data;");
		return $array;
	}

	/**
	 * 将数组转换为字符串
	 * @param	array	$data		数组
	 * @param	bool	$isformdata	如果为0，则不使用stripslashesDeep处理，可选参数，默认为1
	 */
	static public function array2string($data, $isformdata = 1) {
		if ($data == '')
			return '';
		if ($isformdata)
			$data = stripslashesDeep($data);
		return var_export($data, TRUE);
	}

	/**
	 * 产生随机字串，可用来自动生成密码，默认长度6位 字母和数字混合 支持中文
	 * @param string $len 长度
	 * @param string $type 字串类型 0字母数字 1数字 2小写字母 3大写字母 4字母 5中文
	 * @param string $addChars 额外字符
	 * @return string
	 */
	static public function randString($len = 6, $type = '', $addChars = '') {
		$str = '';
		switch ($type) {
			case 1:
				$chars = str_repeat('0123456789', 3);
				break;
			case 2:
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			case 3:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
				break;
			case 4:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			case 5:
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
				break;
		}
		if ($len > 10) {//位数过长重复字符串一定次数
			$chars = ($type == 2 || $type == 3) ? str_repeat($chars, $len) : str_repeat($chars, 5);
		}
		if ($type != 5) {
			$chars = str_shuffle($chars);
			$str = substr($chars, 0, $len);
		} else {
			// 中文随机字
			for ($i = 0; $i < $len; $i++) {
				$str.= self::msubstr($chars, 1, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)));
			}
		}
		return $str;
	}

	/**
	 * 生成一定数量的随机数，并且不重复
	 * @param integer $number 数量
	 * @param string $len 长度
	 * @param string $type 字串类型 1 数字 2小写字母 3大写字母 4大小写字母 5中文 6字母数字混合
	 * @return array
	 */
	static public function buildCountRand($number, $length = 4, $mode = 1) {
		if ($mode == 1 && $length < strlen($number)) {
			//不足以生成一定数量的不重复数字
			return false;
		}
		$rand = array();
		for ($i = 0; $i < $number; $i++) {
			$rand[] = self::randString($length, $mode);
		}
		$unqiue = array_unique($rand);
		if (count($unqiue) == count($rand)) {
			return $rand;
		}
		$count = count($rand) - count($unqiue);
		for ($i = 0; $i < $count * 3; $i++) {
			$rand[] = self::randString($length, $mode);
		}
		$rand = array_slice(array_unique($rand), 0, $number);
		return $rand;
	}

	/**
	 *  带格式生成随机字符，支持批量生成，但可能存在重复
	 * @param string $format 字符格式 # 表示数字 * 表示字母和数字 $ 表示字母
	 * @param integer $number 生成数量
	 * @return mixed
	 */
	static public function buildFormatRand($format, $number = 1) {
		$str = array();
		$length = strlen($format);
		for ($j = 0; $j < $number; $j++) {
			$strtemp = '';
			for ($i = 0; $i < $length; $i++) {
				$char = substr($format, $i, 1);
				switch ($char) {
					case "*"://字母和数字混合
						$strtemp .= self::randString(1);
						break;
					case "#"://数字
						$strtemp .= self::randString(1, 1);
						break;
					case "$"://大写字母
						$strtemp .= self::randString(1, 2);
						break;
					default://其他格式均不转换
						$strtemp .= $char;
						break;
				}
			}
			$str[] = $strtemp;
		}

		return $number == 1 ? $strtemp : $str;
	}

	/**
	 * 获取一定范围内的随机数字 位数不足补零
	 * @param integer $min 最小值
	 * @param integer $max 最大值
	 * @return string
	 */
	static public function randNumber($min, $max) {
		return sprintf("%0" . strlen($max) . "d", mt_rand($min, $max));
	}

	/**
	 * 转换文字中的超链接为可点击连接
	 * @param string $text 要处理的字符串
	 * @return string
	 */
	function makeLink($string) {
		$validChars = "a-z0-9\/\-_+=.~!%@?#&;:$\|";
		$patterns = array(
			"/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([{$validChars}]+)/ei",
			"/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([{$validChars}]+)/ei",
			"/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([{$validChars}]+)/ei",
			"/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([{$validChars}]+)/ei");
		$replacements = array(
			"'\\1<a href=\"\\2://\\3\" title=\"\\2://\\3\" rel=\"external\">\\2://'.helper_input::truncate( '\\3' ).'</a>'",
			"'\\1<a href=\"http://www.\\2.\\3\" title=\"www.\\2.\\3\" rel=\"external\">'.helper_input::truncate( 'www.\\2.\\3' ).'</a>'",
			"'\\1<a href=\"ftp://ftp.\\2.\\3\" title=\"ftp.\\2.\\3\" rel=\"external\">'.helper_input::truncate( 'ftp.\\2.\\3' ).'</a>'",
			"'\\1<a href=\"mailto:\\2@\\3\" title=\"\\2@\\3\">'.helper_input::truncate( '\\2@\\3' ).'</a>'");
		return preg_replace($patterns, $replacements, $string);
	}
	
	/**
	 * 把换行转换为<br />标签
	 * @param string $text 要处理的字符串
	 * @return string
	 */
	static public function nl2Br($string) {
		return preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $string);
	}
	
	/**
	 * 输出安全的html，用于过滤危险代码
	 * @param string $text 要处理的字符串
	 * @param mixed $tags 允许的标签列表，如 table|td|th|td
	 * @return string
	 */
	static public function safeHtml($text, $tags = null) {
		$text = trim($text);
		//完全过滤注释
		$text = preg_replace('/<!--?.*-->/', '', $text);
		//完全过滤动态代码
		$text = preg_replace('/<\?|\?' . '>/', '', $text);
		//完全过滤js
		$text = preg_replace('/<script?.*\/script>/', '', $text);

		$text = str_replace('[', '&#091;', $text);
		$text = str_replace(']', '&#093;', $text);
		$text = str_replace('|', '&#124;', $text);
		//过滤换行符
		$text = preg_replace('/\r?\n/', '', $text);
		//br
		$text = preg_replace('/<br(\s\/)?' . '>/i', '[br]', $text);
		$text = preg_replace('/(\[br\]\s*){10,}/i', '[br]', $text);
		//过滤危险的属性，如：过滤on事件lang js
		while (preg_match('/(<[^><]+)(lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i', $text, $mat)) {
			$text = str_replace($mat[0], $mat[1], $text);
		}
		while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
			$text = str_replace($mat[0], $mat[1] . $mat[3], $text);
		}
		
		if($tags == null){
			$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
		}

		//允许的HTML标签
		$text = preg_replace('/<(' . $tags . ')( [^><\[\]]*)>/i', '[\1\2]', $text);
		
		//过滤多余html
		$text = preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);

		//过滤合法的html标签
		while (preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i', $text, $mat)) {
			$text = str_replace($mat[0], str_replace('>', ']', str_replace('<', '[', $mat[0])), $text);
		}
		//转换引号
		while (preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i', $text, $mat)) {
			$text = str_replace($mat[0], $mat[1] . '|' . $mat[3] . '|' . $mat[4], $text);
		}
		//空属性转换
		$text = str_replace('\'\'', '||', $text);
		$text = str_replace('""', '||', $text);
		//过滤错误的单个引号
		while (preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i', $text, $mat)) {
			$text = str_replace($mat[0], str_replace($mat[1], '', $mat[0]), $text);
		}
		//转换其它所有不合法的 < >
		$text = str_replace('<', '&lt;', $text);
		$text = str_replace('>', '&gt;', $text);
		$text = str_replace('"', '&quot;', $text);
		//反转换
		$text = str_replace('[', '<', $text);
		$text = str_replace(']', '>', $text);
		$text = str_replace('|', '"', $text);
		//过滤多余空格
		$text = str_replace('  ', ' ', $text);
		return $text;
	}
	
	/**
	 * 删除html标签，得到纯文本。可以处理嵌套的标签
	 * @param string $text 要处理的html
	 * @return string
	 */
	static public function html2text($string) {
		while (strstr($string, '>')) {
			$currentBeg = strpos($string, '<');
			$currentEnd = strpos($string, '>');
			$tmpStringBeg = @substr($string, 0, $currentBeg);
			$tmpStringEnd = @substr($string, $currentEnd + 1, strlen($string));
			$string = $tmpStringBeg . $tmpStringEnd;
		}
		return $string;
	}

	/**
	 * 文本域转html
	 * @param string $string 要处理的html
	 * @return string
	 */
	static public function textarea2html($string) {
		$string = nl2br(str_replace(' ', '&nbsp;', $string));
		$string = str_replace(array("\r", "\n"), '<br />', $string);
		return $string;
	}
	
	/**
	 * hmtl转文本域
	 * @param string $string 要处理的html
	 * @return string
	 */
	static public function html2textarea($string) {
		$string = str_replace('&nbsp;', ' ', $string);
		$string = str_replace(array('<br />', '<br>'), "\n", $string);
		return $string;
	}
	
	/**
	 * 返回经htmlspecialchars处理过的字符串或数组
	 * @param string $string 要处理的html
	 * @return string
	 */
	function htmlspecialcharsDeep($string) {
		if (!is_array($string))
			return htmlspecialchars($string);
		foreach ($string as $key => $val)
			$string[$key] = self::htmlspecialcharsDeep($val);
		return $string;
	}
	
	/**
	 * 安全过滤函数
	 * @param string $string 要处理的字符
	 * @return string
	 */
	static public function safeReplace($string) {
		$string = str_replace('%20', '', $string);
		$string = str_replace('%27', '', $string);
		$string = str_replace('%2527', '', $string);
		$string = str_replace('*', '', $string);
		$string = str_replace('"', '&quot;', $string);
		$string = str_replace("'", '', $string);
		$string = str_replace('"', '', $string);
		$string = str_replace(';', '', $string);
		$string = str_replace('<', '&lt;', $string);
		$string = str_replace('>', '&gt;', $string);
		$string = str_replace("{", '', $string);
		$string = str_replace('}', '', $string);
		return $string;
	}
	
	/**
	 * GET参数sql注入过滤
	 * @param string $string 要处理的字符
	 * @param bool $isreplace 替换关键字
	 * @param bool $islike 替换like关键字
	 * @return string
	 */
	static public function sqlReplace($string, $isreplace = true, $islike = false) {
		if($isreplace){
//			$string = str_replace("and", "&#97;nd", $string);
//			$string = str_replace("execute", "&#101;xecute", $string);
//			$string = str_replace("update", "&#117;pdate", $string);
//			$string = str_replace("count", "&#99;ount", $string);
			$string = str_replace("chr(", "&#99;hr(", $string);
//			$string = preg_replace("/chr[ ]*\(/i", "&#99;hr(", $string);
//			$string = str_replace("mid", "&#109;id", $string);
//			$string = str_replace("master", "&#109;aster", $string);
//			$string = str_replace("truncate", "&#116;runcate", $string);
//			$string = str_replace("char", "&#99;har", $string);
//			$string = str_replace("declare", "&#100;eclare", $string);
//			$string = str_replace("select", "&#115;elect", $string);
//			$string = str_replace("create", "&#99;reate", $string);
//			$string = str_replace("delete", "&#100;elete", $string);
//			$string = str_replace("insert", "&#105;nsert", $string);
//			$string = str_replace("union", "&#117;nion", $string);
			$string = str_replace("load_file", "&#108;oad_file", $string);
			$string = str_replace("outfile", "&#111;utfile", $string);
//			$string = str_replace("=", "", $string);
			$string = str_replace("'", '', $string);
//			$string = str_replace('"', "", $string);
		}
		$string = mysql_escape_string($string);
		if($islike){
			$string = str_replace(array('%', '_'), array('\%', '\_'), $string);
		}
		return $string;
	}

	/**
	 * 过滤ASCII码从0-28的控制字符
	 * @param string $str 要处理的字符
	 * @return string
	 */
	static public function trimUnsafeControlChars($str) {
		$rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
		return str_replace(chr(0), '', preg_replace($rule, '', $str));
	}
	
	/**
	 * 是hsc()方法的逆操作
	 * @param string $text 要处理的字符串
	 * @return string
	 */
	static function unHsc($text) {
		return preg_replace(array("/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i", '/&amp;nbsp;/i'), array(">", "<", "\"", "'", "&nbsp;"), $text);
	}
	
	/**
	 * 过滤xss攻击
	 * @param string $string 要处理的字符串
	 * @return string
	 */
	static public function removeXss($string) {
		$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
        $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $parm = array_merge($parm1, $parm2);
        for ($i = 0; $i < sizeof($parm); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($parm[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $parm[$i][$j];
            }
            $pattern .= '/i';
            $string = preg_replace($pattern, ' ', $string);
        }
        return $string;
	}
	
	/**
	 * 处理文本中的换行
	 * @param string $string 要处理的字符串
	 * @param mixed $br 对换行的处理，false：去除换行；true：保留原样；string：替换成string
	 * @return string
	 */
	static public function nl2($string, $br = '<br />') {
		if ($br == false) {
			$string = preg_replace("/(\015\012)|(\015)|(\012)/", '', $string);
		} elseif ($br != true) {
			$string = preg_replace("/(\015\012)|(\015)|(\012)/", $br, $string);
		}
		return $string;
	}
	
	/*
	 * ip转数字
	 * @author liufei
	 * @param string $ip IP
	 * @return int
	 */
	static public function ip2num($ip = ''){
		if(empty($ip)) return 0;
		return bindec(decbin(ip2long($ip)));
	}
	
	/*
	 * 数字转IP
	 * @author liufei
	 * @param string $ip IP
	 * @return int
	 */
	static public function num2ip($num = ''){
		if(empty($num)) return '';
		return long2ip($num);
	}
	
	
}

?>