<include file="admin@header" />
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-form">
        <form action="" method="post" id="form1">
            <input name="id" type="hidden" class="input-text" style="width:200px;"id="id" value="<{$info.id}>" />
            <table width="100%">
                <tr>
                    <th width="8%"><b>客户端ID:</b></th>
                    <td width="80%"><{$info.id}></td>
                </tr>
                <tr>
                    <th width="8%"><b>软件ID:</b></th>
                    <td width="80%"><{$info.sort_id}></td>
                </tr>
                <tr>
                    <th width="8%"><b>软件版本ID:</b></th>
                    <td width="80%"><{$info.sort_ver_id}></td>
                </tr>
                <tr>
                    <th width="8%"><b>用户ID:</b></th>
                    <td width="80%"><{$info.uid}></td>
                </tr>
                <tr>
                    <th width="8%"><b>Token:</b></th>
                    <td width="80%"><{$info.token}></td>
                </tr>
                <tr>
                    <th width="8%"><b>激活码:</b></th>
                    <td width="80%"><{$info.code}></td>
                </tr>

                <tr>
                    <th width="8%"><b>手机号:</b></th>
                    <td width="80%"><{$info.mobile}></td>
                </tr>
                <tr>
                    <th width="8%"><b>识别码:</b></th>
                    <td width="80%"><{$info.identification}></td>
                </tr>
                <tr>
                    <th width="8%"><b>公司名:</b></th>
                    <td width="80%"><input name="data[company]" type="text" class="input-text" style="width:200px;"id="company" value="<{$info.company}>" /></td>
                </tr>
                <tr>
                    <th width="8%"><b>分店号:</b></th>
                    <td width="80%"><input name="data[subbranch]" type="text" class="input-text" id="subbranch" value="<{$info.subbranch}>" /></td>
                </tr>
                <tr>
                    <th width="8%"><b>状态:</b></th>
                    <td width="80%"><input name="data[status]" type="radio" value="1"<if condition="$info.status eq 1"> checked="checked"</if>/> 
                        启用&nbsp;
                <input name="data[status]" type="radio" value="0" <if condition="$info.status eq 0"> checked="checked"</if> /> 
                禁用&nbsp;</td>
                </tr>
                 <tr>
                    <th width="8%"><b>安装次数:</b></th>
                    <td width="80%"><{$info.install_num}></td>
                </tr>
                <tr>
                    <th width="8%"><b>操作人:</b></th>
                    <td width="80%"><{$info.admin_id}></td>
                </tr>
                <if condition="$info.update_time neq '0'">
                    <tr>
                        <th width="8%"><b>修改时间:</b></th>
                        <td width="80%"><{$info.update_time|date='Y-m-d H:i:s',###}></td>
                    </tr>
                    <else />
                        <tr>
                            <th width="8%"><b>添加时间:</b></th>
                            <td width="80%"><{$info.add_time|date='Y-m-d H:i:s',###}></td>
                        </tr>
                </if>
            </table>
            <div class="btn">
                <input name="do" type="hidden" value="dosubmit" />
                <input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
            </div>
        </form>
    </div>
</div>

<include file="admin@footer" />