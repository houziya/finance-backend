<?php

/**
 * area地区模型
 */
class model_area extends model_abstruct {

	protected $tableName = 'area';
	protected $_tree;      //地区无限极分类
	protected $cachename = 'model_area_list'; //地区缓存名称
	
	//热门城市状态
	static public $is_hot_arr = array(
		// ID -- 名称 -- 显示样式
		0 => array('id' => 0, 'name' => '否', 'style' => ''),
		1 => array('id' => 1, 'name' => '是', 'style' => 'class="red"'),
	);
	
	//分站开通状态
	static public $is_open_arr = array(
		// ID -- 名称 -- 显示样式
		0 => array('id' => 0, 'name' => '未开通', 'style' => ''),
		1 => array('id' => 1, 'name' => '签约', 'style' => ''),
		2 => array('id' => 2, 'name' => '开通', 'style' => ''),
	);

	//得到地区下拉框
	public function getSelect($ids='',$option='', $default_name='', $rule=''){
		$lists = $this->getChildList(0);
		$rule = "<option value='\$id'\$selected>\$spacer\$name</option>";
		if(!$option) $option = 'name="data[pid]"';
		$str = helper_form::select($ids, $lists, $option, $default_name, $rule);
		return $str;
	}
    
    /**
     *  得到某分类下的地区列表，只显示一级
     *  2016-03-21 Jimmy Fu
     */ 
     public function getSelectLevel($pid,$ids='',$option='', $default_name='', $rule=''){
        $lists = $this->getChildList($pid);
        $areaList = array(); //不返回二级的
        foreach($lists as $val){
            if($val['pid'] == $pid){
                $areaList[] = $val;
            }
        }
		if(!$option) $option = 'name="data[pid]"';
		$str = helper_form::select($ids, $areaList, $option, $default_name, $rule);
		return $str;
        
     }
	
	//得到已审核地区下拉框
	public function getSelectStatus($ids='',$option='', $default_name='', $rule=''){
		$rows = $this->getChildList(0);
		$lists = array();
		foreach($rows as $k => $v){
			if($v['status'] == 0 || $v['level'] >= 3) continue;
			$v['is_open_tips'] = '';
			if($v['is_open'] == 1){
				$v['is_open_tips'] = '[签约中]';
			}elseif($v['is_open'] == 2){
				$v['is_open_tips'] = '[分站]';
			}			
			$lists[$k] = $v;
		}
		$rule = "<option value='\$id'\$selected>\$spacer\$name \$is_open_tips</option>";
		if(!$option) $option = 'name="data[pid]"';
		$str = helper_form::select($ids, $lists, $option, $default_name, $rule);
		return $str;
	}

	/**
	 * 删除地区 同时删除下面所有子地区
	 * @param int $id 地区ID
	 * @return return
	 */
	public function areaDelete($id){
		//此节点下的所有子节点
		$sun = $this->tree()->getChildList($id);
		$ids = array();
		$ids[] = $id;
		if ($sun) {
			foreach ($sun as $v) {
				$ids[] = $v['id'];
			}
		}
		$ids = implode(',', $ids);
		$result = $this->where("id in($ids)")->delete();
		$this->getList(true);
		return $result;
	}
	
	/*
	 * 获取ajax地区数据
	 * @param string $id 地区ID
	 * @param string $type 类型 1省份 2城市 3地区
	 * @return array
	 */
	public function getAjaxAreaSelect($id, $type){	
		$msg = $type == 1 ? '请选择城市' : '请选择地区';
		$str = "<option value=''>{$msg}</option>";
		if(empty($id) || $id < 1)
		{
			return array('status' => 1, 'data' => $str);
		}
		$rows = $this->getChildTree($id);
		if(!empty($rows)){
			foreach($rows as $v){
				$str .= "'<option value='{$v['id']}'>{$v['name']}</option>";
			}
		}
		return array('status' => 1, 'data' => $str);
	}

	// 得到所有未排序的节点
	public function getNodes(){
		return $this->tree()->nodes;
	}

	// 得到某个节点
	public function getNode($id){
		$nodes = $this->tree()->nodes;
		return isset($nodes[$id]) ? $nodes[$id] : array();
	}

	// 节点id转换成名称
	public function id2name($id){
		$nodes = $this->tree()->nodes;
		if (isset($nodes[$id]['name'])) {
			return $nodes[$id]['name'];
		}else{
			return '';
		}
	}

	/**
	 * 得到指定ID的上级所有列表
	 * 结果按级别顺序排序  1 => 2 => 3
	 * @param int $id 地区ID
	 * @param bool $own 是否包含自己
	 * @return array
	 */
	public function getParentList($id,$own=false) {
		$res = $this->tree()->getParentList($id);
		if($own){
			$node = $this->getNode($id);
			if($node) $res[$id] = $node;
		}
		return $res;
	}

	// 得到子列表
	public function getChildList($id){
		return $this->tree()->getChildList($id);
	}
	
	/**
	 * 得到子列表的id集合
	 * @param int $id 地区ID
	 * @param bool $own 返回类型  string：字符串(1,2,4)  array：数组array(1,2,4)
	 * @return array
	 */
	public function getChildIds($id, $type = 'string'){
		$arr = $this->tree()->getChildList($id);
		$ids = array();
		if($arr){			
			foreach($arr as $v){
				$ids[] = $v['id'];
			}
		}
		if($type == 'string'){
			$ids = $ids ? implode(',', $ids) : '';
		}
		return $ids ;
	}

	// 得到子树
	public function getChildTree($id){
		return $this->tree()->getChildTree($id);
	}

	public function tree(){
		if(empty($this->_tree)){
			$rows = $this->getList();
			$this->_tree = new helper_tree($rows,'id','pid');
			unset($rows);
		}
		return $this->_tree;
	}

	/**
	 * 得到列表缓存
	 * @param bool $delcache 是否删除缓存
	 * @return return
	 */
	public function getList($delcache=false) {
		$rows = S($this->cachename);
		if(empty($rows) || $delcache){
			$arr = $this->order("pid asc,`sort` asc,id asc")->findAll();
			if($arr) {
				$rows = array();
				foreach($arr as $v){
					$rows[$v['id']] = $v;
				}
				S($this->cachename, $rows, 3600 * 24 * 7);
				if($delcache) S($this->cachename.'domain',null);
			}
		}
		return $rows;
	}
	
	/*
	 * 得到开开通分站有二级域名的地区列表
	 * @author liufei
	 * @param bool $delcache 是否删除缓存
	 * @return array
	 */
	public function getDomainList($delcache=false){
		$rows = S($this->cachename.'domain');
		if (empty($rows) || $delcache) {
			if($delcache) $this->getList($delcache);
			$arr = $this->getChildList(0);
			$rows = array();
			foreach ($arr as $v) {
				if ($v['is_open'] == 2 && !empty($v['domain'])) {
					$rows[$v['domain']] = $v;
				}
			}
			S($this->cachename.'domain', $rows, 3600 * 24 * 7);
		}
		return $rows;
	}
	
	/*
	 * 域名转地区信息
	 * @author liufei
	 * @param string $domain 二级域名
	 * @return return
	 */
	public function domain2area($domain = ''){
		if(empty($domain)) return array();
		$rows = $this->getDomainList();
		return empty($rows[$domain]) ? array() : $rows[$domain];
	}

	/*
	 * 得到地区JS数据
	 * @param bool $default 增加 请选择区域 默认框
	 * @return return
	 */
	public function getJsData($default = false){
		$rows = $this->getChildTree(0);
		$rows2 = array('id'=>'','name'=>'请选择省份','child'=>array(array('id'=>'','name'=>'请选择城市','child'=>array(array('id'=>'','name'=>'请选择区域')))));
		array_unshift($rows,$rows2);

		$lists = array();
		foreach($rows as $v1){
			$arr = array();
			$arr['name'] = urlencode($v1['name'])."|".$v1['id'];
			$arr2 = array();
			$_tmp = array(array('id'=>'','name'=>'请选择城市','child'=>array(array('id'=>'','name'=>'请选择区域'))));
			if($default) $v1['child'] = array_merge($_tmp, $v1['child']);
			foreach($v1['child'] as $v2){
				$arr2['name'] = urlencode($v2['name'])."|".$v2['id'];
				if(!empty($v2['child'])){
					$arr3 = array();
					$i3 = 0;
					$_tmp = array(array('id'=>'','name'=>'请选择区域'));
					if($default) $v2['child'] = array_merge($_tmp, $v2['child']);
					foreach($v2['child'] as $v3){
						$arr3[] = urlencode($v3['name'])."|".$v3['id'];
					}
					$arr2['areaList'] = $arr3;
				}
				$arr['cityList'][] = $arr2;
			}
			$lists[] = $arr;
		}
		$data = "var provinceList = ".urldecode(json_encode($lists)).";";
		return $data;
	}

	
    /**
     * 根据条件获取地区列表
     * @param string/array $condition 条件集合
     * @param string/array $fields 查询的字段
     * @param string $sort 排序
     * @return array
     * @author quanzhijie
     */
    public function getAreas($condition, $fields = 'id, name, shortname, pid', $sort = 'sort DESC, is_hot DESC, id ASC')
    {
        $areas = M($this->tableName)->field($fields)->where($condition)->order($sort)->select();
        return $areas ? $areas : array();
    }
	
    /**
	 * 根据条件获取地区信息
	 * @param string/array $condition 条件集合
	 * @param string/array $fields 查询的字段
	 * @return array
	 * @author Baijiansheng
	 */
	public function getArea($condition, $fields = 'id, name, pid') {
		$areas = M($this->tableName)->field($fields)->where($condition)->find();
		return $areas ? $areas : array();
	}
	
	// 根据IP地址得到省份城市ID
	public static function ip2area($ip=''){
		if(!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i', $ip)){
			return array();
		}
		$num = helper_string::ip2num($ip);
		$info = M('ip_address')->where("num_begin <= '{$num}' AND num_end >= '{$num}'")->field("address,province,city")->order("id asc")->find();
		return $info ? $info : array();
	}
	
	// 根据IP地址得到省份和城市信息
	public function ip2area2($ip=''){
		if(empty($ip)) return '';
		$result = array();
		$location = $this->convertip($ip);
		if ($location) {
			//查找省份
			$area = $this->getChildTree(0);
			foreach ($area as $row){
				if ( strstr($location,$row['name']) ) {
					// 找到省份
					$result['province'] = $row['id'];
					$result['province_name'] = $row['name'];
					// 找到城市
					$city = $this->getChildList($row['id']);
					foreach ($city as $val){
						if ( strstr($location,$val['name']) ) {
							$result['city'] = $val['id'];
							$result['city_name'] = $val['name'];
							break 2;
						}
					}
				}
			}
		}
		return $result;
	}

	// IP转换成地址
	public function convertip($ip) {
		
		//IP数据文件路径
		$dat_path = FEE_PATH.'/data/ipdata.dat';

		//检查IP地址
		if(!ereg("^([0-9]{1,3}.){3}[0-9]{1,3}$", $ip)){
			//return 'IP Address Error';
			return '';
		}

		//打开IP数据文件
		if(!$fd = @fopen($dat_path, 'rb')){
			//return 'IP date file not exists or access denied';
			return '';
		}

		//分解IP进行运算，得出整形数
		$ip = explode('.', $ip);
		$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

		//获取IP数据索引开始和结束位置
		$DataBegin = fread($fd, 4);
		$DataEnd = fread($fd, 4);
		$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;

		$BeginNum = 0;
		$EndNum = $ipAllNum;

		//使用二分查找法从索引记录中搜索匹配的IP记录
		while($ip1num>$ipNum || $ip2num<$ipNum) {
			$Middle= intval(($EndNum + $BeginNum) / 2);

			//偏移指针到索引位置读取4个字节
			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) {
				fclose($fd);
				return 'System Error';
			}
			//提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);

			//提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
			if($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}

			//取完上一个索引后取下一个索引
			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) {
				fclose($fd);
				//return 'System Error';
				return '';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) {
				fclose($fd);
				//return 'System Error';
				return '';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);

			//没找到提示未知
			if($ip2num < $ipNum) {
				if($Middle == $BeginNum) {
					fclose($fd);
					//return 'Unknown';
					return '';
				}
				$BeginNum = $Middle;
			}
		}

		//下面的代码读晕了，没读明白，有兴趣的慢慢读
		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) {
				fclose($fd);
				//return 'System Error';
				return '';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}

		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					//return 'System Error';
					return '';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}

			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr2 .= $char;

			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);

			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;
		} else {
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;

			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					//return 'System Error';
					return '';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0)){
				$ipAddr2 .= $char;
			}
		}
		fclose($fd);

		//最后做相应的替换操作后返回结果
		if(preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		$ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
		$ipaddr = preg_replace('/^s*/is', '', $ipaddr);
		$ipaddr = preg_replace('/s*$/is', '', $ipaddr);
		if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
			//$ipaddr = 'Unknown';
			$ipaddr = '';
		}
		$ipaddr = trim($ipaddr);
		if (C('sys_default_charset') == 'utf-8' && $ipaddr) {
			$ipaddr = helper_string::autoCharset($ipaddr,'gbk','utf-8');
		}
		return $ipaddr;
	}
	
	/*
	 * 旧版省市区转新版省市区ID
	 * @author liufei
	 * @param string $type 类型  province|city|area
	 * @param int $id 旧版地区ID
	 * @return return
	 */
	static public function oldid2id($type = '', $id = 0){
		if(empty($type) || empty($id)) return 0;
		
		static $_oldarea;		
		if (empty($_oldarea)) {
			//省市区替换
			$rows = D('area')->field("id,pid,level,province,city,area")->findAll();
			$_oldarea = array();
			foreach ($rows as $v) {
				if ($v['level'] == 1 && $v['province'] == 0) {
					continue;
				}
				if ($v['level'] == 2 && $v['city'] == 0) {
					continue;
				}
				if ($v['level'] == 3 && $v['area'] == 0) {
					continue;
				}
				if ($v['level'] == 1) {
					$_oldarea['province'][$v['province']] = $v['id'];
				} elseif ($v['level'] == 2) {
					$_oldarea['city'][$v['city']] = $v['id'];
				} else {
					$_oldarea['area'][$v['area']] = $v['id'];
				}
			}
		}
		return empty($_oldarea[$type][$id]) ? 0 : $_oldarea[$type][$id];
	}
}
