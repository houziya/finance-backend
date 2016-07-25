<?php

/**
 * 节日、节气、农阳历互换
 * @author  liurengang
 * @date    2015.09.10
 * 
 * */
class model_user_holiday extends model_abstruct {

	//如有需要，可以在此添加新的节假日   
	private $holiday_arr = array(
		'20150927' => array('name' => '中秋节', 'content' => '尊敬的投资人，您好。值此中秋佳节到来之际，人人投平台祝您家庭幸福美满，事业更加有成，平安健康守候身边!'),
		'20160208' => array('name' => '春节', 'content' => '尊敬的投资人，人人投祝您新年快乐！万事如意！'),
	);
	
	private $birthday = '人人投祝您生日快乐，愿您天天快乐，年年好运，一生幸福！';

	/**
	 * 每日计划任务 8 点执行
	 * 每逢节假日、如元宵节、清明节、劳动节、端午节、中秋节、国庆节、圣诞节、元旦、春节等给人人投平台投资人发短信祝福语
	 * (9.23 liufei editor)
	 * @author  liurengang
	 * @date    2015.09.10
	 * @return
	 */
	public function HolidaySmsMessage() {
		$date = date('Ymd');
		
		$cachename = "holidaysmsmessage_{$date}";
		if(G($cachename)) {
			echo "{$date} 一天只让执行一次\n";
			return false;
		}
		
		foreach ($this->holiday_arr as $key => $param) {
			if ($key == $date) {
				$count = M('projectInvestment')->getField("count(distinct(uid))");
				$n = 1000;
				$mobile_arr = array();
				for ($i = 0; $i < $count; $i = $i + $n) {
					$investInfo = M('projectInvestment')->field("uid")->group('uid')->limit($i, $n)->findAll();
					if (!empty($investInfo)) {
						foreach ($investInfo as $key => &$val) {
							$mobile = D('user')->getInfo($val['uid'], 'mobile');
							if (!helper_tool::checkMobile($mobile)) {
								continue;
							}
							$mobile_arr[$mobile] = $mobile;
						}
						unset($val);
					}
				}
				//执行群发短信
				if ($mobile_arr && $param['content']) {
					model_remind::sendMobileMass($mobile_arr, $param['content'], 0, 'admin_remind_custom');
					echo date('Y-m-d H:i:s') . " | 发送节日短信 " . count($mobile_arr) . " 条 | {$param['content']}\n\n";
				}			
			}else{
				echo date('Y-m-d H:i:s') . " | 没有待发送的{$param['name']}短信 \n";
			}
		}
		G($cachename,1,172800);
	}

	/**
	 * 发送生日祝福，每日计划任务 8 点执行
	 * (9.23 liufei editor)
	 * @author  liurengang
	 * @date    2015.09.10
	 * @return
	 */
	public function BirthdaySmsMessage() {
		$date = date('Ymd');
		
		$cachename = "birthdaysmsmessage_{$date}";
		if(G($cachename)) {
			echo "{$date} 一天只让执行一次\n";
			return false;
		}
		
		$day = date('md');
		$sql = "SELECT a.uid,b.mobile FROM user_body a 
				JOIN (SELECT y.uid,y.mobile 
						FROM project_investment x,`user` y
						WHERE x.uid = y.uid 
						GROUP BY x.uid) b 
				ON a.uid = b.uid 
				WHERE a.u_body_num REGEXP '^[0-9]{10}{$day}[0-9x]{4}$'";

		$rows = M()->query($sql);
		$mobile_arr = array();
		foreach ($rows as $val) {
			if (!helper_tool::checkMobile($val['mobile'])) {
				continue;
			}
			$mobile_arr[$val['mobile']] = $val['mobile'];
		}

		//执行群发短信
		if ($mobile_arr && $this->birthday) {
			model_remind::sendMobileMass($mobile_arr, $this->birthday, 0, 'admin_remind_custom');
			echo date('Y-m-d H:i:s') . " | 发送生日短信 " . count($mobile_arr) . " 条 | {$this->birthday}\n\n";
		}else{
			echo date('Y-m-d H:i:s') . " | 没有过生日的投资人！ \n";
		}
		G($cachename,1,172800);
	}

}
