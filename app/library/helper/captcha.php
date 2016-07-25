<?php
define('FONT_TYPE', C('sys_font_type')); //字体文件路径
//验证码类
class helper_captcha{
	public  $scode = null;//生成的验证码内容
	public $code_type = 'session';//验证方式 session/cookie
	private $code_key = 'captcha_key'; //用来保存验证码的 session/cookie 变量名
	private $im; //生成验证码图片的句柄
	private $width = 70;//图片宽度
	private $height = 30;//图片高度

	//初始化验证方法
	public function __construct($code_type='session',$code_key = 'captcha_key'){
		$this->code_type = $code_type;
		$this->code_key = $code_key;
	}

	/*
	输出验证码图片
	a 表示在验证码验证码中使用小写英文
	A 表示在验证码验证码中使用大写英文
	9 表示在验证码验证码中使用数字
	! 表示在验证码验证码中使用不规则字符串
	*/
	public function show($type='9'){

		if ($this->isExtGD()){//检查GD支持
			$this->im = imagecreate($this->width,$this->height); //创建图片
			$bgcolor = ImageColorAllocate($this->im,255,255,255); //背景颜色
			$iborder = ImageColorAllocate($this->im,100,100,100); //边框颜色			
			$bgcolor_img = ImageColorAllocate($this->im, mt_rand(150,200),mt_rand(150,200),mt_rand(150,200)); //干扰物颜色
			for ($i=0;$i<4;$i++){
				$fontColor[] = ImageColorAllocate($this->im, mt_rand(0,150),mt_rand(0,150),mt_rand(0,150));//字体颜色
			}

			//绘制干扰物网格
			$x = mt_rand(0,6); $y = mt_rand(0,6);
			for ($i=0;$i<10;$i++){//网格
				imageline($this->im, $x + 5*$i , mt_rand(0,20) , $x + 5*$i , mt_rand(20,40), $bgcolor_img);
				if ($i%3==0){
					imageline($this->im,mt_rand(0,40),$y + $i + 2, mt_rand(50,100),$y + $i + 2, $bgcolor_img);
				}
			}
			imagerectangle($this->im, 0, 0, $this->width-1, $this->height-1, $iborder); //边框
			$this->randString($type);//取得一个随即数
			$rndcodelen = strlen($this->scode);//计算随机数的长度
			for ($i=0;$i<$rndcodelen;$i++){
				$rndstring[] = substr($this->scode,$i,1);
			}
			for($i = 0; $i < $rndcodelen; $i++){ //改变文字颜色
				$font_type = FONT_TYPE;
				if( $this->isExtGD("imagettftext") && is_file($font_type)){

					$strposs[$i][0] = $i * 16+2;
					$strposs[$i][1] = mt_rand(19,24);
					$strposs[$i][2] = mt_rand(-8,8);
					//文字
					imagettftext($this->im, 15, $strposs[$i][2], $strposs[$i][0], $strposs[$i][1], $fontColor[$i], $font_type, $rndstring[$i]);
				}else{
					imagestring($this->im, 5, $i*10+6, mt_rand(2,5), $rndstring[$i], $fontColor2);
				}
			}
			header("Pragma:no-cache\r\n");
			header("Cache-Control:no-cache\r\n");
			header("Expires:0\r\n");
			$this->supportType();
			ImageDestroy($this->im);
		}else{
			header("content-type:image/jpeg\r\n");
			header("Pragma:no-cache\r\n");
			header("Cache-Control:no-cache\r\n");
			header("Expires:0\r\n");
			echo $this->getIMG();
		}
		return $result;
	}
	
	//检查GD库是否已经载入 'imagecreate'/'imagettftext'
	private function isExtGD($fun = 'imagecreate'){
		$result = false;
		$result = function_exists($fun);
		return $result;
	}
	
	//支持的图片类型
	private function supportType(){
		$result = false;
		//输出特定类型的图片格式，优先级为 gif -> jpg ->png
		if(function_exists("imagepng")){
			header("content-type:image/png\r\n");
			imagepng($this->im);
			$result = true;
		}else{
			header("content-type:image/gif\r\n");
			imagecolortransparent($this->im);
			imagejpeg($this->im);
			$result = true;
		}
		return $result;
	}

	//得到随机串 字母中不包含 I L O 数字中不包含 0 1
	private function randString($type='9'){
		$Case = '';
		$result = '';
		if ( strstr($type, 'a')){// a 表示在验证码验证码中使用小写英文
			$Case .= 'abcdefghgkmnpqrctuvwxyz';
		}
		if (strstr($type, 'A')) {// A 表示在验证码验证码中使用大写英文
			$Case .= 'ABCDEFGHJKMNPQRSTUVWXYZ';
		}
		if (strstr($type, '9')) {// 9 表示在验证码验证码中使用数字
			$Case .= '123456789';
		}
		if (strstr($type, '!')) {// ! 表示在验证码验证码中使用不规则字符串
			$Case .= '!@#$%^&*()_+-=?';
		}
		$leng = strlen($Case);
		for ($i=0;$i<4;$i++){
			$rnd = mt_rand(0,$leng-1);
			$result .= $Case[$rnd];
		}
		if ($this->code_type == 'session') {
			if (!isset($_SESSION)) {
				session_start();
			}
			$_SESSION[$this->code_key] = $result;
		}else {
			setcookie($this->code_key,$result,time()+300,"/");
		}
		$this->scode = $result;
		return $result;
	}

	//在不支持GD的情况下输出图片内容
	private function getIMG()
	{
		//不支持GD，只输出字母 ABCD
		if ($this->code_type == 'session') {
			if (!isset($_SESSION)) {
				session_start();
			}
			$_SESSION[$this->code_key] = "1234";
		}else {
			setcookie($this->code_key,"1234",time()+300,"/");
		}
		header("content-type:image/jpeg\r\n");
		header("Pragma:no-cache\r\n");
		header("Cache-Control:no-cache\r\n");
		header("Expires:0\r\n");
		$fp = fopen(FEE_PATH."/data/code.jpg","r");
		$result = fread($fp,filesize(FEE_PATH."/data/code.jpg"));
		fclose($fp);
		return $result;
	}

	//检查验证码
	public function check($UserCode){
		$result = false;
		if ($this->code_type == 'session') {
			if (!isset($_SESSION)) {
				session_start();
			}
			if (isset($_SESSION[$this->code_key]) && strcasecmp($_SESSION[$this->code_key] , $UserCode)==0 ){
				$result = true;
			}
			if (isset($_SESSION[$this->code_key]) && strlen($_SESSION[$this->code_key])==0){
				$result = false;
			}
		}else {
			if (isset($_COOKIE[$this->code_key]) && strcasecmp($_COOKIE[$this->code_key] , $UserCode)==0){
				$result = true;
			}
			if (isset($_COOKIE[$this->code_key]) && strlen($_COOKIE[$this->code_key])==0){
				$result = false;
			}
		}
		return $result;
	}
}