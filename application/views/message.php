<style type="text/css">

</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">系统消息</div>
        <div class="main-content">
            <div class="main-content-warp" id="kkd-data-target">

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
<script type="text/template" id="template-message-data">
    <div class="media">
        <div class="media-left">
            <img class="media-object" src="/images/icon.png" style="width: 60px; height: 60px;" />
        </div>
        <div class="media-body">
            <h5 class="media-heading">[auditor_name]<font class="item_status_[item_status]"> [item_status_txt] </font>了你发布的【[assessment_type]】【[assessment_name]】
                <a class="color-1" href="/assessment/item/[assessment_item_id]" target="_blank">《[item_title]》</a></h5>
            <p>[status_descript]</p>
        </div>
        <div class="media-right">
            <p>[auditor_datetime]</p>
        </div>
    </div>
</script>
<script type="text/javascript">
    var kkd_item_status = ['审核通过','待审核','驳回'];
    function kkd_init(){
        kkd_data_init();
        read_message();
    }

    function kkd_data_init(url){
        url = (url)?url:'/message';
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:function(){
                $("#kkd-data-target").html(kkd_loading_txt);
            },
            success: load_success,
            error:kkd_ajax_error
        });
    }
    function load_success(data)
    {
        if(data.code == 200 ){
            var temp = $("#template-message-data").html();
            var temp_data = [];
            if(data.data.data.length == 0) {
                $("#kkd-data-target").html(kkd_nonedata_txt);
            }else{
                $(data.data.data).each(function(i,o){
                    var ls = temp.replace('[auditor_name]', o.auditor_name).replace('[item_status]', o.item_status).replace('[item_status]', o.item_status)
                        .replace('[item_status_txt]', kkd_item_status[o.item_status]).replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[assessment_name]', o.assessment_name)
                        .replace('[item_title]', o.item_title).replace('[assessment_item_id]', o.assessment_item_id)
                        .replace('[status_descript]', show_status_descript(o.item_status,o.status_descript)).replace('[auditor_datetime]', o.auditor_datetime);
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

    function show_status_descript(s,txt)
    {
        if(s == 0) return '已通过';
        else return '驳回原因：'+txt;
    }

    function location_url(p)
    {
        kkd_data_init('/message?page=' + p);
    }

    function read_message()
    {
        $.ajax({
            url: '/message',
            dataType:'json',
            type:'put',
            success: function(result)
            {
                if(result.code == 200)
                {
                    $(".bubble").remove();
                }
            }
        });
    }
</script>