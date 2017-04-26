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
            <div class="search-warp">
                <div class="select-box">
                    <select class="cs-select kkd-skin" name="item_status" id="item_status">
                        <option value="all" selected>所有状态</option>
                        <option value="0">待&nbsp;&nbsp;&nbsp;审</option>
                        <option value="1">已&nbsp;&nbsp;&nbsp;审</option>
                        <option value="2">驳&nbsp;&nbsp;&nbsp;回</option>
                    </select>
                </div>
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
                        <th>状态</th>
                        <th>类型</th>
                        <th>项目</th>
                        <th>标题<font> ( 点击查看详情 )</font></th>
                        <th>提交日期</th>
                        <th>修改/删除</th>
                    </tr>
                    </thead>
                    <tbody id="kkd-data-target">
                    <tr>
                        <td colspan="6"></td>
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
</script>
<script type="text/template" id="template-assessment-data">
    <tr>
        <th class="item_status_[item_status]">[item_status_txt]</th>
        <td>[assessment_type]</td>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td class="txt"><a href="javascript:check_item_info([assessment_item_id]);">[item_title]</a></td>
        <td>[commit_datetime]</td>
        <td class="t-icons-warp"><a href="javascript:void(0);" onclick="kkd_pend([assessment_item_id])" class="t-icon pend-status-1"></a>
            <a href="javascript:void(0);" onclick="kkd_rebut([assessment_item_id],0)" class="t-icon pend-status-0"></a></td>
    </tr>
</script>
<script type="text/javascript">
var kkd_item_status = ['待审核','已审核','已驳回'];
var kkd_form_save_type = 'post';
var kkd_form_save_url = '/assessment/item';

function check_item_info(i)
{
    kkd_form_save_url = '/assessment/item/'+i;
    ajax_get_teacher();
}

function ajax_get_teacher()
{
    var maincontent = $("#template-assessment-item-info").html();
    kkd_dialog_ini('考核项目详情',maincontent);
    return;
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
        //-------模拟数据
        var temp_data = [];
        var temp = $("#template-assessment-data").html();
        var ls = temp.replace('[item_status]',0).replace('[assessment_item_id]',0).replace('[item_status_txt]',kkd_item_status[0]);
        temp_data.push(ls);
        ls = temp.replace('[item_status]',1).replace('[item_status_txt]',kkd_item_status[1]);
        temp_data.push(ls);
        ls = temp.replace('[item_status]',2).replace('[item_status_txt]',kkd_item_status[2]);
        temp_data.push(ls);
        for(var i=0;i<5;i++)
        {
            temp_data.push(temp);
        }
        $("#kkd-data-target").html(temp_data.join(''));
        return;
        //----------------
        url = (url)?url:'/assessment/check';
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:function(){
                $('#select_checkbox_all').iCheck('uncheck');
                $("#kkd-data-target").html('<tr><td colspan="6">'+ kkd_loading_txt +'</td></tr>');
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
            }else{
                $(data.data.data).each(function(i,o){
                    var ls = temp.replace(/\[assessment_item_id\]/g,o.assessment_item_id).replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[assessment_item]', o.item_title)
                        .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name)
                        .replace('[commit_datetime]', o.commit_datetime).replace('[assessment_number]', o.item_number);
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
            kkd_data_init('/assessment/check?page=' + p + "&" + pars);
        }
        else kkd_data_init('/assessment/check?'+p);
    }
</script>