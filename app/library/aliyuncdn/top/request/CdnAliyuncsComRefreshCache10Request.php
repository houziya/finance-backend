<?php
/**
 * TOP API: cdn.aliyuncs.com.RefreshCache.1.0 request
 * 
 * @author auto create
 * @since 1.0, 2013.09.22
 */
class CdnAliyuncsComRefreshCache10Request
{
	/** 
	 * 刷新指定URL内容至Cache节点。
一天最多刷新1000个文件（URL）
每个URL以“,”分隔
	 **/
	private $objects;
	
	private $apiParas = array();
	
	public function setObjects($objects)
	{
		$this->objects = $objects;
		$this->apiParas["Objects"] = $objects;
	}

	public function getObjects()
	{
		return $this->objects;
	}

	public function getApiMethodName()
	{
		return "cdn.aliyuncs.com.RefreshCache.1.0";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->objects,"objects");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
