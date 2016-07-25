<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 9:40
 */
class controller_admin_test extends controller_admin_abstract{
    public function actionWmm() {
        try {
            //phpinfo();
            $dbName = "sqlsrv:Server=192.168.1.127,1433;Database=yszt001";
            $dbUser = "sa";
            $dbPassword = "123456";
            $db = new PDO($dbName, $dbUser, $dbPassword);
            if ($db)
            {
                echo "database connect succeed.<br />";
                $num = rand(100,200);
                $no =  date("ymd");
                $j=0;
                for($i=1;$i<=$num;$i++){
                    $noId = $no.sprintf("%05d", $i);
                    $money = rand(100,2000);
                    $number = "XS-" . date('Y-m-d') . "-" . rand(1,2000);
                    $time = date("Y-m-d")." ".sprintf("%02d",rand(9,18)).":".sprintf("%02d",rand(1,59)).":".sprintf("%02d",rand(1,59));
                    $sql = "INSERT INTO dbo.Dlyndx (Number, Total, VchType, btypeid, etypeid, ktypeid, Date) VALUES ('" . $number . "', " .$money. ", 11, '0000600002', '0000600002', '0000600002', '" . date('Y-m-d')  . "')";
                    $results = $db->exec($sql);
                    if(!empty($results)){
                        $j++;
                    }
                }
                echo "共插入".$j."单";
                exit;
                $row = $db->query("select * from dbo.Dlyndx");
                foreach($db->query("select * from dbo.Dlyndx") as $row){
                    print_r($row);
                }
            }
        }catch (PDOException $e)
        {
            $content = $e->getMessage();
            echo   $content . "<br />";
            echo "Hello World!";
        }
    }
}
?>