<?php
/*
xml操作类
echo ArrayToXml::parse($array, 'root');
*/
class Helper_xml{
    //文档对象
    private static $doc = NULL;
    //版本号
    private static $version = '1.0';

    /*
     *	把xml转成数组 object
     */
    public static function xml_to_array( $xml_string){
        $xmlObj = simplexml_load_string($xml_string);
        $arrXml = self::objects_to_array($xmlObj);
        return $arrXml;
    }
    /*
     *	把xml转成数组 json
     */
    public static function xml_to_array2( $xml_string){
        $xmlObj = simplexml_load_string($xml_string);
        $data = json_decode(json_encode($xmlObj),TRUE);
        return $data;
    }
    /*
     *	把对象转成数组
     */
    public static function objects_to_array($arrObjData, $arrSkipIndices = array())
    {
        $arrData = array();
        // if input is object, convert into array
        if (is_object($arrObjData)) {
                        $arrObjData = get_object_vars($arrObjData);
        }

        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {

                if (is_object($value) || is_array($value)) {
                                $value = self::objects_to_array($value, $arrSkipIndices); // recursive call
                }
                if (in_array($index, $arrSkipIndices)) {
                                continue;
                }
                $arrData[$index] = $value;

            }
        }
        return $arrData;
    }
     
    /**
     * 初始化文档版本及编码
     *
     * @param string $version   版本号
     * @param string $encoding  XML编码
     */
    public static function init($version, $encoding) {
        self::$doc = new DomDocument($version, $encoding);
        self::$doc->formatOutput = true;
    }
     
    /**
     * 转换数组到XML
     *
     * @param array $array      要转换的数组
     * @param string $rootName  要节点名称
     * @param string $version   版本号
     * @param string $encoding  XML编码
     *
     * @return string
     */
    public static function array_to_xml($array, $rootName = 'root', $version = '1.0', $encoding = 'UTF-8') {
        self::init($version, $encoding);
        //转换
        $node = self::convert($array, $rootName);
        self::$doc->appendChild($node);
         return self::$doc->saveXML();
    }
    //数组第一个值做属性
    public static function array_to_xml2($array, $rootName = 'root', $version = '1.0', $encoding = 'UTF-8') {
        list($key,$value)=each($array);
        $array = array_slice($array, 1);
        self::init($version, $encoding);
        //转换
        $node = self::convert($array, $rootName);
        self::$doc->appendChild($node);

        $node->setAttribute($key,$value);
        return self::$doc->saveXML();
    }
     
    /**
     * 递归转换
     *
     * @param array $array      数组
     * @param string $nodeName  节点名称
     *
     * @return object (DOMElement)
     */
    private static function convert($array, $nodeName) {
        if (!is_array($array)) return false;         
        //创建父节点
        $node = self::create_node($nodeName);
        //循环数组
        foreach ($array as $key => $value) {
            $element = self::create_node($key);
            //如果不是数组，则创建节点的值
            if (!is_array($value)) {
                $element->appendChild(self::create_value($value));
                $node->appendChild($element);
            } else {
                //如果是数组，则递归
                $node->appendChild(self::convert($value, $key, $element));
            }                      
        }
        return $node;
    }
     
    private static function create_node($name) {
        $node = NULL;
        //如果是字符串，则创建节点
        if (!is_numeric($name)) {
            $node = self::$doc->createElement($name);
        } else {
            //如果是数字，则创建默认item节点
            $node = self::$doc->createElement('item');
        }
         
        return $node;
    }
     
    /**
     * 创建文本节点
     *
     * @param string || bool || integer $value
     *
     * @return object (DOMText || DOMCDATASection );
     */
    private static function create_value($value) {
        $textNode = NULL;
         
        //如果是bool型，则转换为字符串
        if (true === $value || false === $value) {
            $textNode = self::$doc->createTextNode($value ? 'true' : 'false');
        } else {
            //如果含有HTML标签，则创建CDATA节点
            if (strpos($value, '<') > -1) {
                $textNode = self::$doc->createCDATASection($value);
            } else {
                $textNode = self::$doc->createTextNode($value);
            }
        }
         return $textNode;
    }
}

