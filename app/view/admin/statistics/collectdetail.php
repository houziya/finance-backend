<?php
/**
 * 消费统计视图
 * User: wangmengmeng
 * Date: 2016/7/6
 * Time: 10:47
 */
?>
<include file="admin@header" />
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <!--<div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            软件名称：
            <input name="name" type="text" class="input-text" value="" size="10" />
            手机号：
            <input name="mobile" type="text" class="input-text" value="" size="10" />
            客户端：
            <input name="company" type="text" class="input-text" value="" size="10" />
            &nbsp;
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>-->
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">软件</th>
                    <th width="4%">版本</th>
                    <th width="4%">客户端</th>
                    <th width="4%">手机号</th>
                    <th width="10%">消费金额</th>
                    <th width="10%">时间</th>
                    <th width="10%">详情</th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                        <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                        <td><{$v.sort_id}></td>
                        <td><{$v.sort_ver_id}></td>
                        <td><{$v.company}></td>
                        <td><{$v.mobile}></td>
                        <td><{$v.amount}></td>
                        <td><{$v.start_time}>-<{$v.end_time}></td>
                        <td><a href="<{:url('detail?id='.$v['id'])}>">查看详情</a></td>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
                <!--<input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="关闭" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />-->
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>
<include file="admin@footer" />