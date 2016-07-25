<?php
/**
 * Created by PhpStorm.
 * User: likun
 * Date: 15-4-13
 * Time: 下午2:01
 */

/**
 * Class controller_api_abstract
 * 本类是api目录下所有controller的基类
 */
class controller_api_abstract extends controller
{
    /**
     * api不需要index页面,把他重定向到其他地方
     */
    public function actionIndex()
    {
        echo 'index of api new...';
        #todo goto(www.index)
    }

}