<include file="admin@header" />
<script type="text/javascript">
$(function(){
//	$.formValidator.initConfig({formid:"form1", validatorgroup:"form1", autotip:true, submitonce:true});
//	$("#company_status_remark").formValidator({validatorgroup:"form1",onshow:"请输入审核内容",onfocus:"请输入审核内容",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"审核内容不能为空"});
//
//    $.formValidator.initConfig({formid:"form2", validatorgroup:"form2",  autotip:true, submitonce:true});
//    $("#person_status_remark").formValidator({validatorgroup:"form2",onshow:"请输入审核内容",onfocus:"请输入审核内容",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"审核内容不能为空"});
})
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	
	<!-- <include file="admin@user/identitycommoninfo" /> -->
	<div class="table-form">
        <fieldset class="lan">
            <legend>机构领投信息</legend>
            <div class="table-form">
                <table width="100%" cellspacing="0" class="tab-box1">
                    <tr>
                        <th width="50"><strong>公司名称</strong></th>
                        <td><{$company_name}></td>
                        <th width="50"><strong>营业执照编号</strong></th>
                        <td ><{$company_business_licence}></td>
                    </tr>
                    <tr>
                        <th width="50"><strong>公司简介</strong></th>
                        <td><{$company_info}></td>
                        <th width="50"><strong>添加时间</strong></th>
                        <td><{:date('Y-m-d H:i:s',$add_time)}></td>
                    </tr>
                </table>
                <br />
                <table width="100%" cellspacing="0" class="tab-box1">
                    <tr>
                        <th width="100" height="80"><strong>公司logo</strong></th>
                        <td>
                            <if condition="$company_logo">
                                <img src="<?php echo ($url["img3"]); ?><{$company_logo}>" width="60" />
                                <else />
                                暂未上传
                            </if>
                        </td>
                        <th width="100"><strong>公司营业执照</strong></th>
                        <td>
                            <if condition="$company_business_licence_img">
                                <img src="<?php echo ($url["img3"]); ?><{$company_business_licence_img}>" width="60" />
                                <else />
                                暂未上传
                            </if>
                        </td>
                    </tr>
                </table>
                <br />
            </div>
        </fieldset>
        <div class="table-form">
            <form name="form1" action="_SELF_" method="post" id="form1">
                <fieldset class="mar-b10">
                    <legend>审核处理</legend>
                    <table width="100%">
                        <!--<tr>
                            <th>机构领投身份审核备注</th>
                            <td><textarea rows="10" name="company_status_remark" style="width:80%;height:60px;" id="company_status_remark"><if condition="$company_status_remark neq ''"><{$company_status_remark}></if></textarea></td>
                        </tr>-->
                        <tr>
                            <th>机构领投身份审核</th>
                            <td><label><input type="radio" name="company_status" value="-1" <{:radio($company_status,-1)}>/>
                                    审核不通过</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input name="company_status" type="radio" value="0"  <{:radio($company_status,0)}>/>
                                    未审核
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input name="company_status" type="radio" value="1"  <{:radio($company_status,1)}>/>
                                    待审核
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="radio" name="company_status" value="2"  <{:radio($company_status,2)}>/>
                                    审核通过</label>&nbsp;&nbsp;&nbsp;&nbsp;

                            </td>
                        </tr>
                        <tr>
                            <th>机构领投身份是否显示</th>
                            <td><label><input type="radio" name="company_is_show" value="1" <{:radio($company_is_show,1)}>/>
                                    显示</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input name="company_is_show" type="radio" value="0"  <{:radio($company_is_show,0)}>/>
                                    隐藏
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    </table>
                    <div class="btn">
                        <input type="hidden" name="id" id="comid" value="<{$id}>" />
                        <input type="submit" class="button" name="verify" value="确定提交" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="table-form">
        <fieldset class="lan">
            <legend>明星领投信息</legend>
            <div class="table-form">
                <table width="100%" cellspacing="0" class="tab-box1">
                    <tr>
                        <th width="100"><strong>用户ID</strong></th>
                        <td><{$uid}></td>
                        <th width="80"><strong>手机</strong></th>
                        <td><{$person_mobile}></td>
                        <th><strong>添加时间</strong></th>
                        <td><{:date('Y-m-d H:i:s',$add_time)}></td>
                    </tr>
                    <tr>
                        <th width="80"><strong>真实姓名</strong></th>
                        <td><{$person_name}></td>
                        <th width="80"><strong>身份证号</strong></th>
                        <td><{$person_cardid}></td>
                        <!--<th width="80"><strong>真实姓名</strong></th>
                        <td><{$person_name}></td>-->
                        <!-- <th><strong>个人照片</strong></th>
						<td colspan="7"><if condition="$person_photo"><img src="<?php echo ($url["img3"]); ?><{$person_photo}>" width="30" /><else />暂未上传</if>						  <strong>&nbsp;</strong><strong>&nbsp;</strong></td> -->
                    </tr>
                </table>
                <br />
                <table width="100%" cellspacing="0" class="tab-box1">
                    <tr>
                        <th width="100"><strong>个人照片</strong></th>
                        <td>
                            <if condition="$person_photo">
                                <img src="<?php echo ($url["img3"]); ?><{$person_photo}>" width="60" />
                                <else />
                                暂未上传
                            </if>
                        </td>
                        <th width="100"><strong>个人资产证明</strong></th>
                        <td>
                            <if condition="$person_asset">
                                <a href="<{$person_asset}>">下载查看</a>
                                <else />
                                暂未上传
                            </if>
                        </td>
                        <th width="100"><strong>个人征信报告</strong></th>
                        <td>
                            <if condition="$person_credit">
                                <a href="<{$person_credit}>">下载查看</a>
                                <else />
                                暂未上传
                            </if>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <div class="table-form">
            <form name="form2" action="_SELF_" method="post" id="form2">
                <fieldset class="mar-b10">
                    <legend>审核处理</legend>
                    <table width="100%">

                        <!--<tr>
                            <th>明星领投身份审核备注</th>
                            <td><textarea rows="10" name="person_status_remark" style="width:80%;height:60px;" id="person_status_remark"><if condition="$person_status_remark neq ''"><{$person_status_remark}></if></textarea></td>
                        </tr>-->
                        <tr>
                            <th>明星领投身份审核</th>
                            <td><label><input type="radio" name="person_status" value="-1" <{:radio($person_status,-1)}>/>
                                    审核不通过</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input name="person_status" type="radio" value="0"  <{:radio($person_status,0)}>/>
                                    未审核
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input name="person_status" type="radio" value="1"  <{:radio($person_status,1)}>/>
                                    待审核
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="radio" name="person_status" value="2"  <{:radio($person_status,2)}>/>
                                    审核通过</label>&nbsp;&nbsp;&nbsp;&nbsp;

                            </td>
                        </tr>
                        <tr>
                            <th>明星领投身份是否显示</th>
                            <td><label><input type="radio" name="person_is_show" value="1" <{:radio($person_is_show,1)}>/>
                                    显示</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input name="person_is_show" type="radio" value="0"  <{:radio($person_is_show,0)}>/>
                                    隐藏
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    </table>
                    <div class="btn">
                        <input type="hidden" name="id" id="perid" value="<{$id}>" />
                        <input type="submit" class="button" name="verify" value="确定提交" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
	
</div>
<include file="admin@footer" />