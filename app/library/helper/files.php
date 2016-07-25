<?php
// +----------------------------------------------------------------------
// | 文件管理类
// +----------------------------------------------------------------------

class helper_files {

	protected $path; //文件管理根目录

	//初始化系统
	public function __construct() {
		$this->init();
	}

	//初始化运行 设置待操作的根目录
	public function init($path='') {
		$path = $path ? $path : UPLOAD_PATH;
		$this->path = $path;
	}

	//更改文件名
	public function renameFile($oldname, $newname) {
		$oldname = $this->fullpath($oldname);
		$newname = $this->fullpath($newname);
		if (($oldname != $newname) && is_writable($oldname) && !file_exists($newname)) {
			return rename($oldname, $newname);
		} else {
			return false;
		}
	}

	//移动文件
	public function moveFile($oldfile, $newfile) {
		if ($oldfile != "" && $oldfile != $newfile && !ereg("\.\.", $oldfile)) {
			$oldpath = $this->path . $oldfile;
			$newpath = $this->path . $newfile;
			$newdir = dirname($newpath);
			if (!is_dir($newdir))
				mk_dir($newdir);
			if (is_readable($oldpath) && is_writable($oldpath) && is_readable($newdir)) {
				copy($oldpath, $newpath);
				unlink($oldpath);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	//删除文件
	public function deleteFile($filename) {
		$filename = $this->fullpath($filename);
		return unlink($filename);
	}

	//创建新目录
	public function newDir($dirname) {
		$newdir = $this->fullpath($dirname);
		return mk_dir($newdir);
	}

	//删除指定目录
	//警告: $r:true 将会递归删除下面所有文件
	public function deleteDir($indir, $r=false) {
		$newdir = $this->fullpath($indir);
		if (!is_dir($newdir))  return false;
		if ($r) {
			//递归删除
			$dh = dir($newdir);
			while ($filename = $dh->read()) {
				if ($filename == "." || $filename == "..")  continue;
				if (is_dir("$newdir/$filename")) {
					if (!$this->deleteDir("$indir/$filename", $r))
						return false;
				}else {
					if (!unlink("$newdir/$filename"))
						return false;
				}
			}
			$dh->close();
			return rmdir($newdir);
		}else {
			//普通删除
			return rmdir($newdir);
		}
	}

	//根据正则获得某目录合符规则的所有文件  （递归查找子目录）
	public function getMatchFiles($indir, $fileexp='', &$filearr=array()) {
		$newdir = $this->fullpath($indir);
		if(!is_dir($newdir)) return false;
		$dh = dir($newdir);
		while ($filename = $dh->read()) {		
			if ($filename == "." || $filename == "..")  continue;
			$truefile = $newdir.'/'.$filename;
			if (is_dir($truefile)) {
				$this->getMatchFiles("$indir/$filename", $fileexp, $filearr);
			} elseif (preg_match("/(" . $fileexp . ")/i", $filename)) {
				$filearr[] = $truefile;
			}
		}
		$dh->close();
		return $filearr;
	}

	//取得目录下面的文件信息
	public function getDirList($pathname, $pattern='*') {
		$pathname = $this->fullpath($pathname);
		if (!is_dir($pathname)) return false;
		$dir = $dir1 = $dir2 = array();
		$list = glob($pathname.'/'.$pattern);
		foreach ($list as $i => $file) {
			$dir[$i]['name'] = basename($file);
			$dir[$i]['path'] = realpath($file);
			$dir[$i]['owner'] = fileowner($file);
			$dir[$i]['perms'] = fileperms($file);
			$dir[$i]['inode'] = fileinode($file);
			$dir[$i]['group'] = filegroup($file);			
			$dir[$i]['atime'] = fileatime($file);
			$dir[$i]['ctime'] = filectime($file);
			$dir[$i]['size'] = filesize($file);
			$dir[$i]['size_format'] = $this->formatByte($dir[$i]['size']);
			$dir[$i]['filetype'] = filetype($file);
			$dir[$i]['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
			$dir[$i]['mtime'] = filemtime($file);
			$dir[$i]['isdir'] = is_dir($file);
			$dir[$i]['isfile'] = is_file($file);
			$dir[$i]['islink'] = is_link($file);
			$dir[$i]['isexecutable']= function_exists('is_executable')?is_executable($file):'';
			$dir[$i]['isreadable'] = is_readable($file);
			$dir[$i]['iswritable'] = is_writable($file);
			if ($dir[$i]['isdir']) {
				$dir1[] = $dir[$i];
			} else {
				$dir2[] = $dir[$i];
			}
		}
		$dir = array_merge($dir1, $dir2);
		unset($list,$pathname,$dir1,$dir2);
		return $dir;
	}

	//检查目录及子目录文件大小
	public function getDirSize($indir,$format=true,&$size=0) {
		$newdir = $this->fullpath($indir);
		$dh = dir($newdir);
		while ($filename = $dh->read()) {
			if ($filename == "." || $filename == "..")  continue;
			if (is_dir("$newdir/$filename")) {
				$this->getDirSize("$indir/$filename",$format,$size);
			} else {
				$size = $size + filesize("$newdir/$filename");
			}
		}
		return $format?$this->formatByte($size):$size;
	}

	//字节格式化 把字节数格式为 B K M G T 描述的大小
	public function formatByte($size, $dec=2) {
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return round($size, $dec) . " " . $a[$pos];
	}

	//得到文件全路径
	protected function fullpath($name) {
		$name = str_replace("\\", '/', $name);
		$name = preg_replace("|\.\./*|i", '', $name);
		$name = preg_replace("|\./*|i", '/', $name);
		$name = preg_replace("|/{2,}|i", '/', $this->path . '/' . $name);
		return $name;
	}


}

?>