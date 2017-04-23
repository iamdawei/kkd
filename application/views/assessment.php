<style type="text/css">
    .form-user-edit{margin-top: 20px;}
    .form-user-edit .input-group{float:none;border-top: none;}
    .child-title font{color:red;font-size:12px;}
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">考核标准</div>
        <div class="main-header">
            <a class="add" href="javascript:add_assessment();">新增考核标准</a>
            <div class="search-warp">
                <div class="select-box">
                    <select class="cs-select kkd-skin" name="is_open" id="is_open">
                        <option value="all" selected>所有状态</option>
                        <option value="1">已发布</option>
                        <option value="0">未发布</option>
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
                        <th>分值</th>
                        <th>组件</th>
                        <th>审核上级</th>
                        <th>发布/修改/删除</th>
                    </tr>
                    </thead>
                    <tbody id="kkd-data-target">
                    <tr>
                        <td colspan="7"></td>
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
<script id="template-assessment-dialog" type="text/template">
    <form class="form-user-edit" id="kkd-assessment-edit" name="kkd-assessment-edit">
        <div class="child-title red">基础设置</div>
        <div class="input-group">
            <label class="control-label">类型 :</label>
            <div class="form-control kkd-icheck">
                <ul id="kkd-checkbox-type">
                    <li><input type="radio" value="0" id="type-1" name="assessment_type" checked><label for="type-1">专业标准</label></li>
                    <li><input type="radio" value="1" id="type-2" name="assessment_type"><label for="type-2">素养标准</label></li>
                    <li><input type="radio" value="2" id="type-3" name="assessment_type"><label for="type-3">学术标准</label></li>
                </ul>
            </div>
        </div>
        <div class="input-group">
            <label class="control-label" for="assessment_name">项目 :</label>
            <input type="text" placeholder="如：集团项目示范课" id="assessment_name" maxlength="15" name="assessment_name" class="form-control">
        </div>
        <div class="child-title">组件设置<font> ( 可多选 )</font></div>
        <div class="input-group">
            <label class="control-label">组件 :</label>
            <div class="form-control kkd-icheck">
                <ul id="kkd-checkbox-group">
                    <li><input type="checkbox" value="1" id="have_title" name="have_title"><label for="have_title">标题</label></li>
                    <li><input type="checkbox" value="1" id="have_content" name="have_content"><label for="have_content">内容</label></li>
                    <li><input type="checkbox" value="1" id="have_zip" name="have_zip"><label for="have_zip">附件</label></li>
                </ul>
            </div>
        </div>
        <div class="child-title yellow">分值设置<font> ( 如果未负数则表示扣分 )</font></div>
        <div class="input-group">
            <label class="control-label">得分 :</label>
            <div class="form-control kkd-icheck">
                <div class="spinner" data-trigger="spinner">
                    <button class="decrease" type="button" data-spin="down">-</button>
                    <input class="kkd-spinner" name="assessment_number" id="assessment_number" type="text" value="1" data-rule="defaults" type="text" />
                    <button class="increase" type="button" data-spin="up">+</button>
                </div>
            </div>
        </div>
        <div class="child-title green">审核设置<font> ( 可多选 )</font></div>
        <div class="input-group">
            <label class="control-label">审核上级 :</label>
            <div class="form-control kkd-icheck">
                <ul id="kkd-checkbox-role">
                </ul>
            </div>
        </div>
        <div class="clearfix" style="text-align: center;">
            <br /><br />
            <button class="btn btn-primary lg" data-lock-txt="保存中..." data-unlock-txt="保存信息" type="button" onclick="save(this)">保存信息</button>
        </div>
    </form>
</script>
<script type="text/template" id="template-assessment-data">
    <tr>
        <th class="status_[is_open]">[is_open_txt]</th>
        <td>[assessment_type]</td>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td>[assessment_number]</td>
        <td>[assessment_group]</td>
        <td class="txt" title="[assessment_role]">[assessment_role]</td>
        <td class="t-icons-warp"><a href="javascript:void(0);" onclick="kkd_push([assessment_set_id],[is_open])" class="t-push t-status-[is_open]"></a>
            <a href="javascript:void(0);" onclick="update_assessment([assessment_set_id],[is_open])" class="t-edit t-status-[is_open]"></a>
            <a href="javascript:void(0);" onclick="kkd_delete([assessment_set_id],this,[is_open])" class="t-delete t-status-[is_open]"></a></td>
    </tr>
</script>
<script type="text/javascript">
    var kkd_roles = <?php echo $KKD_ROLES?>;
    var kkd_assessment_open  = ['未发布','已发布'];
    var kkd_form_save_type = 'post';
    var kkd_form_save_url = '/assessment';
    function add_assessment()
    {
        kkd_form_save_type = 'post';
        kkd_form_save_url = '/assessment';
        var maincontent = $("#template-assessment-dialog").html();
        kkd_dialog_ini('新增考核标准',maincontent);
        $('[data-trigger="spinner"]').spinner();
        kkd_icheck_init();
    }

    function save(obj)
    {
        KKD_AJAX_OBJ = $(obj);
        //验证参数
        var assessment_name = $.trim($("#assessment_name").val());
        if(assessment_name.length < 1 || assessment_name.length >15) return alert('请设置15字以内的项目名');
        $("#assessment_name").val(assessment_name);
        var have_title = $("#have_title").prop('checked');
        var have_content = $("#have_content").prop('checked');
        var have_zip = $("#have_zip").prop('checked');
        if($("#assessment_number").val() == 0) return alert('分值不能为 0 噢');

        //组织请求体
        var req_datas = $("#kkd-assessment-edit").serialize();
        if(req_datas.indexOf('&assessment_role') < 0) return alert('请设置审核角色');
        if(!have_title) req_datas += '&have_title=0';
        if(!have_content) req_datas += '&have_content=0';
        if(!have_zip) req_datas += '&have_zip=0';
        $.ajax({
            url: kkd_form_save_url,
            dataType:'json',
            data:req_datas,
            type:kkd_form_save_type,
            success:function(data){
                if(data.code == 200) {
                    kkd_dialog_close();
                    kkd_data_init();
                }
                else alert(data.info);
            },
            beforeSend:kkd_ajax_beforeSend,
            error:kkd_ajax_error,
            complete:kkd_ajax_complete
        });
    }
    function kkd_delete(uid,obj,is_open)
    {
        if(is_open == 1){
            alert('已发布，不能删除啦~');
            return;
        }
        $.ajax({
            url: '/assessment/'+uid,
            dataType:'json',
            type:'delete',
            success:function(data){
                if(data.code == 200) {
                    $(obj).parent().parent().addClass('disabled');
                    $(obj).parent().parent().children("th").text('[已删除]');
                }
                else alert(data.info);
            }
        });
    }
    function update_assessment(uid,is_open)
    {
        if(is_open == 1){
            alert('已发布，不能修改啦~');
            return;
        }

        var maincontent = $("#template-assessment-dialog").html();
        kkd_dialog_ini('新增考核标准',maincontent);
        $('[data-trigger="spinner"]').spinner();
        kkd_icheck_init();
        kkd_form_save_type = 'put';
        kkd_form_save_url = '/assessment/'+uid;
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
                    $("#assessment_name").val(result.data.assessment_name);
                    $("#assessment_number").val(result.data.assessment_number);

                    $("input[name='assessment_type']").eq(result.data.assessment_type).iCheck('check');

                    (result.data.have_title==1)?$("#have_title").iCheck('check'):'';
                    (result.data.have_content==1)?$("#have_content").iCheck('check'):'';
                    (result.data.have_zip==1)?$("#have_zip").iCheck('check'):'';

                    var ichecks_data = result.data.assessment_role;
                    if(typeof(ichecks_data) === 'string' && ichecks_data.length > 0){
                        var temparr = ichecks_data.split(',');
                        for(var i =0;i<temparr.length;i++)
                        {
                            var f_t = temparr[i].split(':');
                            var objs_str = "#icbs-role-" + f_t[0];
                            $(objs_str).iCheck('check');
                        }
                    }
                }
            }
        });
    }

    function kkd_push(uid,is_open)
    {
        if(is_open == 1){
            alert('不能重复发布噢');
            return;
        }
        $.ajax({
            url: '/assessment/open/'+uid,
            dataType:'json',
            type:'put',
            success:function(data){
                if(data.code == 200) {
                    kkd_data_init();
                }
                else alert(data.info);
            }
        });
    }

    function kkd_init(){
        kkd_select_int();
        kkd_data_init();
    }
    function kkd_data_init(url){
        url = (url)?url:'/assessment';
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:function(){
                $("#kkd-data-target").html('<tr><td colspan="7">'+ kkd_loading_txt +'</td></tr>');
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
                    var ass_g_txt = [];
                    if(o.have_title == 1)ass_g_txt.push('标题');
                    if(o.have_content == 1)ass_g_txt.push('内容');
                    if(o.have_zip == 1)ass_g_txt.push('附件');
                    if(ass_g_txt.length==0)ass_g_txt.push('- / - / -');
                    var ls = temp.replace(/\[is_open\]/g, o.is_open).replace('[is_open_txt]',kkd_assessment_open[o.is_open]).replace('[assessment_type]', kkd_assessment_type[o.assessment_type])
                        .replace(/\[assessment_name\]/g, o.assessment_name).replace('[assessment_group]', ass_g_txt.join('/'))
                        .replace('[assessment_number]', o.assessment_number).replace(/\[assessment_role\]/g, kkd_teacher_join_data(o.assessment_role,'role')).replace(/\[assessment_set_id\]/g, o.assessment_set_id);
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
            kkd_data_init('/assessment?page=' + p + "&" + pars);
        }
        else kkd_data_init('/assessment?'+p);
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

    function kkd_teacher_join_data(source,type)
    {
        if(source == null || source == '') return '';
        var temp_data = [];
        var ibs = source.split(',');
        for(var i =0;i<ibs.length;i++)
        {
            var ins=ibs[i].split(':');
            var ls = '';
            switch (type){
                case 'class':
                    ls = kkd_class_values[ins[0]]+'（'+ins[1]+'）班';
                    break;
                case 'role':
                    ls = ins[1];
                    break;
            }
            temp_data.push(ls);
        }
        return temp_data.join('，');
    }
    function kkd_icheck_init()
    {
        $('#kkd-checkbox-role').html('');
        var stemp_role = '<li><input type="checkbox" value="[data-value]" id="icbs-role-[id]" name="assessment_role[]"><label for="icbs-role-[id]">[value]</label></li>';
        var temp_data = [];
        for(var irole = 0 ;irole < kkd_roles.length ;irole++)
        {
            var ls = stemp_role.replace(/\[id\]/g, kkd_roles[irole]['role_id']).replace('[data-value]',kkd_roles[irole]['role_id']+":"+kkd_roles[irole]['role_name']).replace('[value]',kkd_roles[irole]['role_name']);
            temp_data.push(ls);
        }
        $('#kkd-checkbox-role').html(temp_data.join(''));

        $('#kkd-checkbox-type input,#kkd-checkbox-group input,#kkd-checkbox-role input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
    }
</script>