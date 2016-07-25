<?php
/**
 * User: wangmengmeng
 * Date: 2016/7/4
 * Time: 10:04
 */
class Common{
    /**
     * 活取request数据
     * @return bool
     */
    private function getRequestData() {
        //$info = $this->_post();
        $info = file_get_contents("php://input");
        //$rs = file_put_contents(DATA_PATH.'/log/1.txt', $info, FILE_APPEND);
        $info = json_decode(trim($info), true);
        return $this->decodeRequest($info);
    }

    /**
     * 发送response信息
     */
    public function sendResponse() {
        header('Content-Type:application/json; charset=utf-8');
        echo $this->encodeResponse();
    }

    /**
     * 解析并验证request数据
     * @param $info
     * @return bool
     */
    private function decodeRequest($info) {
        //签名检测
        $this->request = $info;
        return true;
    }

    /**
     * 设置 response body data
     * @param $dat
     */
    private function setResponse($dat) {
        if ($dat) {
            $this->response['data'] = $dat;
        }
    }

    /**
     * 编码并加入验证信息
     */
    private function encodeResponse() {
        return urldecode(json_encode(urlEncodeJson($this->response)));
    }

    /**
     * 检查签名认证
     * @param $params 待签名的数组,
     * @author tianxiang
     * @return 1 签名通过，-1签名不通过
     */
    private function signVerify($params) {
        $typeId = $params['typeId'];
        $row = D('finance/softwareClientOparation')->getInfoById($typeId);

        $fieldA = "," . $row['field'];
        $fieldArr = explode(",", $fieldA);
        array_shift($fieldArr);
        $fieldstr = "";
        if (!empty($fieldArr)) {
            foreach ($fieldArr as $field) {
                $fieldstr = $fieldstr . "&" . $params[$field];
            }
        }
        $field = substr($fieldstr, 1);
        $sign = $params['sign'];
        $key = $this->signKey; //用signKey做签名秘钥
        $sequence = $key . "&" . $field; //待签名字符串
        //签名验证
        if (!empty($sequence)) {
            $wsign = md5($sequence); //加密
            if ($sign != $wsign) { //签名检测
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
?>