<?php
//得到服务器网卡MAC地址  必须有exec执行权限
class helper_networkmac
{
	private $return_array = array(); // 返回带有MAC地址的字串数组
	private $mac_addr=array();

	public function __construct(){
		switch (strtolower(PHP_OS) ){
			case "linux":$this->forLinux();break;
			case "solaris":break;
			case "unix":break;
			case "aix":break;
			default:$this->forWindows();break;
		}

		$temp_array = array();
		foreach ( $this->return_array as $value )
		{
			if ( preg_match( "/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp_array ) )
			{
				$this->mac_addr[] = $temp_array[0];
			}
		}
		unset($temp_array);
		return $this->mac_addr;
	}
	
	public function getMac(){
		return $this->mac_addr;
	}

	protected function forWindows(){
		@exec("ipconfig /all", $this->return_array);		
		if ( $this->return_array )
		return $this->return_array;
		else{
			$ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
			if ( is_file($ipconfig) )
			@exec($ipconfig." /all", $this->return_array);
			else
			@exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array);
			return $this->return_array;
		}
	}

	protected function forLinux(){
		@exec("ifconfig -a", $this->return_array);
		return $this->return_array;
	}
}
?>