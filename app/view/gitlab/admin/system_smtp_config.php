<!DOCTYPE html>
<html class="" lang="en">
<head  >

    <? require_once VIEW_PATH.'gitlab/common/header/include.php';?>
    <script src="<?=ROOT_URL?>dev/js/admin/setting.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?=ROOT_URL?>dev/lib/handlebars-v4.0.10.js" type="text/javascript" charset="utf-8"></script>

</head>
<body class="" data-group="" data-page="projects:issues:index" data-project="xphp">
<? require_once VIEW_PATH.'gitlab/common/body/script.php';?>


<header class="navbar navbar-gitlab with-horizontal-nav">
    <a class="sr-only gl-accessibility" href="#content-body" tabindex="1">Skip to content</a>
    <div class="container-fluid">
        <? require_once VIEW_PATH.'gitlab/common/body/header-content.php';?>
    </div>
</header>
<script>
    var findFileURL = "/ismond/xphp/find_file/master";
</script>
<div class="page-with-sidebar">
    <? require_once VIEW_PATH.'gitlab/admin/common-page-nav-admin.php';?>


    <div class="content-wrapper page-with-layout-nav page-with-sub-nav">
        <div class="alert-wrapper">

            <div class="flash-container flash-container-page">
            </div>

        </div>
        <div class="container-fluid ">

            <div class="content" id="content-body">

                <?php include VIEW_PATH.'gitlab/admin/common_system_left_nav.php';?>
                <div class="row prepend-top-default" style="margin-left: 160px">
                    <div class="panel  ">
                        <div class="panel-heading">
                            <strong> SMTP 配置</strong><span>SMTP 配置用于发送邮件</span>
                            <form class="form-inline member-search-form" action="#" accept-charset="UTF-8" method="get">
                                <div class="form-group">
                                    <a class="hidden-xs hidden-sm btn btn-grouped  " id="btn-mail_test">
                                        发送测试
                                    </a>
                                    <a class="hidden-xs hidden-sm btn btn-grouped issuable-edit" data-target="#modal-edit_datetime" data-toggle="modal" href="#modal-edit_datetime">
                                        <i class="fa fa-edit"></i> 修改
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="prepend-top-default">

                            <div class="table-holder">
                                <table class="table ci-table">

                                    <tbody id="tbody_id">
                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="modal-edit_datetime">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a class="close" data-dismiss="modal" href="#">×</a>
                            <h3 class="page-title">修改SMTP</h3>
                        </div>
                        <div class="modal-body">
                            <form class="js-quick-submit js-upload-blob-form form-horizontal"   action="/admin/system/basic_setting_update"   accept-charset="UTF-8" method="post">
                                <div id="form_id">

                                </div>

                                <div class="form-actions">

                                </div>
                                <button name="submit" type="button" class="btn btn-save" id="submit-all">保存</button>
                                <a class="btn btn-cancel" data-dismiss="modal" href="#">取消</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="modal-mail_test">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a class="close" data-dismiss="modal" href="#">×</a>
                            <h3 class="page-title">Test</h3>
                        </div>
                        <div class="modal-body">
                            <form class="js-quick-submit js-upload-blob-form form-horizontal"   action="/admin/system/mail_test"   accept-charset="UTF-8" method="post">
                                <div id="form_id">


                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label">收件人:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="params[recipients]" id="id_max_project_name" value="">
                                        </div>
                                    </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label">标题</label>
                                            <div class="col-sm-10">
                                                <input type="text"  class="form-control" name="params[title]" id="id_title" value="Test Message From Hornet">

                                            </div>
                                        </div>

                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label">邮件格式:</label>
                                        <div class="col-sm-10">

                                            <label style=" font-weight: 200;  ">
                                                <input type="radio" value="text" checked="checked" name="params[content_type]" id="test_type_text">
                                                text
                                            </label>
                                            <label style=" font-weight: 200;  ">
                                                <input type="radio" value="html"  name="params[content_type]" id="test_type_html">
                                                html
                                            </label>

                                        </div>
                                    </div>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label">内容</label>
                                            <div class="col-sm-10">
                                                <textarea name="params[content]" id="test_content" rows="5" class="form-control" rows="3" >This is a test message from JIRA.
Server: JIRA邮件
SMTP Port: 25
Description:
From:
Host User Name:  </textarea>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label">返回日志：</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control"  rows="6" name="resp_log" id="resp_log" style="font-size: 10px"></textarea>

                                            </div>
                                        </div>

                                </div>

                                <div class="form-actions">

                                </div>
                                <button name="btn-submit-test" type="button" class="btn btn-create btn-send_test" id="submit-test">保存</button>
                                <a class="btn btn-cancel" data-dismiss="modal" href="#">取消</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<script type="text/html"  id="datetime_settings_tpl">
    {{#settings}}
    <tr class="commit">
        <td>
            <div class="branch-commit">
                <strong>
                    {{title}}:
                </strong>
            </div>
        </td>
        <td> {{text}} <br> {{description}}</td>
        <td> </td>
    </tr>
    {{/settings}}
</script>
<script type="text/html"  id="datetime_settings_form_tpl">
    {{#settings}}
    <div class="form-group">
        <label class="control-label" for="date_format">{{title}}:</label>
        <div class="col-sm-5">
            <div class="form-group">
                {{#if_eq form_input_type 'text'}}
                <input type="text" class="form-control" name="params[{{_key}}]" id="id_{{_key}}"  value="{{_value}}" />
                {{/if_eq}}
                {{#if_eq form_input_type 'radio'}}
                {{#each form_optional_value }}
                <label style=" font-weight: 200;  ">
                    <input type="radio" value="{{@index}}" checked="checked" name="params[{{../_key}}]" id="id_{{../_key}}">
                    {{this}}
                </label>
                {{/each}}
                {{/if_eq}}
            </div>
        </div>
    </div>

    {{/settings}}

</script>


<script type="text/javascript">


    function show_mail_test(){
        $('#modal-mail_test').modal();
    }

    $(".btn-send_test").click(function(){

        var method = 'post';
        var url = '';

        method =  $(this).closest('form').attr('method') ;
        url =  $(this).closest('form').attr('action') ;
        var params = $(this).closest('form').serialize();
        $.ajax({
            type: method,
            dataType: "json",
            async: true,
            url: url,
            data: params ,
            success: function (resp) {
                alert(resp.msg );
                $('#resp_log').text( resp.data.verbose );
            },
            error: function (resp) {
                alert("请求数据错误" + resp);
            }
        });

    });

    $(function() {

        $('#btn-mail_test').on('click',function () {
            show_mail_test();
        })
        fetchSetting('/admin/system/setting_fetch','mail','datetime_settings_tpl','tbody_id');
        fetchSetting('/admin/system/setting_fetch','mail','datetime_settings_form_tpl', 'form_id');


    });

</script>


</body>
</html>


</div>