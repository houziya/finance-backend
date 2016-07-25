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
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            周期类型:
            <select name="search[type]" id="stat-cycel">
                <foreach from="stat_cycel" key="k" item="v">
                    <option value="<{$k}>" <?= $k == $type ? "selected" : ""?>><{$v}></option>
                </foreach>
            </select>
            手机号：
            <input name="search[mobile]" type="text" class="input-text" value="<{$mobile}>" size="16" />
            店名：
            <input name="search[subbranch]" type="text" class="input-text" value="<{$subbranch}>" size="16" />
            开始日期：
            <?= helper_form::date("search[start_time]") ?>
            结束日期：
            <?= helper_form::date("search[end_time]") ?>
            &nbsp;
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">软件</th>
                    <th width="4%">版本</th>
                    <th width="4%">店名</th>
                    <th width="6%">手机号</th>
                    <th width="4%">消费金额</th>
                    <th width="10%">时间</th>
                    <th width="4%">抓取次数</th>
                    <th width="4%">详情</th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                        <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                        <td><{$v.name}></td>
                        <td><{$v.version}></td>
                        <td><{$v.subbranch}></td>
                        <td><{$v.mobile}></td>
                        <td><{$v.amount}></td>
                        <td><{$v.topdate}></td>
                        <td><{$v.degreen}></td>
                        <td><a href="<{:url('collectDetail?sort_client_id='.$v['sort_client_id'].'&date='.$v['topdate'])}>">查看详情</a> | <a href="<{:url('expenseStat?sort_client_id='.$v['sort_client_id'].'&name='.$v['name'].'&type='.($type|0))}>">查看报表</a></td>
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