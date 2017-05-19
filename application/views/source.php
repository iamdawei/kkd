<style type="text/css">
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">资源中心</div>
        <div class="main-header">
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
                <div class="select-box right">
                    <button class="btn btn-search" data-lock="false" onclick="search()" type="button">搜索</button>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="main-content-warp">
                <table class="kkd-table table-hover">
                    <thead>
                    <tr>
                        <th>类型</th>
                        <th>项目</th>
                        <th>申请明细<font> ( 点击查看详情 )</font></th>
                        <th>申请人</th>
                        <th>审核人</th>
                        <th>提交日期</th>
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
<script type="text/template" id="template-assessment-data">
    <tr>
        <td>[assessment_type]</td>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td class="txt" title="[item_title]"><a href="javascript:get_assessment_item_info('/assessment/source/[assessment_item_id]');">[item_title]</a></td>
        <td>[teacher_name]</td>
        <td>[auditor_name]</td>
        <td>[commit_datetime]</td>
    </tr>
</script>
<script type="text/javascript">
var kkd_form_save_url = '/assessment/source';

function check_item_info(i)
{
    $.ajax({
        url: '/assessment/source/'+i,
        dataType:'json',
        type:'get',
        success:function(result){
            if(result.code == 200) {
                var maincontent = $("#template-assessment-item-info").html();
                var o = result.data;
                maincontent = maincontent.replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[item_title]', o.item_title)
                    .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name).replace('[item_zip]',split_zip(o.files))
                    .replace('[commit_datetime]', o.commit_datetime).replace('[item_content]', o.item_content).replace(/\[assessment_item_id\]/g, o.assessment_item_id);
                kkd_dialog_ini('考核项目详情',maincontent);
            }
            else alert(result.info);
        }
    });
}
function split_zip(files)
{
    var temp_data = [];
    var temp_str = "<li class=\"[class]\"><a href=\"/home/download?name=[name]&file=[file_real_name]\">[name]</a></li>";
    $(files).each(
        function(i,o){
            var temp_fix = o.file_name.split('.');
            var file_fix = temp_fix[temp_fix.length-1];
            temp_data.push(temp_str.replace(/\[name\]/g,o.file_name).replace('[file_real_name]',o.file_real_name).replace('[class]',kkd_file_arr[file_fix]));
        }
    );
    if(temp_data.length == 0) return "<li class='none'>无附件</li>";
    else return temp_data.join('');
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
    url = (url)?url:kkd_form_save_url;
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
            $("#kkd-data-target").html('<tr><td colspan="8">'+ kkd_nonedata_txt +'</td></tr>');
        }else{
            $(data.data.data).each(function(i,o){
                var ls = temp.replace(/\[assessment_item_id\]/g,o.assessment_item_id).replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace(/\[item_title\]/g, o.item_title)
                    .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name).replace('[auditor_name]', o.auditor_name)
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
        kkd_data_init(kkd_form_save_url+'?page=' + p + "&" + pars);
    }
    else kkd_data_init(kkd_form_save_url+'?'+p);
}
</script>