<style type="text/css">
    .kkd-dialog-wrap .kkd-dialog-container{width:600px;}
    .kkd-table.dialog>thead>tr>td, .kkd-table.dialog>thead>tr>th{color:#2258A0;font-size:14px;}
    .kkd-table.dialog>thead>tr>th{border:none !important;}
    .dialog-t-title{text-align: left;}
    .dialog-t-time{text-align: right;}
    .kkd-table font{color:red;font-size:12px;}
    .item-content-box{text-align: left;text-align: justify;}
    .item-files{text-align: left;}
    .item-files>li{height:34px;line-height:34px;display:inline-block;padding:3px 5px 3px 30px;}
    .kkd-table>tfoot>tr>td {
        background-color: #F3FBFD;
    }
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">待审核</div>
        <div class="main-header">
            <a class="pend" href="javascript:pend_all();">批量通过</a>
            <div class="search-warp">
                <div class="select-box">
                    <select class="cs-select kkd-skin" name="assessment_type" id="assessment_type">
                        <option value="all" selected>所有类型</option>
                        <option value="0">专&nbsp;&nbsp;&nbsp;业</option>
                        <option value="1">素&nbsp;&nbsp;&nbsp;养</option>
                        <option value="2">学&nbsp;&nbsp;&nbsp;术</option>
                    </select>
                </div>
                <div class="select-box">
                    <input type="text" placeholder="要查找的项目名" id="keywords" class="form-control search" />
                </div>
                <button class="btn btn-search" data-lock="false" onclick="search()" type="button">搜索</button>
            </div>
        </div>
        <div class="main-content">
            <div class="main-content-warp">
                <table class="kkd-table table-hover">
                    <thead>
                    <tr>
                        <th><a href="javascript:void(0);" class="t-select">
                                <input type="checkbox" value="all" id="select_checkbox_all" name="select_checkbox_all"><label for="select_checkbox_all"> 全 选</label>
                            </a></th>
                        <th>类型</th>
                        <th>项目</th>
                        <th>申请明细<font> ( 点击查看详情 )</font></th>
                        <th>分值</th>
                        <th>申请人</th>
                        <th>提交日期</th>
                        <th>通过/驳回</th>
                    </tr>
                    </thead>
                    <tbody id="kkd-data-target">
                    <tr>
                        <td colspan="8"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pages clearfix">
            <ul class="pagination" id="kkd-pagination">
                <li><span class="previous">&nbsp;</span></li>
                <li class="active"><span>1</span></li>
                <li><span class="next">&nbsp;</span></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/template" id="template-assessment-item-info">
    <table class="kkd-table dialog">
        <thead>
        <tr>
            <th class="dialog-t-title">【[assessment_type]】[item_title]</th>
            <th class="dialog-t-time">[teacher_name] [commit_datetime]</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2">
                <div class="item-content-box">
                    [item_content]
                </div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2"><ul class="item-files"><li class="file">文档附件</li>[item_zip]</ul></td>
        </tr>
        </tfoot>
    </table>
    <div style="text-align: center;margin: 50px 0;">
        <button class="btn btn-success lg" data-lock-txt="执行中..." data-unlock-txt="通 过" type="button" onclick="kkd_pend([assessment_item_id])">通 过</button>
        <button class="btn btn-warning lg" data-lock-txt="执行中..." data-unlock-txt="驳 回" type="button" onclick="kkd_rebut([assessment_item_id],0)">驳 回</button>
    </div>
</script>
<script type="text/template" id="template-assessment-data">
    <tr>
        <th><input type="checkbox" value="[assessment_item_id]" name="select_checkbox[]" /></th>
        <td>[assessment_type]</td>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td><a href="javascript:check_item_info([assessment_item_id]);">[assessment_item]</a></td>
        <td>[assessment_number]</td>
        <td>[teacher_name]</td>
        <td class="txt" title="[assessment_role]">[commit_datetime]</td>
        <td class="t-icons-warp"><a href="javascript:void(0);" onclick="kkd_pend([assessment_item_id])" class="t-icon pend-status-1"></a>
            <a href="javascript:void(0);" onclick="kkd_rebut([assessment_item_id],0)" class="t-icon pend-status-0"></a></td>
    </tr>
</script>
<script type="text/template" id="template-assessment-pend">
    <div class="form-content">
        <br />
        <div class="input-group">
            <textarea rows="3" cols="20" id="status_descript" name="status_descript" class="form-control textarea">请填写您的驳回理由...</textarea>
        </div>
        <br /><br />
        <div class="clearfix" style="text-align: center;">
            <button class="btn btn-success lg" data-lock-txt="执行中..." data-unlock-txt="驳 回" type="button" onclick="kkd_rebut([assessment_item_id],1)">驳 回</button>
        </div>
    </div>
</script>
<script type="text/javascript">
var kkd_form_save_type = 'post';
var kkd_form_save_url = '/assessment/check';

function check_item_info(i)
{
    kkd_form_save_url = '/assessment/check/'+i;
    ajax_get_teacher();
}

function ajax_get_teacher()
{
    $.ajax({
        url: kkd_form_save_url,
        dataType:'json',
        type:'get',
        success:function(result){
            if(result.code == 200) {
                var maincontent = $("#template-assessment-item-info").html();
                var o = result.data;
                maincontent = maincontent.replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[item_title]', o.item_title)
                    .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name).replace('[item_zip]',split_zip(o.item_zip))
                    .replace('[commit_datetime]', o.commit_datetime).replace('[item_content]', o.item_content).replace(/\[assessment_item_id\]/g, o.assessment_item_id);
                kkd_dialog_ini('考核项目详情',maincontent);
            }
        }
    });
}
function split_zip(str)
{
    var file_arr = {doc:'icon-file-word',docx:'icon-file-word',xls:'icon-file-excel',xlsx:'icon-file-excel',ppt:'icon-file-ppt',pptx:'icon-file-ppt'
        ,jpg:'icon-file-img',png:'icon-file-img',gif:'icon-file-img'
        ,mp3:'icon-file-mp3',rar:'icon-file-rar',mp4:'icon-file-video'};
    var files = str.split(',');
    var temp_data = [];
    var temp_str = "<li class=\"[class]\"><a href=\"javascript:void(0);\">[name]</a></li>";
    for(var i =0 ; i<files.length;i++)
    {
        var temp_fix = files[i].split('.');
        var file_fix = temp_fix[temp_fix.length-1];
        temp_data.push(temp_str.replace('[name]',files[i]).replace('[class]',file_arr[file_fix]));
    }
    if(temp_data.length == 0) return "<li>无附件</li>";
    else return temp_data.join('');
}
//通过
function kkd_pend(aid)
{
    $.ajax({
        url:'/assessment/check/'+aid,
        dataType:'json',
        type:'put',
        success:function(result){
            if(result.code == 200) {
                alert('[审核通过] 执行完成');
                kkd_dialog_close();
                kkd_data_init();
            }
        }
    });
}
//驳回
function kkd_rebut(aid,type)
{
    if(type == 0)
    {
        kkd_dialog_close();
        var maincontent = $("#template-assessment-pend").html();
        kkd_dialog_ini('驳回信息',maincontent.replace('[assessment_item_id]',aid));
    }
    else
    {
        var sd = $("#status_descript").val();
        if(sd == '请填写您的驳回理由...'){
            $("#status_descript").focus();
            alert('请填写您的驳回理由...');
            return false;
        }
        var data = "status_descript=" + sd;
        $.ajax({
            url:'/assessment/check/'+aid,
            dataType:'json',
            data:data,
            type:'delete',
            success:function(result){
                if(result.code == 200) {
                    alert('[审核驳回] 执行完成');
                    kkd_dialog_close();
                    kkd_data_init();
                }
            }
        });
    }
}

function pend_all()
{
    var temp_data =[];
    $("input[name='select_checkbox[]']").each(function(i,o){
        if($(o).prop('checked') === true) temp_data.push(o.value);
    });
    if(temp_data.length == 0) alert('请勾选要通过的项');
    else{
        $.ajax({
            url:'/assessment/check/'+temp_data.join(','),
            dataType:'json',
            type:'post',
            success:function(result){
                if(result.code == 200) {
                    alert('[批量通过] 执行完成');
                    kkd_data_init();
                }
            }
        });
    }
}
function search(pars)
{
    var assessment_type = $("#assessment_type").val();
    var keywords = $.trim($("#keywords").val());

    var s_data = [];
    s_data.push('assessment_type='+assessment_type);
    s_data.push('keywords='+keywords);

    if(pars === 1) return s_data.join('&');
    else location_url(s_data.join('&'));
}

function kkd_init(){
    kkd_select_int();
    kkd_data_init();
}
function kkd_data_init(url){
    url = (url)?url:'/assessment/check';
    $.ajax({
        url: url,
        dataType:'json',
        type:'get',
        beforeSend:function(){
            $('#select_checkbox_all').iCheck('uncheck');
            $("#kkd-data-target").html('<tr><td colspan="8">'+ kkd_loading_txt +'</td></tr>');
        },
        success: load_success,
        error:kkd_ajax_error
    });
}
function load_success(data)
{
    if(data.code == 200 ){
        var temp = $("#template-assessment-data").html();
        var temp_data = [];
        if(data.data.data.length == 0) {
            $("#kkd-data-target").html('<tr><td colspan="8">'+ kkd_nonedata_txt +'</td></tr>');
            kkd_icheck_init();
        }else{
            $(data.data.data).each(function(i,o){
                var ls = temp.replace(/\[assessment_item_id\]/g,o.assessment_item_id).replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[assessment_item]', o.item_title)
                    .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name)
                    .replace('[commit_datetime]', o.commit_datetime).replace('[assessment_number]', o.item_number);
                temp_data.push(ls);
            });
            $("#kkd-data-target").html(temp_data.join(''));
            kkd_icheck_init();
        }
        pages_init(data.data.total,data.data.current_page,data.data.total_page);
    }
    else {
        alert(data.info);
    }
}

function location_url(p)
{
    if(typeof p === "number"){
        //分页时，带入search参数
        var pars = search(1);
        kkd_data_init('/assessment/check?page=' + p + "&" + pars);
    }
    else kkd_data_init('/assessment/check?'+p);
}

function kkd_icheck_init()
{
    $("input[type='checkbox']").iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('#select_checkbox_all').off('ifChecked ifUnchecked').on('ifChecked ifUnchecked', function(event){
        if(event.type =='ifUnchecked') $("input[name='select_checkbox[]']").iCheck('uncheck');
        else if(event.type =='ifChecked') $("input[name='select_checkbox[]']").iCheck('check');
    });
}
</script>