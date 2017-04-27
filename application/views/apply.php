<style type="text/css">
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
    /*1待审,0表示通过,2表示已驳回*/
    .t-edit.apply-1,.t-edit.apply-2{
        background: url('/images/common/t-edit.png') no-repeat 0 0;
    }
    .t-edit.apply-1:hover,.t-edit.apply-2:hover{
        background: url('/images/common/t-edit-hover.png') no-repeat 0 0;
    }
    .t-edit.apply-0{
        background: url('/images/common/t-edit-none.png') no-repeat 0 0;
    }
    .t-delete.apply-1,.t-delete.apply-2{
        margin-right:0px;
        background: url('/images/common/t-delete.png') no-repeat 0 0;
    }
    .t-delete.apply-1:hover,.t-delete.apply-2:hover{
        background: url('/images/common/t-delete-hover.png') no-repeat 0 0;
    }
    .t-delete.apply-0{
        margin-right:0px;
        background: url('/images/common/t-delete-none.png') no-repeat 0 0;
    }
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">申请列表</div>
        <div class="main-header">
            <div class="search-warp">
                <div class="select-box">
                    <select class="cs-select kkd-skin" name="item_status" id="item_status">
                        <option value="all" selected>所有状态</option>
                        <option value="1">待&nbsp;&nbsp;&nbsp;审</option>
                        <option value="0">已&nbsp;&nbsp;&nbsp;审</option>
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
                        <th>标题</th>
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
<script type="text/template" id="template-assessment-data">
    <tr>
        <th class="item_status_[item_status]">[item_status_txt]</th>
        <td>[assessment_type]</td>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td class="txt" title="[item_title]">[item_title]</td>
        <td>[commit_datetime]</td>
        <td class="t-icons-warp"><a href="javascript:void(0);" onclick="kkd_edit([assessment_item_id],[item_status])" class="t-edit apply-[item_status]"></a>
            <a href="javascript:void(0);" onclick="kkd_delete([assessment_item_id],this,[item_status])" class="t-delete apply-[item_status]"></a></td>
    </tr>
</script>
<script type="text/javascript">
    var kkd_item_status = ['已审核','待审核','已驳回'];
    var kkd_form_save_type = 'post';
    var kkd_form_save_url = '/assessment/item';

    function kkd_delete(uid,obj,item_status)
    {
        if(item_status === 0){
            alert('已审核的信息，不能删除啦~');
            return;
        }
        $.ajax({
            url:"/assessment/item/"+uid,
            dataType:'json',
            type:'delete',
            success:function(result){
                if(result.code == 200) {
                    $(obj).parent().parent().addClass('disabled');
                    $(obj).parent().parent().children("th").text('[已删除]');
                }
                else alert(result.info);
            }
        });
    }
    function kkd_edit(uid,item_status)
    {
        if(item_status === 0){
            alert('已审核的信息，不能编辑啦~');
            return;
        }
        window.open('/home/item/?edit='+uid);
    }

    function search(pars)
    {
        var assessment_type = $("#assessment_type").val();
        var item_status = $("#item_status").val();
        var keywords = $.trim($("#keywords").val());

        var s_data = [];
        s_data.push('assessment_type='+assessment_type);
        s_data.push('item_status='+item_status);
        s_data.push('keywords='+keywords);

        if(pars === 1) return s_data.join('&');
        else location_url(s_data.join('&'));
    }

    function kkd_init(){
        kkd_select_int();
        kkd_data_init();
    }
    function kkd_data_init(url){
        url = (url)?url:'/assessment/item';
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:function(){
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
                    var ls = temp.replace(/\[item_status\]/g, o.item_status).replace(/\[assessment_item_id\]/g,o.assessment_item_id).replace('[item_status_txt]',kkd_item_status[o.item_status])
                        .replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace(/\[assessment_name\]/g, o.assessment_name).replace(/\[item_title\]/g, o.item_title).replace('[commit_datetime]', o.commit_datetime);
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
            kkd_data_init('/assessment/item?page=' + p + "&" + pars);
        }
        else kkd_data_init('/assessment/item?'+p);
    }
</script>