<include file="admin@header" />
<script type="text/javascript">
    //得到多选框ID集合
    function get_ids() {
        var ids = '';
        $("input[name='ids[]']").each(function() {
            if($(this).attr('checked')=='checked') {
                ids += '|'+$(this).val();
            };
        });
        if(ids!=''){
            return ids.substring(1);
        }else{
            return '';
        }
    }
    //批量设置
    function setStatus(v){
        var ids = get_ids();
        if(ids==''){
            alert('请选择待设置的id');
            return false;
        }
        $("input[name='status']").val(v);
        $('#myform').attr('action','<{:url("setStatus")}>');
        $('#myform').submit();
    };
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            客户端id：
            <input name="search[id]" type="text" class="input-text" value="<{$id}>" size="3" />
            手机号：
            <input name="search[mobile]" type="text" class="input-text" value="<{$mobile}>" size="15" />
            激活码：
            <input name="search[code]" type="text" class="input-text" value="<{$code}>" size="32" />
            识别码：
            <input name="search[identification]" type="text" class="input-text" value="<{$identification}>" size="15" />
            分店名：
            <input name="search[company]" type="text" class="input-text" value="<{$company}>" size="20" />
            分店号：
            <input name="search[subbranch]" type="text" class="input-text" value="<{$subbranch}>" size="3" />
            添加时间：<{$input_add_time}>
            更新时间：<{$input_update_time}>
            状态：
            <{$status_select}>
            客户端离线在线状态
            <{$online_status_select}>
            &nbsp;	
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">ID</th>
                    <th width="10%">激活码</th>
                    <th width="8%">手机号</th>
                    <th width="10%">识别码</th>
                    <th width="16%">分店名</th>
                    <th width="5%">分店号</th>
                    <th width="5%">状态</th>
                    <th width="10%">离线/在线状态</th>
                    <th width="11%">添加时间/更新时间</th>
                    <th width="8%">操作</th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                    <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                    <td><{$v.id}></td>
                    <td><{$v.code}></td>
                    <td><{$v.mobile}></td>
                    <td><{$v.identification}></td>
                    <td><{$v.company}></td>
                    <td><{$v.subbranch}></td>
                    <td><{$v.status_tips}></td>
                    <td><{$v.online_status_tips}></td>
                    <td><{$v.add_time|date='Y-m-d H:i:s',###}><br/><{$v.update_time|date='Y-m-d H:i:s',###}></td>
                    <td>
                      <a href="<{:url('edit?id='.$v['id'])}>">编辑</a>
                      <a href="<{:url('queryConfig?id='.$v['id'].'&menu_id=39')}>">查询配置</a>
                      <a href="<{:url('statistics/ClientStatus?id='.$v['id'])}>">客户端状态报表</a>
                    </td>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
               <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="关闭" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />