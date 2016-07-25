<?php
/**
 * php导出csv文件
 * @author szy
 */
  
class helper_xtcsv
{  
    // 定义类属性 
  private $total = 0;         // 总记录数 
  public $data = array(); //设置数据
  public $field = array(); //设置表头
  public $exportName = 'export.csv'; // 导出的文件名 
  public $separator = ',';      // 设置分隔符 
  public $delimiter = '';      // 设置定界符 
  
    
  public function setData($dt=array()) //设置数据
  {
      $this->data = $dt;
  }

   public  function getTotal() //获取总的记录数
  {
     return count($this->data);
  }
  
 
   public function setField($field =  array())
  {
       $this->field = $field;
  }
  
 
  
   public function setExportName($filename='')//设置导出文件名 
  { 
    if($filename!='')
	{ 
      $this->exportName = $filename; 
    } 
  } 
  
   public function setSeparator($separator) //设置分隔符
  { 
    if($separator!='')
	{ 
      $this->separator = $separator; 
    } 
  } 
  
   
   public function setDelimiter($delimiter) //设置定界符
  { 
    if($delimiter!='')
	{ 
      $this->delimiter = $delimiter; 
    } 
  } 
  
 
   public function export() //执行导出
  { 
  
    // 获取总记录数 
    $this->total = $this->getTotal(); 
  
    // 没有记录 
    if(!$this->total)
	{ 
      return false; 
    } 
 
    // 获取导出的列名 
    $fields = $this->field; 
  
    // 设置导出文件header 
    $this->setHeader(); 


	$exportData .= iconv("utf-8","gb2312",$this->formatCSV($fields));

	$data = $this->data; //获取数据
    if($data)
	{ 
        foreach($data as $row)
		{ 
			//$this->formatCSV($row)
          $exportData .= iconv("utf-8","gb2312",$this->formatCSV($row)); 
        } 
    } 
 
    echo $exportData;
  } 
  
  
  
  /** 设置导出文件header */
  private function setHeader(){ 
    header('content-type:application/x-msexcel;charset=GB2312'); 
  
    $ua = $_SERVER['HTTP_USER_AGENT']; 
  
    if(preg_match("/MSIE/", $ua)){ 
      header('content-disposition:attachment; filename="'.rawurlencode($this->exportName).'"'); 
    }elseif(preg_match("/Firefox/", $ua)){ 
      //header("content-disposition:attachment; filename*=\"utf8''".$this->exportName.'"'); 
	   header('content-disposition:attachment; filename="'.$this->exportName.'"'); 
    }else{ 
      header('content-disposition:attachment; filename="'.$this->exportName.'"'); 
    } 
    ob_end_flush(); 
    ob_implicit_flush(true); 
  } 
  
  
  private function formatCSV($data=array())//格式化为csv数据
  {  
    // 对数组每个元素进行转义 
    $data = array_map(array($this,'escape'), $data); 
    return $this->delimiter.implode($this->delimiter.$this->separator.$this->delimiter, $data).$this->delimiter."\r\n"; 
  } 
 
  private function escape($str) //转义字符串
  { 
    return str_replace($this->delimiter, $this->delimiter.$this->delimiter, $str); 
  } 
}  

 