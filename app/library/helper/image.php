<?php

// +----------------------------------------------------------------------
// | 图像操作类库
// +----------------------------------------------------------------------

class helper_image {

	/**
	  +----------------------------------------------------------
	 * 裁剪缩略图  支持自动裁剪和手工裁剪
	  +----------------------------------------------------------
	 * @param string $src_img 原图文件名
	 * @param string $dst_img 缩略图文件名
	 * @param integer $dst_w 缩略图宽度
	 * @param integer $dst_h 缩略图高度
	 * @param integer $cut 裁剪模式 0自动等比例裁剪 1自动按原图中心裁剪 array()手工裁剪
	 * @param integer $quality 缩略图质量
	  +----------------------------------------------------------
	 * @return void
	  +----------------------------------------------------------
	 */
	static public function thumb($src_img, $dst_img, $dst_w=100, $dst_h=100, $cut=1, $quality=85) {
		//图片详细信息
		$img = array();
		$imginfo = self::getImageInfo($src_img);
		$img['src_img'] = $src_img;
		$img['src_w'] = $src_w = $imginfo['width'];
		$img['src_h'] = $src_h = $imginfo['height'];
		$img['dst_img'] = $dst_img;

		//缩略图长和宽都比原图大，直接复制原图
		if($src_w < $dst_w && $src_h < $dst_h){
			return copy($src_img,$dst_img);
		}

		if ($src_w < $dst_w) {
			$img['dst_w'] = $dst_w = $src_w;
		} else {
			$img['dst_w'] = $dst_w;
		}
		if ($src_h < $dst_h) {
			$img['dst_h'] = $dst_h = $src_h;
		} else {
			$img['dst_h'] = $dst_h;
		}

		$img['type'] = $type = $imginfo['type'];
		//宽高比
		$img['rate_src'] = $rate_src = $img['src_w'] / $img['src_h'];
		$img['rate_dst'] = $rate_dst = $img['dst_w'] / $img['dst_h'];

		if (is_numeric($cut)) { //自动裁剪
			switch ($cut) {
				//默认等比例缩放 大小不超过新图的长宽
				case 0:
					//目标填充大小
					if ($rate_src < $rate_dst) { //目标图高固定 求宽
						$img['fill_w'] = floor($src_w * $dst_h / $src_h);
						$img['fill_h'] = $dst_h;
					} elseif ($rate_src > $rate_dst) { //目标图宽固定 求高
						$img['fill_w'] = $dst_w;
						$img['fill_h'] = floor($src_h * $dst_w / $src_w);
					} else {
						$img['fill_w'] = $dst_w;
						$img['fill_h'] = $dst_h;
					}
					//原图坐标
					$img['src_x'] = 0;
					$img['src_y'] = 0;
					//原图截取大小
					$img['copy_w'] = $src_w;
					$img['copy_h'] = $src_h;
					//目标大小
					$img['dst_w'] = $img['fill_w'];
					$img['dst_h'] = $img['fill_h'];
					break;
				//按目标图大小从原图中心等比例缩放截取
				case 1:
					//原图截取大小和坐标
					if ($rate_src < $rate_dst) { //原图宽固定 求高
						$img['copy_w'] = $src_w;
						$img['copy_h'] = floor($src_w * $dst_h / $dst_w);
						//宽固定 X坐标移动 Y坐标不变
						$img['src_x'] = 0;
						$img['src_y'] = floor(($src_h - $img['copy_h']) / 2);
					} elseif ($rate_src > $rate_dst) { //原图高固定 求宽
						$img['copy_w'] = floor($src_h * $dst_w / $dst_h);
						$img['copy_h'] = $src_h;
						//高固定 X坐标不变 Y坐标移动
						$img['src_x'] = floor(($src_w - $img['copy_w']) / 2);
						$img['src_y'] = 0;
					} else {
						//原图截取大小
						$img['copy_w'] = $src_w;
						$img['copy_h'] = $src_h;
					}
					//填充大小
					$img['fill_w'] = $dst_w;
					$img['fill_h'] = $dst_h;
					break;
				default:
					//原图坐标
					$img['src_x'] = 0;
					$img['src_y'] = 0;
					//原图截取大小
					$img['copy_w'] = $src_w;
					$img['copy_h'] = $src_h;
					break;
			}
		} elseif (is_array($cut)) { //手工裁剪
			$img['src_x'] = $cut[0];
			$img['src_y'] = $cut[1];
			$img['copy_w'] = $cut[2];
			$img['copy_h'] = $cut[3];
			$img['fill_w'] = $dst_w;
			$img['fill_h'] = $dst_h;
		} else {
			return false;
		}

		//设置原图句柄
		$createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
		if ($type == 'bmp') {
			$src_res = $this->imagecreatefrombmp($src_img);
		} else {
			$src_res = $createFun($src_img);
		}

		//检查目标图路径
		if (!is_dir(dirname($dst_img))) {
			mk_dir(dirname($dst_img));
		}

		//开始创建目标图
		$dst_res = imagecreatetruecolor($img['dst_w'], $img['dst_h']);
		$white = ImageColorAllocate($dst_res, 255, 255, 255);
		imagefilledrectangle($dst_res, 0, 0, $img['fill_w'], $img['fill_h'], $white); // 填充背景色
		//复制指定大小的原图到新图
		if (function_exists("ImageCopyResampled")) {
			imagecopyresampled($dst_res, $src_res, 0, 0, $img['src_x'], $img['src_y'], $img['fill_w'], $img['fill_h'], $img['copy_w'], $img['copy_h']);
		} else {
			imagecopyresized($dst_res, $src_res, 0, 0, $img['src_x'], $img['src_y'], $img['fill_w'], $img['fill_h'], $img['copy_w'], $img['copy_h']);
		}

		// 对jpeg图形设置隔行扫描
		if ('jpg' == $type || 'jpeg' == $type)
			imageinterlace($dst_res, true);

		//输出缩略图
		$dstinfo = pathinfo($dst_img);
		$imgtype = $dstinfo['extension'];
		$imgtype = $imgtype ? $imgtype : $type;
		$imageFun = 'image' . ($imgtype == 'jpg' ? 'jpeg' : $imgtype);
		if ($imgtype == 'jpg' || $imgtype == 'jpeg') {
			$imageFun($dst_res, $dst_img, $quality);
		} else {
			$imageFun($dst_res, $dst_img);
		}
		//释放资源
		imagedestroy($src_res);
		imagedestroy($dst_res);
	}

	/**
	  +----------------------------------------------------------
	 * 取得图像信息
	  +----------------------------------------------------------
	 * @param string $image 图像文件名
	  +----------------------------------------------------------
	 * @return mixed
	  +----------------------------------------------------------
	 */
	static public function getImageInfo($img) {
		$imageInfo = getimagesize($img);
		if ($imageInfo !== false) {
			$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
			$imageSize = filesize($img);
			$info = array(
				"width" => $imageInfo[0],
				"height" => $imageInfo[1],
				"type" => $imageType,
				"size" => $imageSize,
				"mime" => $imageInfo['mime']
			);
			return $info;
		} else {
			return false;
		}
	}

	/**
	  +----------------------------------------------------------
	 * 为图片添加水印
	  +----------------------------------------------------------
	 * @param string $source 原文件名
	 * @param string $water  水印图片
	 * @param string $position  水印位置 0随机  1上左 2上中 3上右 4中左 5中中 6中右 7下左 8下中 9下右
	 * @param string $$savename  添加水印后的图片名
	 * @param string $alpha  水印的透明度
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function water($source, $water, $savename=null, $position=9, $alpha=80) {
		//检查文件是否存在
		if (!file_exists($source) || !file_exists($water))
			return false;

		if(!is_numeric($position)) $position = 9;

		//如果没有给出保存文件名，默认为原图像名
		if ($savename) {
			copy($source,$savename);
		}else{
			$savename=$source;
		}

		//图片信息
		$sInfo = self::getImageInfo($source);
		if($sInfo['type']=='jpg') $sInfo['type'] = 'jpeg';
		$wInfo = self::getImageInfo($water);
		if($wInfo['type']=='jpg') $wInfo['type'] = 'jpeg';
		//如果图片小于水印图片，不生成图片
		if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
			return false;

		//建立图像
		$sCreateFun = "imagecreatefrom" . $sInfo['type'];
		$sImage = $sCreateFun($source);
		$wCreateFun = "imagecreatefrom" . $wInfo['type'];
		$wImage = $wCreateFun($water);

		//设定图像的混色模式
		imagealphablending($wImage, true);

		//图像位置,默认为右下角右对齐
		switch ($position) {
			case 0:
				$posY = mt_rand(0, ($sInfo["height"] - $wInfo["height"]));
				$posX = mt_rand(0, ($sInfo["width"] - $wInfo["width"]));
				break;
			case 1:
				$posY = $posX = 0;
				break;
			case 2:
				$posY = 0;
				$posX = ($sInfo["width"] - $wInfo["width"])/2;
				break;
			case 3:
				$posY = 0;
				$posX = $sInfo["width"] - $wInfo["width"];
				break;
			case 4:
				$posY = ($sInfo["height"] - $wInfo["height"])/2;
				$posX = 0;
				break;
			case 5:
				$posY = ($sInfo["height"] - $wInfo["height"])/2;
				$posX = ($sInfo["width"] - $wInfo["width"])/2;
				break;
			case 6:
				$posY = ($sInfo["height"] - $wInfo["height"])/2;
				$posX = $sInfo["width"] - $wInfo["width"];
				break;
			case 7:
				$posY = $sInfo["height"] - $wInfo["height"];
				$posX = 0;
				break;
			case 8:
				$posY = $sInfo["height"] - $wInfo["height"];
				$posX = ($sInfo["width"] - $wInfo["width"])/2;
				break;
			case 9:
				$posY = $sInfo["height"] - $wInfo["height"];
				$posX = $sInfo["width"] - $wInfo["width"];
				break;
			default:
				break;
		}
		

		//生成混合图像
		if($wInfo['type']=='png'){
			imagecopy($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height']);
		}else{
			imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);
		}		
		
		//输出图像
		$ImageFun = 'image' . $sInfo['type'];

		//保存图像		
		if ($sInfo['type'] == 'jpg' || $sInfo['type'] == 'jpeg') {
			imagejpeg($sImage, $savename, 90);
		} else {
			$ImageFun($sImage, $savename);
		}
		imagedestroy($sImage);
	}

	static public function showImg($imgFile, $text='', $x='10', $y='10', $alpha='50') {
		//获取图像文件信息
		$info = self::getImageInfo($imgFile);
		if ($info !== false) {
			$createFun = str_replace('/', 'createfrom', $info['mime']);
			$im = $createFun($imgFile);
			if ($im) {
				$ImageFun = str_replace('/', '', $info['mime']);
				//水印开始
				if (!empty($text)) {
					$tc = imagecolorallocate($im, 0, 0, 0);
					if (is_file($text) && file_exists($text)) {//判断$text是否是图片路径
						// 取得水印信息
						$textInfo = self::getImageInfo($text);
						$createFun2 = str_replace('/', 'createfrom', $textInfo['mime']);
						$waterMark = $createFun2($text);
						//$waterMark=imagecolorallocatealpha($text,255,255,0,50);
						$imgW = $info["width"];
						$imgH = $info["width"] * $textInfo["height"] / $textInfo["width"];
						//$y	=	($info["height"]-$textInfo["height"])/2;
						//设置水印的显示位置和透明度支持各种图片格式
						imagecopymerge($im, $waterMark, $x, $y, 0, 0, $textInfo['width'], $textInfo['height'], $alpha);
					} else {
						imagestring($im, 80, $x, $y, $text, $tc);
					}
					//ImageDestroy($tc);
				}
				//水印结束
				if ($info['type'] == 'png' || $info['type'] == 'gif') {
					imagealphablending($im, FALSE); //取消默认的混色模式
					imagesavealpha($im, TRUE); //设定保存完整的 alpha 通道信息
				}
				Header("Content-type: " . $info['mime']);
				$ImageFun($im);
				@ImageDestroy($im);
				return;
			}

			//保存图像
			$ImageFun($sImage, $savename);
			imagedestroy($sImage);
			//获取或者创建图像文件失败则生成空白PNG图片
			$im = imagecreatetruecolor(80, 30);
			$bgc = imagecolorallocate($im, 255, 255, 255);
			$tc = imagecolorallocate($im, 0, 0, 0);
			imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
			imagestring($im, 4, 5, 5, "no pic", $tc);
			self::output($im);
			return;
		}
	}

	/**
	  +----------------------------------------------------------
	 * 根据给定的字符串生成图像
	  +----------------------------------------------------------
	 * @param string $string  字符串
	 * @param string $size  图像大小 width,height 或者 array(width,height)
	 * @param string $font  字体信息 fontface,fontsize 或者 array(fontface,fontsize)
	 * @param string $type 图像格式 默认PNG
	 * @param integer $disturb 是否干扰 1 点干扰 2 线干扰 3 复合干扰 0 无干扰
	 * @param bool $border  是否加边框 array(color)
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function buildString($string, $rgb=array(), $filename='', $type='png', $disturb=1, $border=true) {
		if (is_string($size))
			$size = explode(',', $size);
		$width = $size[0];
		$height = $size[1];
		if (is_string($font))
			$font = explode(',', $font);
		$fontface = $font[0];
		$fontsize = $font[1];
		$length = strlen($string);
		$width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
		$height = 22;
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = @imagecreatetruecolor($width, $height);
		} else {
			$im = @imagecreate($width, $height);
		}
		if (empty($rgb)) {
			$color = imagecolorallocate($im, 102, 104, 104);
		} else {
			$color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
		}
		$backColor = imagecolorallocate($im, 255, 255, 255); //背景色（随机）
		$borderColor = imagecolorallocate($im, 100, 100, 100);	 //边框色
		$pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));	 //点颜色

		@imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		@imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		@imagestring($im, 5, 5, 3, $string, $color);
		if (!empty($disturb)) {
			// 添加干扰
			if ($disturb = 1 || $disturb = 3) {
				for ($i = 0; $i < 25; $i++) {
					imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
				}
			} elseif ($disturb = 2 || $disturb = 3) {
				for ($i = 0; $i < 10; $i++) {
					imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $pointColor);
				}
			}
		}
		self::output($im, $type, $filename);
	}

	/**
	 +----------------------------------------------------------
	 * 生成图像验证码
	 +----------------------------------------------------------
	 * @param string $length  位数
	 * @param string $mode  类型
	 * @param string $type 图像格式
	 * @param string $width  宽度
	 * @param string $height  高度
	 * @param string $verifyName
	 * @param int $codeType 类型: 1-仿google 2-数学运算
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static public function buildImageVerify($length=4, $mode=1, $type='png', $width=48, $height=22, $verifyName='default', $codeType = 1) {
        switch ($codeType) {
            case 1: // 仿google
                $randval = helper_string::randString($length, $mode);
                $captcha = array('code' => $randval, 'time' => time());
                session('validate_'.$verifyName, $captcha);
                self::googleImage($randval);
                break;
            case 2: // 数学运算
                $num1 = rand(1, 99);
                $num2 = rand(1, 99);
                $randval = $num1 + $num2;
                $captcha = array('code' => $randval, 'time' => time());
                session('validate_'.$verifyName, $captcha);
                self::mathImage($num1, $num2, 100, 24);
                break;
            default:
                self::buildImageVerifyImage($length, $mode, $type, $width, $height, $verifyName);
        }
    }

    /**
    * 仿照google验证码
    * @param string $text 生成的文本
    */
    public static function googleImage($text) {
        $im_x = 160;
        $im_y = 40;
        $im = imagecreatetruecolor($im_x,$im_y);
        $text_c = ImageColorAllocate($im, mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        $tmpC0=mt_rand(100,255);
        $tmpC1=mt_rand(100,255);
        $tmpC2=mt_rand(100,255);
        $buttum_c = ImageColorAllocate($im,$tmpC0,$tmpC1,$tmpC2);
        imagefill($im, 16, 13, $buttum_c);

        $font = FEE_PATH. "/data/t1.ttf";

        for ($i=0;$i<strlen($text);$i++)
        {
            $tmp =substr($text,$i,1);
            $array = array(-1,1);
            $p = array_rand($array);
            $an = $array[$p]*mt_rand(1,10);//角度
            $size = 28;
            imagettftext($im, $size, $an, 15+$i*$size, 35, $text_c, $font, $tmp);
        }


        $distortion_im = imagecreatetruecolor ($im_x, $im_y);

        imagefill($distortion_im, 16, 13, $buttum_c);
        for ( $i=0; $i<$im_x; $i++) {
            for ( $j=0; $j<$im_y; $j++) {
                $rgb = imagecolorat($im, $i , $j);
                if( (int)($i+20+sin($j/$im_y*2*M_PI)*10) <= imagesx($distortion_im)&& (int)($i+20+sin($j/$im_y*2*M_PI)*10) >=0 ) {
                    imagesetpixel ($distortion_im, (int)($i+10+sin($j/$im_y*2*M_PI-M_PI*0.1)*4) , $j , $rgb);
                }
            }
        }
        //加入干扰象素;
        $count = 160;//干扰像素的数量
        for($i=0; $i<$count; $i++){
            $randcolor = ImageColorallocate($distortion_im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($distortion_im, mt_rand()%$im_x , mt_rand()%$im_y , $randcolor);
        }

        $rand = mt_rand(5,30);
        $rand1 = mt_rand(15,25);
        $rand2 = mt_rand(5,10);
        for ($yy=$rand; $yy<=+$rand+2; $yy++){
            for ($px=-80;$px<=80;$px=$px+0.1)
            {
                $x=$px/$rand1;
                if ($x!=0)
                {
                    $y=sin($x);
                }
                $py=$y*$rand2;

                imagesetpixel($distortion_im, $px+80, $py+$yy, $text_c);
            }
        }

        //设置文件头;
        Header("Content-type: image/JPEG");

        //以PNG格式将图像输出到浏览器或文件;
        ImagePNG($distortion_im);

        //销毁一图像,释放与image关联的内存;
        ImageDestroy($distortion_im);
        ImageDestroy($im);
    }

    /**
    * 数学计算验证码
    * @param int $w 宽度
    * @param int $h 高度
    */
    public static function mathImage($num1, $num2, $w, $h)
    {
        $im = imagecreate($w, $h);

        //imagecolorallocate($im, 14, 114, 180); // background color
        $red = imagecolorallocate($im, 255, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);

        $_SESSION['helloweba_math'] = $num1 + $num2;

        $gray = imagecolorallocate($im, 118, 151, 199);
        $black = imagecolorallocate($im, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));

        //画背景
        imagefilledrectangle($im, 0, 0, 100, 24, $black);
        //在画布上随机生成大量点，起干扰作用;
        for ($i = 0; $i < 80; $i++) {
            imagesetpixel($im, rand(0, $w), rand(0, $h), $gray);
        }

        imagestring($im, 5, 5, 4, $num1, $red);
        imagestring($im, 5, 30, 3, "+", $red);
        imagestring($im, 5, 45, 4, $num2, $red);
        imagestring($im, 5, 70, 3, "=", $red);
        imagestring($im, 5, 80, 2, "?", $white);

        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
    }

	/**
	  +----------------------------------------------------------
	 * 生成图像验证码
	  +----------------------------------------------------------
	 * @param string $length  位数
	 * @param string $mode  类型
	 * @param string $type 图像格式
	 * @param string $width  宽度
	 * @param string $height  高度
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function buildImageVerifyImage($length=4, $mode=1, $type='png', $width=48, $height=22, $verifyName='default') {
		$randval = helper_string::randString($length, $mode);
		$captcha = array('code' => $randval, 'time' => time());
		session('validate_'.$verifyName, $captcha);
		$width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = @imagecreatetruecolor($width, $height);
		} else {
			$im = @imagecreate($width, $height);
		}
		$r = Array(225, 255, 255, 223);
		$g = Array(225, 236, 237, 255);
		$b = Array(225, 236, 166, 125);
		$key = mt_rand(0, 3);

		$backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]); //背景色（随机）
		$borderColor = imagecolorallocate($im, 100, 100, 100);	 //边框色
		$pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));	 //点颜色

		@imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		@imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		$stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
		// 干扰
		for ($i = 0; $i < 10; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
		}
		for ($i = 0; $i < 25; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
		}
		for ($i = 0; $i < $length; $i++) {
			imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $randval{$i}, $stringColor);
		}
		//        @imagestring($im, 5, 5, 3, $randval, $stringColor);
		self::output($im, $type);
	}

	// 中文验证码
	static public function GBVerify($length=4, $type='png', $width=180, $height=50, $fontface='simhei.ttf', $verifyName='verify') {
		$code = helper_string::randString($length, 4);
		$width = ($length * 45) > $width ? $length * 45 : $width;
		$_SESSION[$verifyName] = md5($code);
		$im = imagecreatetruecolor($width, $height);
		$borderColor = imagecolorallocate($im, 100, 100, 100);	 //边框色
		$bkcolor = imagecolorallocate($im, 250, 250, 250);
		imagefill($im, 0, 0, $bkcolor);
		@imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		// 干扰
		for ($i = 0; $i < 15; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
		}
		for ($i = 0; $i < 255; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $fontcolor);
		}
		if (!is_file($fontface)) {
			$fontface = dirname(__FILE__) . "/" . $fontface;
		}
		for ($i = 0; $i < $length; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120)); //这样保证随机出来的颜色较深。
			$codex = helper_string::msubstr($code, 1, $i);
			imagettftext($im, mt_rand(16, 20), mt_rand(-60, 60), 40 * $i + 20, mt_rand(30, 35), $fontcolor, $fontface, $codex);
		}
		self::output($im, $type);
	}

	/**
	  +----------------------------------------------------------
	 * 把图像转换成字符显示
	  +----------------------------------------------------------
	 * @param string $image  要显示的图像
	 * @param string $type  图像类型，默认自动获取
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function showASCIIImg($image, $string='', $type='') {
		$info = self::getImageInfo($image);
		if ($info !== false) {
			$type = empty($type) ? $info['type'] : $type;
			unset($info);
			// 载入原图
			$createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
			$im = $createFun($image);
			$dx = imagesx($im);
			$dy = imagesy($im);
			$i = 0;
			$out = '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
			set_time_limit(0);
			for ($y = 0; $y < $dy; $y++) {
				for ($x = 0; $x < $dx; $x++) {
					$col = imagecolorat($im, $x, $y);
					$rgb = imagecolorsforindex($im, $col);
					$str = empty($string) ? '*' : $string[$i++];
					$out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">' . $str . '</span>', $rgb['red'], $rgb['green'], $rgb['blue']);
				}
				$out .= "<br>\n";
			}
			$out .= '</span>';
			imagedestroy($im);
			return $out;
		}
		return false;
	}

	/**
	  +----------------------------------------------------------
	 * 生成UPC-A条形码
	  +----------------------------------------------------------
	 * @static
	  +----------------------------------------------------------
	 * @param string $type 图像格式
	 * @param string $type 图像格式
	 * @param string $lw  单元宽度
	 * @param string $hi   条码高度
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function UPCA($code, $type='png', $lw=2, $hi=100) {
		static $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
	'0110001', '0101111', '0111011', '0110111', '0001011');
		static $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
	'1001110', '1010000', '1000100', '1001000', '1110100');
		$ends = '101';
		$center = '01010';
		/* UPC-A Must be 11 digits, we compute the checksum. */
		if (strlen($code) != 11) {
			die("UPC-A Must be 11 digits.");
		}
		/* Compute the EAN-13 Checksum digit */
		$ncode = '0' . $code;
		$even = 0;
		$odd = 0;
		for ($x = 0; $x < 12; $x++) {
			if ($x % 2) {
				$odd += $ncode[$x];
			} else {
				$even += $ncode[$x];
			}
		}
		$code.= ( 10 - (($odd * 3 + $even) % 10)) % 10;
		/* Create the bar encoding using a binary string */
		$bars = $ends;
		$bars.=$Lencode[$code[0]];
		for ($x = 1; $x < 6; $x++) {
			$bars.=$Lencode[$code[$x]];
		}
		$bars.=$center;
		for ($x = 6; $x < 12; $x++) {
			$bars.=$Rencode[$code[$x]];
		}
		$bars.=$ends;
		/* Generate the Barcode Image */
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($lw * 95 + 30, $hi + 30);
		} else {
			$im = imagecreate($lw * 95 + 30, $hi + 30);
		}
		$fg = ImageColorAllocate($im, 0, 0, 0);
		$bg = ImageColorAllocate($im, 255, 255, 255);
		ImageFilledRectangle($im, 0, 0, $lw * 95 + 30, $hi + 30, $bg);
		$shift = 10;
		for ($x = 0; $x < strlen($bars); $x++) {
			if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
				$sh = 10;
			} else {
				$sh = 0;
			}
			if ($bars[$x] == '1') {
				$color = $fg;
			} else {
				$color = $bg;
			}
			ImageFilledRectangle($im, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
		}
		/* Add the Human Readable Label */
		ImageString($im, 4, 5, $hi - 5, $code[0], $fg);
		for ($x = 0; $x < 5; $x++) {
			ImageString($im, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
			ImageString($im, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
		}
		ImageString($im, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
		/* Output the Header and Content. */
		self::output($im, $type);
	}

	static public function output($im, $type='png', $filename='') {
		header("Content-type: image/" . $type);
		$ImageFun = 'image' . $type;
		if (empty($filename)) {
			$ImageFun($im);
		} else {
			$ImageFun($im, $filename);
		}
		imagedestroy($im);
	}

}

function imagebmp(&$im, $filename = '', $bit = 8, $compression = 0) {
	if (!in_array($bit, array(1, 4, 8, 16, 24, 32))) {
		$bit = 8;
	} else if ($bit == 32) {// todo:32 bit
		$bit = 24;
	}

	$bits = pow(2, $bit);

	// 调整调色板
	imagetruecolortopalette($im, true, $bits);
	$width = imagesx($im);
	$height = imagesy($im);
	$colors_num = imagecolorstotal($im);

	if ($bit <= 8) {
		// 颜色索引
		$rgb_quad = '';
		for ($i = 0; $i < $colors_num; $i++) {
			$colors = imagecolorsforindex($im, $i);
			$rgb_quad .= chr($colors['blue']) . chr($colors['green']) . chr($colors['red']) . "\0";
		}

		// 位图数据
		$bmp_data = '';

		// 非压缩
		if ($compression == 0 || $bit < 8) {
			if (!in_array($bit, array(1, 4, 8))) {
				$bit = 8;
			}

			$compression = 0;

			// 每行字节数必须为4的倍数，补齐。
			$extra = '';
			$padding = 4 - ceil($width / (8 / $bit)) % 4;
			if ($padding % 4 != 0) {
				$extra = str_repeat("\0", $padding);
			}

			for ($j = $height - 1; $j >= 0; $j--) {
				$i = 0;
				while ($i < $width) {
					$bin = 0;
					$limit = $width - $i < 8 / $bit ? (8 / $bit - $width + $i) * $bit : 0;

					for ($k = 8 - $bit; $k >= $limit; $k -= $bit) {
						$index = imagecolorat($im, $i, $j);
						$bin |= $index << $k;
						$i++;
					}
					$bmp_data .= chr($bin);
				}
				$bmp_data .= $extra;
			}
		}
		// RLE8 压缩
		else if ($compression == 1 && $bit == 8) {
			for ($j = $height - 1; $j >= 0; $j--) {
				$last_index = "\0";
				$same_num = 0;
				for ($i = 0; $i <= $width; $i++) {
					$index = imagecolorat($im, $i, $j);
					if ($index !== $last_index || $same_num > 255) {
						if ($same_num != 0) {
							$bmp_data .= chr($same_num) . chr($last_index);
						}
						$last_index = $index;
						$same_num = 1;
					} else {
						$same_num++;
					}
				}
				$bmp_data .= "\0\0";
			}
			$bmp_data .= "\0\1";
		}
		$size_quad = strlen($rgb_quad);
		$size_data = strlen($bmp_data);
	} else {
		// 每行字节数必须为4的倍数，补齐。
		$extra = '';
		$padding = 4 - ($width * ($bit / 8)) % 4;
		if ($padding % 4 != 0) {
			$extra = str_repeat("\0", $padding);
		}

		// 位图数据
		$bmp_data = '';

		for ($j = $height - 1; $j >= 0; $j--) {
			for ($i = 0; $i < $width; $i++) {
				$index = imagecolorat($im, $i, $j);
				$colors = imagecolorsforindex($im, $index);

				if ($bit == 16) {
					$bin = 0 << $bit;

					$bin |= ( $colors['red'] >> 3) << 10;
					$bin |= ( $colors['green'] >> 3) << 5;
					$bin |= $colors['blue'] >> 3;

					$bmp_data .= pack("v", $bin);
				} else {
					$bmp_data .= pack("c*", $colors['blue'], $colors['green'], $colors['red']);
				}
				// todo: 32bit;
			}
			$bmp_data .= $extra;
		}
		$size_quad = 0;
		$size_data = strlen($bmp_data);
		$colors_num = 0;
	}

	// 位图文件头
	$file_header = "BM" . pack("V3", 54 + $size_quad + $size_data, 0, 54 + $size_quad);

	// 位图信息头
	$info_header = pack("V3v2V*", 0x28, $width, $height, 1, $bit, $compression, $size_data, 0, 0, $colors_num, 0);

	// 写入文件
	if ($filename != '') {
		$fp = fopen($filename, "wb");
		fwrite($fp, $file_header);
		fwrite($fp, $info_header);
		fwrite($fp, $rgb_quad);
		fwrite($fp, $bmp_data);
		fclose($fp);
		return 1;
	}

	// 浏览器输出
	header("Content-Type: image/bmp");
	echo $file_header . $info_header;
	echo $rgb_quad;
	echo $bmp_data;

	return 1;
}

function imagecreatefrombmp($file) {
	global $CurrentBit, $echoMode;
	$f = fopen($file, "r");
	$Header = fread($f, 2);

	if ($Header == "BM") {
		$Size = freaddword($f);
		$Reserved1 = freadword($f);
		$Reserved2 = freadword($f);
		$FirstByteOfImage = freaddword($f);

		$SizeBITMAPINFOHEADER = freaddword($f);
		$Width = freaddword($f);
		$Height = freaddword($f);
		$biPlanes = freadword($f);
		$biBitCount = freadword($f);
		$RLECompression = freaddword($f);
		$WidthxHeight = freaddword($f);
		$biXPelsPerMeter = freaddword($f);
		$biYPelsPerMeter = freaddword($f);
		$NumberOfPalettesUsed = freaddword($f);
		$NumberOfImportantColors = freaddword($f);

		if ($biBitCount < 24) {
			$img = imagecreate($Width, $Height);
			$Colors = pow(2, $biBitCount);
			for ($p = 0; $p < $Colors; $p++) {
				$B = freadbyte($f);
				$G = freadbyte($f);
				$R = freadbyte($f);
				$Reserved = freadbyte($f);
				$Palette[] = imagecolorallocate($img, $R, $G, $B);
			}
			if ($RLECompression == 0) {
				$Zbytek = (4 - ceil(($Width / (8 / $biBitCount))) % 4) % 4;
				for ($y = $Height - 1; $y >= 0; $y--) {
					$CurrentBit = 0;
					for ($x = 0; $x < $Width; $x++) {
						$C = freadbits($f, $biBitCount);
						imagesetpixel($img, $x, $y, $Palette[$C]);
					}
					if ($CurrentBit != 0) {
						freadbyte($f);
					}
					for ($g = 0; $g < $Zbytek; $g++)
						freadbyte($f);
				}
			}
		}

		if ($RLECompression == 1) { //$BI_RLE8
			$y = $Height;
			$pocetb = 0;
			while (true) {
				$y--;
				$prefix = freadbyte($f);
				$suffix = freadbyte($f);
				$pocetb+=2;

				$echoit = false;

				if ($echoit

					)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if (($prefix == 0) and ($suffix == 1))
					break;
				if (feof($f))
					break;

				while (!(($prefix == 0) and ($suffix == 0))) {
					if ($prefix == 0) {
						$pocet = $suffix;
						$Data.=fread($f, $pocet);
						$pocetb+=$pocet;
						if ($pocetb % 2 == 1) {
							freadbyte($f);
							$pocetb++;
						}
					}
					if ($prefix > 0) {
						$pocet = $prefix;
						for ($r = 0; $r < $pocet; $r++)
							$Data.=chr($suffix);
					}
					$prefix = freadbyte($f);
					$suffix = freadbyte($f);
					$pocetb+=2;
					if ($echoit)
						echo "Prefix: $prefix Suffix: $suffix<BR>";
				}

				for ($x = 0; $x < strlen($Data); $x++) {
					imagesetpixel($img, $x, $y, $Palette[ord($Data[$x])]);
				}
				$Data = "";
			}
		}

		if ($RLECompression == 2) { //$BI_RLE4
			$y = $Height;
			$pocetb = 0;

			while (true) {
				//break;
				$y--;
				$prefix = freadbyte($f);
				$suffix = freadbyte($f);
				$pocetb+=2;

				$echoit = false;

				if ($echoit

					)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if (($prefix == 0) and ($suffix == 1))
					break;
				if (feof($f))
					break;

				while (!(($prefix == 0) and ($suffix == 0))) {
					if ($prefix == 0) {
						$pocet = $suffix;

						$CurrentBit = 0;
						for ($h = 0; $h < $pocet; $h++)
							$Data.=chr(freadbits($f, 4));
						if ($CurrentBit != 0)
							freadbits($f, 4);
						$pocetb+=ceil(($pocet / 2));
						if ($pocetb % 2 == 1) {
							freadbyte($f);
							$pocetb++;
						}
					}
					if ($prefix > 0) {
						$pocet = $prefix;
						$i = 0;
						for ($r = 0; $r < $pocet; $r++) {
							if ($i % 2 == 0) {
								$Data.=chr($suffix % 16);
							} else {
								$Data.=chr(floor($suffix / 16));
							}
							$i++;
						}
					}
					$prefix = freadbyte($f);
					$suffix = freadbyte($f);
					$pocetb+=2;
					if ($echoit)
						echo "Prefix: $prefix Suffix: $suffix<BR>";
				}
				for ($x = 0; $x < strlen($Data); $x++) {
					imagesetpixel($img, $x, $y, $Palette[ord($Data[$x])]);
				}
				$Data = "";
			}
		}
		if ($biBitCount == 24) {
			$img = imagecreatetruecolor($Width, $Height);
			$Zbytek = $Width % 4;

			for ($y = $Height - 1; $y >= 0; $y--) {
				for ($x = 0; $x < $Width; $x++) {
					$B = freadbyte($f);
					$G = freadbyte($f);
					$R = freadbyte($f);
					$color = imagecolorexact($img, $R, $G, $B);
					if ($color == -1)
						$color = imagecolorallocate($img, $R, $G, $B);
					imagesetpixel($img, $x, $y, $color);
				}
				for ($z = 0; $z < $Zbytek; $z++)
					freadbyte($f);
			}
		}
		return $img;
	}
	fclose($f);
}

function freadbyte($f) {
	return ord(fread($f, 1));
}

function freadword($f) {
	$b1 = freadbyte($f);
	$b2 = freadbyte($f);
	return $b2 * 256 + $b1;
}

function freaddword($f) {
	$b1 = freadword($f);
	$b2 = freadword($f);
	return $b2 * 65536 + $b1;
}

?>