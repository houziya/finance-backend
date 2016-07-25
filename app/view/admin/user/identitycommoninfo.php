
	<div class="table-form">
		<fieldset class="lan">
			<legend>会员详情</legend>
			<div class="table-form">
				<table width="100%" cellspacing="0" class="tab-box1">
					<tr>
						<th width="100"><strong>用户ID</strong></th>
						<td><{$indentity_info.uid}></td>
						<th width="80"><strong>真实姓名</strong></th>
						<td><{$indentity_info.realname}></td>
						<th width="80"><strong>身份证号</strong></th>
						<td><{$indentity_info.u_body_num}></td>
						<th width="80"><strong>IP</strong></th>
						<td><{$indentity_info.ip}></td>
					</tr>
					<tr>						
						<th><strong>添加时间</strong></th>
						<td><{:date('Y-m-d H:i:s',$indentity_info['add_time'])}></td>
						<th><strong>审核时间</strong></th>
						<td><{:date('Y-m-d H:i:s',$indentity_info['check_time'])}></td>
						<th><strong>来源</strong></th>
						<td><{:date('Y-m-d H:i:s',$indentity_info['source'])}></td>
                        <th>&nbsp;</th>
						<td></td>
					</tr>
					<tr>
						<th><strong>身份证照片</strong></th>
						<td colspan="7"><if condition="$indentity_info['u_body_photo']"><img src="<{$indentity_info.u_body_photo}>" width="300" /><else />暂未上传</if>						  <strong>&nbsp;</strong><strong>&nbsp;</strong></td>
					</tr>
					<tr>
					  <th><strong>身份证照片反面</strong></th>
					  <td colspan="7"><if condition="$indentity_info['u_body_photof']"><img src="<{$indentity_info.u_body_photof}>" width="300" /><else />暂未上传</if></td>
				  </tr>
					
				</table>
			</div>
		</fieldset>		
	</div>