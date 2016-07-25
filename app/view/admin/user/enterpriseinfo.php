
	<div class="table-form">
		<fieldset class="lan">
			<legend>会员企业认证详情</legend>
			<div class="table-form">
				<table width="100%" cellspacing="0" class="tab-box1">
					<tr>
						<th width="100"><strong>用户ID</strong></th>
						<td><{$indentity_info.uid}></td>
						<th width="80"><strong>企业名称</strong></th>
						<td><{$indentity_info.enterprise_name}></td>
						<th width="80"><strong>企业联系人</strong></th>
						<td><{$indentity_info.contact}></td>
                        <th width="80"><strong>类型</strong></th>
                        <td><{$indentity_info.type}></td>
					</tr>
                    <tr>
                        <th width="100"><strong>法人姓名</strong></th>
                        <td><{$indentity_info.legal}></td>
                        <th width="80"><strong>法人身份证号</strong></th>
                        <td><{$indentity_info.legal_id_no}></td>
                        <th width="80"><strong>开户银行许可证</strong></th>
                        <td><{$indentity_info.bank_license}></td>
                        <th width="80"><strong>组织机构代码</strong></th>
                        <td><{$indentity_info.org_no}></td>
                    </tr>
                    <tr>
                        <th width="100"><strong>营业执照编号</strong></th>
                        <td><{$indentity_info.business_license}></td>
                        <th width="80"><strong>税务登记号</strong></th>
                        <td><{$indentity_info.tax_no}></td>
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
						<th><strong>法人身份证(正反面复印在一页，加盖公章)</strong></th>
						<td colspan="7"><if condition="$indentity_info['id_photo']"><img src="<{$indentity_info.id_photo}>" width="300" />&nbsp;&nbsp;&nbsp;<a href="<?php echo url('user/downfile',array('filetype'=>'id_photo','id'=>$indentity_info['id']));?>" target="_blank"><strong>原件预览</strong></a><else />暂未上传</if>						  <strong>&nbsp;</strong><strong>&nbsp;</strong></td>
                    </tr>
					<tr>
					  <th><strong>营业执照副本复印件加盖公章</strong></th>
					  <td colspan="7"><if condition="$indentity_info['atta_yyzz']"><img src="<{$indentity_info.atta_yyzz}>" width="300" />&nbsp;&nbsp;&nbsp;<a href="<?php echo url('user/downfile',array('filetype'=>'atta_yyzz','id'=>$indentity_info['id']));?>" target="_blank"><strong>原件预览</strong></a><else />暂未上传</if></td>
				    </tr>
                    <tr>
                        <th><strong>税务登记证复印件加盖公章</strong></th>
                        <td colspan="7"><if condition="$indentity_info['atta_swdj']"><img src="<{$indentity_info.atta_swdj}>" width="300" />&nbsp;&nbsp;&nbsp;<a href="<?php echo url('user/downfile',array('filetype'=>'atta_swdj','id'=>$indentity_info['id']));?>" target="_blank"><strong>原件预览</strong></a><else />暂未上传</if></td>
                    </tr>
                    <tr>
                        <th><strong>组织机构代码证复印件加盖公章</strong></th>
                        <td colspan="7"><if condition="$indentity_info['atta_zzjg']"><img src="<{$indentity_info.atta_zzjg}>" width="300" />&nbsp;&nbsp;&nbsp;<a href="<?php echo url('user/downfile',array('filetype'=>'atta_zzjg','id'=>$indentity_info['id']));?>" target="_blank"><strong>原件预览</strong></a><else />暂未上传</if></td>
                    </tr>
                    <tr>
                        <th><strong>银行开户许可证复印件加盖公章</strong></th>
                        <td colspan="7"><if condition="$indentity_info['atta_yhkh']"><img src="<{$indentity_info.atta_yhkh}>" width="300" />&nbsp;&nbsp;&nbsp;<a href="<?php echo url('user/downfile',array('filetype'=>'atta_yhkh','id'=>$indentity_info['id']));?>" target="_blank"><strong>原件预览</strong></a><else />暂未上传</if></td>
                    </tr>
					
				</table>
			</div>
		</fieldset>
        <p style="margin-left: 10px">注：企业用户注册激活时，提供以下信息到p2pyy@lanmao.com</p>
        <div style="padding-left: 30px">
            <p>1、平台名称+商编（100124xxx）</p>
            <p>2、平台会员编号</p>
            <p>3、完整的企业名称（与注册的会员名称一致）</p>
            <p>4、注册类型</p>
            <p>5、【普通附件】企业5证的黑白复印件+红色公章</p>
            <p style="margin-left: 10px">a. 营业执照副本复印件加盖公章；</p>
            <p style="margin-left: 10px">b. 税务登记证复印件加盖公章；</p>
            <p style="margin-left: 10px">c. 组织机构代码证复印件加盖公章；</p>
            <p style="margin-left: 10px">d. 银行开户许可证复印件加盖公章；</p>
            <p style="margin-left: 10px">e.法人身份证正反面复印在一页，加盖公章。</p>
        </div>
	</div>