<?php

// +----------------------------------------------------------------------
// | 文件上传类
// +----------------------------------------------------------------------

class helper_uploadfile {

	// 上传文件的最大值
	public $maxsize = 2048; // -1不限制大小(KB) 默认为2M
	// 允许上传的文件后缀 留空不作后缀检查
	public $exts = array('jpg','jpeg','gif','png','bmp','doc','docx','xls','xlsx','ppt','pptx','pdf','txt','rar','zip','swf');
	// 使用对上传图片进行缩略图处理
	public $thumb = false;
	// 缩略图最大宽度
	public $thumbwidth = 300;
	// 缩略图最大高度
	public $thumbheight = 300;
	// 缩略图裁剪模式 0自动等比例裁剪 1自动按原图中心裁剪 array()手工裁剪
	public $thumbcut = 1;
	// 上传文件保存路径
	public $savepath = '';
	// 附件本地基础目录
	public $basePath = '';
	
	// 上传文件命名规则，例如可以是 time uniqid com_create_guid 等
	// array情况下为自定义函数和内置的方法，array('方法|函数','参数')
	// 字符情况下为文件名
	public $saverule = '';
	
	// 错误信息
	private $error = '';
	// 上传成功的文件信息
	private $fileinfo;
	
	public $private_file = false; //是否私密文件  false公共文件  true私密文件

	/*
	 * 构造函数
	 * @param string $dir 目录
	 * @param string $base_path 基本路径
	 * @return bool
	 */
	public function __construct($dir = '', $basePath = ''){
		if(empty($basePath)) $basePath = ROOT_PATH;
		$this->setDir($dir, $basePath);
	}
	
	/**
	 * 上传文件处理
	 * @param string $field  上传表单
	 */
	public function upload($field = '') {
		if(empty($field)) $field = 'attach';
		$exts = $this->exts;
		if (is_array($exts)) {
			$this->exts = array_map('strtolower', $exts);
		} else {
			$this->exts = explode('|', strtolower($exts));
		}
		
		// 获取上传的文件信息   对$_FILES数组信息处理
		if(empty($_FILES[$field]) || is_array($_FILES[$field]['name']) || empty($_FILES[$field]['name'])){
			$this->error = '上传错误';
			return false;
		}
		$file = $_FILES[$field];
		$file['extension'] = fileext($file['name']);
		if($file['extension'] == 'jpeg') $file['extension'] = 'jpg';
		$file['basepath'] = $this->basePath;
		$file['savepath'] = $this->savepath;
		$file['savename'] = $this->getSaveName($file);
		if(strpos($file['savename'], '/')){
			$file['savepath'] = $file['savepath'] . '/' . trim(substr($file['savename'],0,strrpos($file['savename'], '/')),'/');
			$file['savename'] = substr($file['savename'],strrpos($file['savename'], '/') + 1);
		}

		//上传文件绝对地址
		$file['file'] = $file['basepath'] . $file['savepath'] . '/' . $file['savename'];
		$file['url'] = $file['savepath'] . '/' . $file['savename'];

		// 检查附件合法性
		if (!$this->check($file)) return false;

		//保存上传文件
		if (!$this->save($file)) return false;
		
		$file['hash'] = md5($file['url']);

		//上传成功后保存文件信息，供其他地方调用
		unset($file['tmp_name'], $file['error']);
		
		$this->fileinfo = $file;
		return $file;
	}
	
	/**
	 * 上传一个文件
	 * @param mixed $name 数据
	 * @param string $value  数据表名
	 */
	private function save($file) {
		$filename = $file['file'];
		// 如果是图像文件 检测文件格式
		if (in_array(strtolower($file['extension']), array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf')) && false === getimagesize($file['tmp_name'])) {
			$this->error = '非法图像文件';
			return false;
		}
		if (!is_dir(dirname($filename))) mk_dir(dirname($filename));
		if (!move_uploaded_file($file['tmp_name'], $filename)) {
			$this->error = '文件上传保存错误！';
			return false;
		}

		if ($this->thumb && in_array(strtolower($file['extension']), array('gif', 'jpg', 'jpeg', 'bmp', 'png'))) {
			$image = getimagesize($filename);
			if (false !== $image) {
				// 生成图像缩略图
				helper_image::thumb($filename, $filename, $this->thumbwidth, $this->thumbheight, $this->thumbcut);
			}			
		}
		return $file;
	}
	
	/*
	 * 设置公开附件路径
	 * @param string $dir 目录
	 * @param string $base_path 基本路径
	 * @return bool
	 */
	public function setDir($dir = '', $basePath = ROOT_PATH){
		$path = $this->private_file ? '/s/upload2/' : '/s/upload/';		
		$this->savepath = $dir ? $path.trim(str_replace('\\', '/', $dir),'/') : rtrim($path,'/');
		if($basePath) $this->basePath = rtrim(str_replace('\\', '/', $basePath),'/');
	}
	
	/**
	 * 获取错误代码信息
	 * @param string $errorNo  错误号码
	 */
	protected function error($errorNo) {
		switch ($errorNo) {
			case 1:
				$this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
				break;
			case 2:
				$this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
				break;
			case 3:
				$this->error = '文件只有部分被上传';
				break;
			case 4:
				$this->error = '没有文件被上传';
				break;
			case 6:
				$this->error = '找不到临时文件夹';
				break;
			case 7:
				$this->error = '文件写入失败';
				break;
			default:
				$this->error = '未知上传错误！';
		}
		return;
	}

	/**
	 * 根据上传文件命名规则取得保存文件名
	 * @param string $filename 数据
	 */
	private function getSaveName($filename) {
		$saverule = $this->saverule;
		if (empty($saverule)) {
			//没有定义命名规则，使用默认命名规则
			$saverule = array('rand');
		} 
		
		if(is_array($saverule)){
			$rule = $saverule[0]; //函数名
			$param = empty($saverule[1]) ? '' : $saverule[1]; //参数
			if (method_exists($this, $rule)) {
				//使用自定义方法名作为标识号
				$saveName = $this->$rule($param) . "." . $filename['extension'];
			} elseif (function_exists($rule)) {
				//使用函数生成一个唯一文件标识号
				$saveName = $rule($param) . "." . $filename['extension'];
			} else {
				//使用给定的文件名作为标识号
				$saveName = $rule . "." . $filename['extension'];
			}
		}else{
			$saveName = $saverule.'.'.$filename['extension'];
		}
		return $saveName;
	}

	/**
	 * 检查上传的文件
	 * @param array $file 文件信息
	 */
	private function check($file) {
		if ($file['error'] !== 0) {
			//文件上传失败
			$this->error($file['error']);
			return false;
		}
		//文件上传成功，检查文件大小
		if (!$this->checkSize($file['size'])) {
			$this->error = '上传文件大小不符！';
			return false;
		}
		//检查文件类型
		if (!$this->checkExt($file['extension'])) {
			$this->error = '上传文件类型不允许';
			return false;
		}
		//检查是否合法上传
		if (!$this->checkUpload($file['tmp_name'])) {
			$this->error = '非法上传文件！';
			return false;
		}
		return true;
	}

	/**
	 * 检查上传的文件后缀是否合法
	 * @param string $ext 后缀名
	 */
	private function checkExt($ext) {
		if (!empty($this->exts)) return in_array(strtolower($ext), $this->exts, true);
		return true;
	}

	/**
	 * 检查文件大小是否合法
	 * @param integer $size 数据
	 */
	private function checkSize($size) {
		return !($size > $this->maxsize * 1024) || (-1 == $this->maxsize);
	}

	/**
	 * 检查文件是否非法提交
	 * @param string $filename 文件名
	 */
	private function checkUpload($filename) {
		return is_uploaded_file($filename);
	}

	/**
	 * 取得上传文件的信息
	 */
	public function getFileInfo() {
		return $this->fileinfo;
	}

	/**
	 * 取得最后一次错误信息
	 */
	public function getError() {
		return $this->error;
	}
	
	/**
	 * 根据当前时间得到上传文件的随机保存名称
	 */
	private function rand() {
		return md5(date('YmdHis') . mt_rand(100, 999));
	}
	
	/**
	 * 获取子目录的名称
	 * @param array $file  上传的文件信息
	 */
//	private function getSubName($file) {
//		switch ($this->subType) {
//			case 'date':
//				$dir = '/' . date('Y/md');
//				break;
//			case 'hash':
//				$name = md5($file['savename']);
//				$dir = '';
//				for ($i = 0; $i < 2; $i++) {
//					$dir .= '/' . $name{$i};
//				}
//				break;
//			default:
//				$dir = '';
//				break;
//		}
//		return $dir;
//	}

}