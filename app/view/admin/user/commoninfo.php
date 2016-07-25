
	<div class="table-form">
		<fieldset class="lan">
			<legend>会员详情</legend>
			<div class="table-form">
				<table width="100%" cellspacing="0" class="tab-box1">
					<tr>
						<th width="80"><strong>UID/用户名</strong></th>
						<td><{$info.uid}> | <{$info.username}></td>
						<th width="80"><strong>邮箱</strong></th>
						<td><{$info.email}></td>
						<th width="80"><strong>手机号</strong></th>
						<td><{$info.mobile}></td>
						<th width="80"><strong>真实姓名</strong></th>
						<td><{$info.realname}></td>
					</tr>
					<tr>
						<th><strong>粉丝数量</strong></th>
						<td><{$info.fans_num}></td>
						<th><strong>关注数量</strong></th>
						<td><{$info.focus_num}></td>
						<th><strong>约谈数量</strong></th>
						<td><{$info.question_num}></td>
						<th><strong>被评价数量</strong></th>
						<td><{$info.reviews_num}></td>
					</tr>
					<tr>
						<th><strong>未读私信条数</strong></th>
						<td><{$info.newpm}></td>
						<th><strong>投资数量</strong></th>
						<td><{$info.investor_num}></td>
						<th><strong>投资星级</strong></th>
						<td><{$info.investment_start}></td>
						<th><strong>回访内容</strong></th>
						<td><{$info.callback_content}></td>
					</tr>
					<tr>
						<th><strong>执行回访的用户名</strong></th>
						<td><{$info.callback_username}></td>
						<th><strong>回访时间</strong></th>
						<td><{:date('Y-m-d H:i:s',$info['callback_time'])}></td>
						<th><strong>会员等级</strong></th>
						<td><{$info.user_level}></td>
						<th><strong>登录次数</strong></th>
						<td><{$info.login_num}></td>
					</tr>
					<tr>
						<th><strong>性别</strong></th>
						<td><if condition="$info['sex'] neq '' && $info['sex'] eq 1">男<else />女</if></td>
						<th><strong> 用户头像 </strong></th>
						<td><if condition="$info['face']"><img src="<{$url.img}><{$info.face}>" width="150"  height="150"/></if></td>
						<th><strong>是否推荐人</strong></th>
						<td><if condition="$info['is_recommend'] neq '' && $info['is_recommend'] eq 1">是<else />否</if></td>
						<th><strong>实名认证状态</strong></th>
						<td>
						<if condition="$info['is_idcard'] neq '' && $info['is_idcard'] eq 0">待审
						<elseif condition="$info['is_idcard'] neq '' && $info['is_idcard'] eq 1"  />审核中
						<elseif condition="$info['is_idcard'] neq '' && $info['is_idcard'] eq 2"  />审核通过
						<elseif condition="$info['is_idcard'] neq '' && $info['is_idcard'] eq 3"  />易宝审核通过
						<elseif condition="$info['is_idcard'] neq '' && $info['is_idcard'] eq -1" />审核失败
						<elseif condition="$info['is_idcard'] neq '' && $info['is_idcard'] eq -2" />易宝审核失败
						</if>
						</td>
					</tr>
					<tr>
						<th><strong>回访状态</strong></th>
						<td>
						<if condition="$info['callback_status'] neq '' && $info['callback_status'] eq 0">未回访
						<elseif condition="$info['callback_status'] neq '' && $info['callback_status'] eq 1" />已回访
						<elseif condition="$info['callback_status'] neq '' && $info['callback_status'] eq 2"/>已回访并特殊标记
						<elseif condition="$info['callback_status'] neq '' && $info['callback_status'] eq 3"/>需要再次回访
						</if>
						</td>
						<th><strong>是否管理员</strong></th>
						<td><if condition="$info['is_admin'] neq '' && $info['is_admin'] eq 1">是
						<elseif condition="$info['is_admin'] neq '' && $info['is_admin'] eq 0" />否</if></td>
						<th><strong>公司认证状态</strong></th>
						<td><if condition="$info['is_company_check'] neq '' && $info['is_company_check'] eq 0">待审
						<elseif condition="$info['is_company_check'] neq '' && $info['is_company_check'] eq 1" />审核中</td>
						<elseif condition="$info['is_company_check'] neq '' && $info['is_company_check'] eq 2" />审核通过</td>
						<elseif condition="$info['is_company_check'] neq '' && $info['is_company_check'] eq -1" />审核失败</if></td>
						<th><strong>用户类型</strong></th>
						<td><if condition="$info['type'] neq '' && $info['type'] eq 0">投资方
						<elseif condition="$info['type'] neq '' && $info['type'] eq 1" />项目方</if></td>
					</tr>
					<tr>
						<th><strong>审核状态</strong></th>
						<td><if condition="$info['status'] neq '' && $info['status'] eq 0">待审核
						<elseif condition="$info['status'] neq '' && $info['status'] eq 1" />正常
						<elseif condition="$info['status'] neq '' && $info['status'] eq -1" />已删除</if></td>
						<th><strong>注册ip</strong></th>
						<td><{$info['regist_ip']}></td>
						<th><strong>最后登录ip</strong></th>
						<td><{$info['last_login_ip']}></td>
						<th><strong>注册时间</strong></th>
						<td><{:date('Y-m-d H:i:s',$info['regist_time'])}></td>
					</tr>
					<tr>
						<th><strong>最后登录时间</strong></th>
						<td><{:date('Y-m-d H:i:s',$info['last_login_time'])}></td>
						<th><strong>注册来源</strong></th>
						<td><?php if($info['source'] == 1):?>后台添加<?php elseif($info['source'] ==2):?>web<?php elseif($info['source'] ==3):?>wap<?php elseif($info['source'] ==4):?>ios<?php elseif($info['source'] ==5):?>android<?php else:?>web<?php endif;?></td>
						<th><strong>自动授权</strong></th>
						<td><if condition="$info['is_auto_yeepay'] eq 1">是
						<elseif condition="$info['is_auto_yeepay'] eq 0" />否</if></td>
						<th><strong>&nbsp;</strong></th>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</fieldset>		
	</div>