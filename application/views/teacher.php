<div class="main">
    <div class="main-warp">
        <div class="main-title">教师管理</div>
        <div class="main-header">
            <a class="add" href="javascript:add_teacher();">新 增</a>
            <div class="search-warp">
                <div class="select-box">
                    <select class="cs-select kkd-skin" id="s_teacher_grade">
                        <option value="" disabled selected>所有年级</option>
                        <option value="">所有年级</option>
                        <option value="1">一年级</option>
                        <option value="2">二年级</option>
                        <option value="3">三年级</option>
                        <option value="4">四年级</option>
                        <option value="5">五年级</option>
                        <option value="6">六年级</option>
                    </select>
                </div>
                <div class="select-box">
                    <select class="cs-select kkd-skin" id="s_teacher_subject" name="teacher_role">
                    </select>
                </div>
                <div class="select-box">
                    <select class="cs-select kkd-skin" id="sel_rol" name="sel_rol">
                    </select>
                </div>
                <div class="select-box">
                    <input type="text" placeholder="请输入您要查找的内容" id="s_keywords" class="form-control search" />
                </div>
                <div class="select-box right">
                    <button class="btn btn-search" data-lock="false" onclick="search()" type="button">搜索</button>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="main-content-warp" id="main-content">

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
<script id="item_template" type="text/template">
    <div class="item-box">
        <div class="item-box-warp">
            <div class="item-header">
                <a href="javascript:void(0);" onclick="update_teacher([teacher_id])" class="edit"></a>
                <img class="item-img" src="[teacher_photo]" width="100px" height="100px">
                <a href="javascript:void(0);" onclick="kkd_delete([teacher_id],this)" class="delete"></a>
            </div>
            <div class="item-content">
                <p class="user"><font>[已删除]</font><br />[teacher_name]<br />[teacher_account]</p>
                <p>入职时间：[teacher_indution_date] 任教学科：[teacher_subject]<br />任教班级：[teacher_class]<br />角色：[teacher_role]</p>
            </div>
            <button class="btn btn-primary lg" data-lock-txt="重置中..." data-unlock-txt="重置密码" type="button" onclick="reset_password([teacher_id],this)">重置密码</button>
        </div>
    </div>
</script>
<script id="form_user_template" type="text/template">
    <form class="form-user-edit" id="kkd-user-edit" name="kkd-user-edit">
        <div class="edit-left-warp">
            <div class="input-group">
                <label class="control-label" for="teacher_account">用户名 :</label>
                <input type="text" placeholder="登录账号" id="teacher_account" name="teacher_account" maxlength="11" class="form-control">
            </div>
            <div class="input-group">
                <label class="control-label" for="teacher_name">姓 名 :</label>
                <input type="text" placeholder="姓名" id="teacher_name" name="teacher_name" class="form-control">
            </div>
            <div class="input-group">
                <label class="control-label" for="teacher_email">邮箱 :</label>
                <input type="text" placeholder="邮箱" id="teacher_email" name="teacher_email" class="form-control">
            </div>
        </div>
        <div class="edit-right-warp">
            <div class="head-photo">
                <a href="javascript:void(0);"><img id="teacher_photo" src="/images/default_user.png" width="90px" height="90px"></a>
            </div>
        </div>
        <div class="input-group clearfix">
            <label class="control-label" for="teacher_born_date1">出生年月 :</label>
            <select class="sel_born_year" name="teacher_born_date1" id="teacher_born_date1"></select>
            <label class="select-label">年</label>
            <select class="sel_born_month" name="teacher_born_date2" id="teacher_born_date2"></select>
            <label class="select-label">月</label>
            <select class="sel_born_day" name="teacher_born_date3" id="teacher_born_date3"></select>
            <label class="select-label">日</label>
        </div>
        <div class="input-group">
            <label class="control-label" for="teacher_gender">性别 :</label>
            <select name="teacher_gender" id="teacher_gender">
                <option value="男">男</option>
                <option value="女">女</option>
            </select>
        </div>
        <div class="input-group">
            <label class="control-label" for="teacher_indution_date1">入职时间 :</label>
            <select class="sel_in_year" name="teacher_indution_date1" id="teacher_indution_date1"></select>
            <label class="select-label">年</label>
            <select class="sel_in_month" name="teacher_indution_date2" id="teacher_indution_date2"></select>
            <label class="select-label">月</label>
            <select class="sel_in_day" name="teacher_indution_date3" id="teacher_indution_date3"></select>
            <label class="select-label">日</label>
        </div>
        <div class="input-group">
            <label class="control-label" for="teacher_subject">学科 :</label>
            <select name="teacher_subject" id="teacher_subject">
            </select>
        </div>
        <div class="input-group">
            <label class="control-label">班级 :</label>
            <div class="form-control kkd-icheck">
                <ul id="kkd-checkbox-box">
                </ul>
            </div>
        </div>
        <div class="input-group">
            <label class="control-label">角色 :</label>
            <div class="form-control kkd-icheck">
                <ul id="kkd-checkbox-role-box">
                </ul>
            </div>
        </div>
        <div class="input-group">
            <button class="btn btn-primary lg center" data-lock-txt="保存中..." data-unlock-txt="保存信息" type="button" onclick="save(this)">保存信息</button>
        </div>
    </form>
</script>

<script type="text/javascript">
    var kkd_school_config = <?php echo $KKD_SCHOOL_CONFIG?>;
    var kkd_roles = <?php echo $KKD_ROLES?>;
    var kkd_form_save_type = 'post';
    var kkd_form_save_url = '/teachers';
    function add_teacher()
    {
        var maincontent = $("#form_user_template").html();
        kkd_dialog_ini('教师管理 - 添加',maincontent);
        kkd_icheck_init();
        kkd_date_init();
        kkd_teacher_subject_inti('#teacher_subject');
    }
    function update_teacher(uid)
    {
        var maincontent = $("#form_user_template").html();
        kkd_dialog_ini('教师管理 - 编辑',maincontent);
        kkd_icheck_init();
        kkd_teacher_subject_inti('#teacher_subject');
        kkd_form_save_type = 'put';
        kkd_form_save_url = '/teachers/'+uid;
        ajax_get_teacher();
    }
    function reset_password(uid,obj)
    {
        confirm('是否重置该账户密码？',function(){
            KKD_AJAX_OBJ = $(obj);
            $.ajax({
                url: '/teachers/password/'+uid,
                dataType:'json',
                type:'delete',
                beforeSend:kkd_ajax_beforeSend,
                success:function(data){
                    if(data.code == 200) {
                        alert('密码已重置');
                    }
                    else alert(data.info);
                },
                error:kkd_ajax_error,
                complete:kkd_ajax_complete
            });
        });
    }
    function ajax_get_teacher()
    {
        $.ajax({
            url: kkd_form_save_url,
            dataType:'json',
            type:'get',
            success:function(result){
                if(result.code == 200) {
                    $("#teacher_account").val(result.data.teacher_account);
                    $("#teacher_name").val(result.data.teacher_name);
                    $("#teacher_email").val(result.data.teacher_email);
                    $("#teacher_gender").val(result.data.teacher_gender);
                    $("#teacher_subject").val(result.data.teacher_subject);
                    $("#teacher_class").val(result.data.teacher_class);
                    $("#teacher_photo").attr('src',result.data.teacher_photo);
                    var tbd = result.data.teacher_born_date;
                    var tbds = tbd.split('-');
                    $("#teacher_born_date1").attr('rel',tbds[0]);
                    $("#teacher_born_date2").attr('rel',tbds[1]);
                    $("#teacher_born_date3").attr('rel',tbds[2]);

                    tbd = result.data.teacher_indution_date;
                    tbds = tbd.split('-');
                    $("#teacher_indution_date1").attr('rel',tbds[0]);
                    $("#teacher_indution_date2").attr('rel',tbds[1]);
                    $("#teacher_indution_date3").attr('rel',tbds[2]);
                    kkd_date_init();
                    var ichecks_data = result.data.teacher_class;

                    if(typeof(ichecks_data) === 'string' && ichecks_data.length > 0){
                        var objs_str = "#icbs-" + ichecks_data.replace(/\:/g,'-').replace(/\,/g,',#icbs-');
                        $(objs_str).iCheck('check');
                    }
                    ichecks_data = result.data.teacher_role;
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

    function save(obj)
    {
        KKD_AJAX_OBJ = $(obj);
        var temp_lock = true;
        //格式化日期
        $("#kkd-user-edit select").each(function(i,o){
            if($(o).val() === '0'){
                temp_lock=false;
                alert('请填写正确的日期');
                return false;
            }
        });
        if(temp_lock === false){
            return;
        }
        var temp_source = $("#kkd-user-edit").serialize();
        var d = new Date();
        d.setFullYear($("#teacher_indution_date1").val(),$("#teacher_indution_date2").val(),$("#teacher_indution_date3").val());
        var d2 = new Date();
        d2.setFullYear($("#teacher_born_date1").val(),$("#teacher_born_date2").val(),$("#teacher_born_date3").val());

        //第一步要先开始格式化checkbox，否则会丢失尾部数据
        var data_source = save_checkbox_format(temp_source,'teacher');
        data_source = save_checkbox_format(data_source,'role');
        var teacher_photo = $("#teacher_photo").attr('src');
        data_source = data_source+"&teacher_photo=" + teacher_photo + "&teacher_indution_date="+ d.Format('yyyy-MM-dd')+"&teacher_born_date="+d2.Format('yyyy-MM-dd');
        $.ajax({
            url: kkd_form_save_url,
            dataType:'json',
            data:data_source,
            type:kkd_form_save_type,
            beforeSend:kkd_ajax_beforeSend,
            success:function(data){
                if(data.code == 200) {
                    window.location.reload();
                }
                else alert(data.info);
            },
            error:kkd_ajax_error,
            complete:kkd_ajax_complete
        });
    }

    function save_checkbox_format(source,type)
    {
        var check_txt = (type === 'teacher')?'&teacher_class=':'&teacher_role=';
        if(source.indexOf(check_txt) < 0 )
        {
            return source;
        }
        var temp_data = source.split(check_txt);
        var tearr = [];
        for(var i=1;i<temp_data.length;i++)
        {
            tearr.push(temp_data[i]);
        }
        source = temp_data[0] + check_txt + tearr.join(',');
        return source;
    }
    function kkd_date_init()
    {
        $.ms_DatePicker({
            YearSelector: ".sel_born_year",
            MonthSelector: ".sel_born_month",
            DaySelector: ".sel_born_day"
        });
        $.ms_DatePicker();
        $.ms_DatePicker({
            YearSelector: ".sel_in_year",
            MonthSelector: ".sel_in_month",
            DaySelector: ".sel_in_day"
        });
        $.ms_DatePicker();
    }

    function kkd_icheck_init()
    {
        var source = kkd_school_config['school_grade_class'];
        var stemp = '<li><input type="checkbox" value="[data-value]" id="icbs-[number]" name="teacher_class"><label for="icbs-[number]">[value]</label></li>';
        var ibs=source.split(',');
        $('#kkd-checkbox-box').html('');
        var temp_data = [];
        var t_i = 0;
        for(var i =0;i<ibs.length;i++)
        {
            var ins=ibs[i].split(':');
            for(var j=0;j<ins[1];j++)
            {
                var ls = stemp.replace(/\[number\]/g, ((i+1)+"-"+(j+1))).replace('[data-value]',(i+1)+":"+(j+1)).replace('[value]',kkd_class_values[ins[0]]+'（'+(j+1)+'）班');
                temp_data.push(ls);
                t_i++;
            }
            temp_data.push("<br />");
        }
        $('#kkd-checkbox-box').html(temp_data.join(''));

        //角色icheck
        $('#kkd-checkbox-role-box').html('');
        var stemp_role = '<li><input type="checkbox" value="[data-value]" id="icbs-role-[id]" name="teacher_role"><label for="icbs-role-[id]">[value]</label></li>';
        var temp_data = [];
        for(var irole = 0 ;irole < kkd_roles.length ;irole++)
        {
            var ls = stemp_role.replace(/\[id\]/g, kkd_roles[irole]['role_id']).replace('[data-value]',kkd_roles[irole]['role_id']+":"+kkd_roles[irole]['role_name']).replace('[value]',kkd_roles[irole]['role_name']);
            temp_data.push(ls);
        }
        $('#kkd-checkbox-role-box').html(temp_data.join(''));

        $('#kkd-checkbox-box input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
        $('#kkd-checkbox-role-box input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
    }

    function location_url(p)
    {
        if(typeof p === "number"){
            //分页时，带入search参数
            var pars = search(1);
            kkd_data_init('/teachers?page=' + p + "&" + pars);
        }
        else kkd_data_init('/teachers?'+p);
    }

    function search(pars)
    {
        var teacher_grade = $("#s_teacher_grade").val();
        var teacher_subject = $("#s_teacher_subject").val();
        var sel_role=$('#sel_rol').val();
        var keywords = $.trim($("#s_keywords").val());

        teacher_grade = (teacher_grade == null)?'':teacher_grade;
        teacher_subject = (teacher_subject == null)?'':teacher_subject;
        sel_role = (sel_role == null)?'':sel_role;
        keywords = (keywords == null)?'':keywords;
        var s_data = [];
        s_data.push('teacher_class='+teacher_grade);
        s_data.push('teacher_subject='+teacher_subject);
        s_data.push('teacher_role='+sel_role);
        s_data.push('keywords='+keywords);

        if(pars === 1) return s_data.join('&');
        else location_url(s_data.join('&'));
    }

    function kkd_delete(uid,obj)
    {
        confirm('是否删除该账号？',function(){
            $.ajax({
                url: '/teachers/'+uid,
                dataType:'json',
                type:'delete',
                success:function(data){
                    if(data.code == 200) {
                        $(obj).parent().parent().addClass('disabled');
                    }
                    else alert(data.info);
                }
            });
        });
    }

    //---------------------------
    //页面数据初始化
    // kkd_init:页面初始化入口
    //---------------------------
    function kkd_init()
    {
        kkd_teacher_subject_inti('#s_teacher_subject');
        kkd_sel_rol();
        kkd_select_int();
        kkd_data_init();
    }

    function kkd_data_init(url){
        url = (url)?url:'/teachers';
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:page_loading_wait,
            success: load_success,
            error:kkd_ajax_error,
            complete:load_complete
        });
    }

    function load_success(data)
    {
        if(data.code == 200 ){
            var temp = $("#item_template").html();
            var temp_data = [];
            if(data.data.data.length == 0) {
                $("#main-content").html(kkd_nonedata_txt);
            }else{
                $(data.data.data).each(function(i,o){
                    var ls = temp.replace(/\[teacher_id\]/g, o.teacher_id).replace('[teacher_name]', o.teacher_name).replace('[teacher_account]', o.teacher_account)
                        .replace('[teacher_photo]', o.teacher_photo).replace('[teacher_indution_date]', o.teacher_indution_date).replace('[teacher_subject]', (o.teacher_subject)?o.teacher_subject:'未设置')
                        .replace('[teacher_class]', kkd_teacher_join_data(o.teacher_class,'class')).replace('[teacher_role]', kkd_teacher_join_data(o.teacher_role,'role'));
                    temp_data.push(ls);
                });
                $("#main-content").html(temp_data.join(''));
            }
            pages_init(data.data.total,data.data.current_page,data.data.total_page);
        }
        else {
            alert(data.info);
        }
    }

    function kkd_teacher_join_data(source,type)
    {
        if(source == null || source == '') return '未设置';
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

    function load_complete(data){

    }

    function kkd_teacher_subject_inti(obj)
    {
        var ss =kkd_school_config['school_subject'];
        var vas = ss.split(',');
        var temp_data = [];
        temp_data.push('<option value="">所有学科</option>');
        var temp_model = '<option value="[value]">[value]</option>';
        for(var i =0 ;i<vas.length;i++)
        {
            temp_data.push(temp_model.replace(/\[value\]/g,vas[i]));
        }
        $(obj).html(temp_data.join(''));
    }

    function kkd_sel_rol() {
        var oOpt="<option value=''>所有角色</option>";
        for(var i=0;i<kkd_roles.length;i++){
            oOpt+=`<option value="${kkd_roles[i].role_id}">${kkd_roles[i].role_name}</option>`;
        }
        $('#sel_rol').html(oOpt);
    }
</script>                           