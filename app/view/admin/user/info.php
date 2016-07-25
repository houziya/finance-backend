<include file="admin@header" />
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
		<ul class="tabBut">
			<li class="on">会员企业认证信息</li>
			<li onclick="opendialog('<{:url('pay/useraccount?uid='.$info['uid'])}>','银行账户',0,'info','90%','90%');">银行账户</li>
			<li onclick="opendialog('<{:url('pay/accountdraw?uid='.$info['uid'])}>','提现记录',0,'info','90%','90%');">提现记录</li>
			<li onclick="opendialog('<{:url('pay/recharge?uid='.$info['uid'])}>','充值记录',0,'info','90%','90%');">充值记录</li>
			<li onclick="opendialog('<{:url('project/index?uid='.$info['uid'])}>','项目列表',0,'info','90%','90%');">项目列表</li>
			<li onclick="opendialog('<{:url('tender/index?uid='.$info['uid'])}>','投资列表',0,'info','90%','90%');">投资列表</li>
			<li onclick="opendialog('<{:url('credit/index?uid='.$info['uid'])}>','积分日志',0,'info','90%','90%');">积分日志</li>
			<li onclick="opendialog('<{:url('feedback/index?uid='.$info['uid'])}>','留言列表',0,'info','90%','90%');">留言列表</li>
			<li onclick="opendialog('<{:url('log/login?uid='.$info['uid'])}>','登录日志',0,'info','90%','90%');">登录日志</li>
			<li onclick="opendialog('<{:url('message/index?receive_uid='.$info['uid'])}>','站内信',0,'info','90%','90%');">站内信</li>
			<li onclick="opendialog('<{:url('log/index?uid='.$info['uid'])}>','操作日志',0,'info','90%','90%');">操作日志</li>
			<li onclick="opendialog('<{:url('user/useridentity?uid='.$info['uid'])}>','会员身份认证',0,'info','90%','90%');">身份认证</li>
            <li onclick="opendialog('<{:url('user/userenterprise?uid='.$info['uid'])}>','会员企业认证',0,'info','90%','90%');">企业身份认证</li>
			<li onclick="opendialog('<{:url('user/deliveryaddress?uid='.$info['uid'])}>','会员收货地址',0,'info','90%','90%');">收货地址</li>
			<li onclick="opendialog('<{:url('comment/index?value_id='.$info['uid'])}>/types/1','用户评论',0,'info','90%','90%');">评论</li>
			<li onclick="opendialog('<{:url('admincallback/usercallback?table_id='.$info['uid'])}>/types/1','会员回访',0,'info','90%','90%');">回访</li>
			<li onclick="opendialog('<{:url('user/authentication?uid='.$info['uid'])}>/types/1','高级认证',0,'info','90%','90%');">高级认证</li>
		</ul>
	
    <div class="table-form col-3 pad-10">
    	<include file="admin@user/commoninfo" />
    	<div class="btn">
			<a href="javascript:opendialog('<{:url('user/deliveryaddressadd?uid='.$info['uid'])}>','添加会员收货地址',0,'info','90%','90%');" class="button3">添加会员收货地址</a>
			<a href="javascript:opendialog('<{:url('admincallback/callbackadd?table_id='.$info['uid'].'&type=1')}>','添加会员回访',0,'info','90%','90%');" class="button3">添加会员回访</a>
		</div>
    	
  
		<div class="fl mar-r10" style="width:48%">
			<fieldset class="lan"><legend>资产统计</legend>
			<table width="100%">
			<?php if(!empty($property_info)):?>
				<tr>
					<th width="120"><strong>总金额</strong></th>
					<td><if condition="$property_info.total neq ''"><{$property_info.total}><else />0.00</if> <?php if($is_account_tips){ ?><span class="red">用户资金出错</span>
					<script>$(function(){alert('用户资金出错，请联系技术处理');});</script>
					<?php } ?></td>
				</tr>
				<tr>
					<th><strong>可用余额</strong></th>
					<td><if condition="$property_info.amount neq ''"><{$property_info.amount}><else />0.00</if></td>
				</tr>
				<tr>
					<th><strong>用户账户可用余额</strong></th>
					<td><if condition="$property_info.user_amount neq ''"><{$property_info.user_amount}><else />0.00</if></td>
				</tr>
				<tr>
					<th><strong>项目账户可用余额</strong></th>
					<td><if condition="$property_info.project_amount neq ''"><{$property_info.project_amount}><else />0.00</if></td>
				</tr>
				<tr>
					<th><strong>冻结金额</strong></th>
					<td><if condition="$property_info.freeze_amount neq ''"><{$property_info.freeze_amount}><else />0.00</if></td>
				</tr>
				<tr>
					<th><strong>正在投资中</strong></th>
					<td><{$property_info.investment_total}>&nbsp;</td>
				</tr>
				<tr>
					<th><strong>投资总金额</strong></th>
					<td><{$property_info.investment_amount}>&nbsp;</td>
				</tr>
				<tr>
					<th><strong>项目资金申请中</strong></th>
					<td><{$property_info.drawmoney_amount}>&nbsp;</td>
				</tr>
				<tr>
					<th><strong>累计提现</strong></th>
					<td><{$property_info.drawmoney_total}>&nbsp;</td>
				</tr>
				<tr>
					<th><strong>充值总金额</strong></th>
					<td><{$property_info.recharge_amount}>&nbsp;</td>
				</tr>
			<?php else:?>
			<?php endif;?>
			</table>

			</fieldset>
		</div>

		<div class="col-auto">
			<fieldset class="lan"><legend>项目统计</legend>
				<table width="100%">
					<tr>
						<td align="center"><strong>全部</strong></td>
						<td align="center"><strong>草稿（未审核）</strong></td>
						<td align="center"><strong>待审核</strong></td>
						<td align="center"><strong>未通过</strong></td>
						<td align="center"><strong>预热中</strong></td>
						<td align="center"><strong>融资中</strong></td>
						<td align="center"><strong>融资失败</strong></td>
						<td align="center"><strong>融资成功</strong></td>
					</tr>
					<tr>
						<td align="center"><if condition="$investmentPronums.zs_nums neq '' and $investmentPronums.zs_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>','全部项目',0,'info','90%','90%');" title="全部项目" alt="全部项目"><{$investmentPronums.zs_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.ws_nums neq '' and $investmentPronums.ws_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/0','草稿（未审核）',0,'info','90%','90%');"  title="草稿（未审核）" alt="草稿（未审核）"><{$investmentPronums.ws_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.ds_nums neq '' and $investmentPronums.ds_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/1','待审核',0,'info','90%','90%');"  title="待审核" alt="待审核"><{$investmentPronums.ds_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.wg_nums neq '' and $investmentPronums.wg_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/-1','未通过',0,'info','90%','90%');"  title="未通过" alt="未通过"><{$investmentPronums.wg_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.yr_nums neq '' and $investmentPronums.yr_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/2','预热',0,'info','90%','90%');"  title="预热" alt="预热"><{$investmentPronums.yr_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.rz_nums neq '' and $investmentPronums.rz_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/4','融资中',0,'info','90%','90%');"  title="融资中" alt="融资中"><{$investmentPronums.rz_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.sb_nums neq '' and $investmentPronums.sb_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/5','融资失败',0,'info','90%','90%');"  title="融资失败" alt="融资失败"><{$investmentPronums.sb_nums}></a><else />0</if></td>
						<td align="center"><if condition="$investmentPronums.wc_nums neq '' and $investmentPronums.wc_nums neq '0' "><a href="javascript:opendialog('<{:url('project/index?uid='.$info[uid])}>/status/6','融资成功',0,'info','90%','90%');"  title="融资成功" alt="融资成功"><{$investmentPronums.wc_nums}></a><else />0</if></td>
					</tr>
				</table>
			</fieldset>
		</div>
		
		<?php if(!empty($yeepay_account)){ ?>
		<div class="col-auto">
			<fieldset class="lan"><legend>易宝账户</legend>
				<table width="100%">
				<tr>
					<th width="120"><strong>易宝总余额</strong></th>
					<td><{$yeepay_account.total}></td>
				</tr>
				<tr>
					<th><strong>易宝可提现余额</strong></th>
					<td><{$yeepay_account.amount}></td>
				</tr>
				<tr>
					<th><strong>易宝冻结金额</strong></th>
					<td><{$yeepay_account.freeze}></td>
				</tr>
				</table>
			</fieldset>
		</div>
		<?php } ?>
		
	</div>
	<div class="bk10"></div>
	<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
</div>

<include file="admin@footer" />