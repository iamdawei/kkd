<style type="text/css">
    .kkd-table.dialog>thead>tr>td, .kkd-table.dialog>thead>tr>th{color:#2258A0;font-size:14px;}
    .kkd-table.dialog>thead>tr>th{border:none !important;}
    .dialog-t-title{text-align: left;}
    .dialog-t-time{text-align: right;}
    .kkd-table font{color:red;font-size:12px;}
    .item-content-box{text-align: left;text-align: justify;}
    .item-files{text-align: left;}
    .item-files>li{display:inline-block;padding:3px 5px;}
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">待审核</div>
        <div class="main-header">
            <a class="add" href="javascript:select_all();">批量通过</a>
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
            <th class="dialog-t-title">【[assessment_type]】[assessment_name]</th>
            <th class="dialog-t-time">[teacher_name] [commit_datetime]</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2">
                <div class="item-content-box">
                    <p>1111111111111</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                    <p>我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容，我是内容</p>
                </div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2"><ul class="item-files"><li>文档附件</li><li>文档附件</li><li>文档附件</li><li>文档附件</li><li>文档附件</li><li>文档附件</li></ul></td>
        </tr>
        </tfoot>
    </table>
</script>
<script type="text/template" id="template-assessment-data">
    <tr>
        <th><input type="checkbox" value="[assessment_item_id]" name="select_checkbox[]"></th>
        <td>[assessment_type]</td>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td><a href="javascript:check_item_info([assessment_item_id]);">[assessment_item]</a></td>
        <td>[assessment_number]</td>
        <td>[teacher_name]</td>
        <td class="txt" title="[assessment_role]">[commit_datetime]</td>
        <td class="t-icons-warp"><a href="javascript:void(0);" onclick="kkd_pend([assessment_item_id],this,0)" class="t-push t-status-0"></a>
            <a href="javascript:void(0);" onclick="kkd_pend([assessment_item_id],this,1)" class="t-delete t-status-0"></a></td>
    </tr>
</script>
<script type="text/template" id="template-assessment-pend">
    <div class="form-content">
        <br />
        <div class="input-group">
            <input type="text" placeholder="请填写您的驳回理由..." id="status_descript" name="status_descript" class="form-control">
        </div>
        <br /><br />
        <div class="clearfix" style="text-align: center;">
            <button class="btn btn-primary lg" data-lock-txt="保存中..." data-unlock-txt="保 存" type="button" onclick="save(this)">保 存</button>
        </div>
    </div>
</script>
<script type="text/javascript">
var kkd_form_save_type = 'post';
var kkd_form_save_url = '/pend';

function check_item_info(i)
{
    var maincontent = $("#template-assessment-item-info").html();
    kkd_dialog_ini('考核项目详情',maincontent);
}
function kkd_pend(aid,obj,type)
{
    if(type == 1)
    {
        var maincontent = $("#template-assessment-pend").html();
        kkd_dialog_ini('驳回信息',maincontent);
    }
}

function select_all()
{
    var temp_data =[];
    $("input[name='select_checkbox[]']").each(function(i,o){
        if($(o).prop('checked') === true) temp_data.push(o.value);
    });
    if(temp_data.length == 0) alert('请勾选要通过的项');
    else alert(temp_data.join(','));
}

function kkd_init(){
    kkd_select_int();
    kkd_data_init();
}
function kkd_data_init(url){
    //---------------
    // -- 临时数据模拟
    var temp_data = [];
    var temp = $("#template-assessment-data").html();
    for(var i =0 ;i<10;i++)
    {
        var ls = temp.replace(/\[assessment_item_id\]/g, 0).replace('[assessment_type]', kkd_assessment_type[0]).replace('[assessment_item]', '临时的申请明细情况')
            .replace(/\[assessment_name\]/g, '临时测试项目').replace('[teacher_name]', '临时测试人').replace('[commit_datetime]', '2017-4-23 10:10:10').replace('[assessment_number]', 0);
        temp_data.push(ls);
    }
    $("#kkd-data-target").html(temp_data.join(''));
    //---------------

    kkd_icheck_init();
    return false;
    url = (url)?url:'/pend';
    $.ajax({
        url: url,
        dataType:'json',
        type:'get',
        beforeSend:function(){
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
            $("#kkd-data-target").html('<tr><td colspan="7">'+ kkd_nonedata_txt +'</td></tr>');
        }else{
            $(data.data.data).each(function(i,o){
                var ls = temp.replace(/\[assessment_item_id\]/g,o.assessment_item_id).replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[assessment_item]', o.assessment_item)
                    .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name)
                    .replace('[commit_datetime]', o.commit_datetime).replace('[assessment_number]', o.assessment_number);
                temp_data.push(ls);
            });
            $("#kkd-data-target").html(temp_data.join(''));
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
        kkd_data_init('/pend?page=' + p + "&" + pars);
    }
    else kkd_data_init('/pend?'+p);
}

function search(pars)
{
    var is_open = $("#is_open").val();
    var assessment_type = $("#assessment_type").val();
    var keywords = $.trim($("#keywords").val());

    is_open = (is_open == null)?'':is_open;
    assessment_type = (assessment_type == null)?'':assessment_type;
    keywords = (keywords == null)?'':keywords;
    var s_data = [];
    s_data.push('is_open='+is_open);
    s_data.push('assessment_type='+assessment_type);
    s_data.push('keywords='+keywords);

    if(pars === 1) return s_data.join('&');
    else location_url(s_data.join('&'));
}

function kkd_icheck_init()
{
    $('#select_checkbox_all').on('ifChecked ifUnchecked', function(event){
        if(event.type =='ifUnchecked') $("input[name='select_checkbox[]']").iCheck('uncheck');
        else if(event.type =='ifChecked') $("input[name='select_checkbox[]']").iCheck('check');
    });
    $("input[type='checkbox']").iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
}
</script>