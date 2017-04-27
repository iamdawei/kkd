<style type="text/css">
    .main-content{margin-top: 30px;}
    .role-list {font-size: 16px;color:#0078ff;font-size: 0px;}
    .role-list > li{
        width:300px;
        height: 140px;
        overflow: hidden;
        font-size: 16px;
        padding:40px 20px 20px 100px;
        margin:0 20px 20px 0;
        display: inline-block;
        border:1px solid #DDDDDD;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
    }
    .role-list > li.role_0{
        background:#eaf4f8 url('/images/role_0.png') no-repeat 10px center;
        background-size: 80px;
    }
    .role-list > li.role_1{
        background:#eaf4f8 url('/images/role_1.png') no-repeat 10px center;
        background-size: 80px;
    }
    .role-list > li.role_2{
        background:#eaf4f8 url('/images/role_2.png') no-repeat 10px center;
        background-size: 80px;
    }
    .role-list > li:hover{
        -moz-box-shadow: 0px 2px 5px #DDDDDD;
        -webkit-box-shadow: 0px 2px 5px #DDDDDD;
        box-shadow: 0px 2px 5px #DDDDDD;
    }
    .role-list > li.role-add{
        padding:40px 20px 20px 150px;
        background:#eaf4f8 url('/images/common/add-role.png') no-repeat 60px center;
        background-size: 80px 80px;
        line-height: 50px;
    }
    .role-list > li.load{
        width:100%;
        height:50px;
        padding:0px;
        margin:0px;
        background:none;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        border-radius: 0px;
        border:none;
        overflow:visible;
    }
    .role-list > li.load:hover{
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
        box-shadow:none;
    }
    .role-list > li.role-add > a{color:#0078ff;}
    .role-list > li > span{
        width:100%;
        height:50px;
        display: block;
    }
    .icon-box{position:relative;bottom:0px;text-align: right;font-size: 0px;margin-right:-20px;}
    .t-edit{
        background: url('/images/common/t-edit-hover.png') no-repeat 0 0;
    }
    .t-delete{
        background: url('/images/common/t-delete-hover.png') no-repeat 0 0;
    }
    .form-content{width: 70%;margin:0px auto;}

    .kkd-dialog-wrap .kkd-dialog-container{width:400px;}
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">角色管理</div>
        <div class="main-content">
            <div class="main-content-warp">
                <ul class="role-list" id="kkd-data-target">
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="template-role-edit">
        <div class="form-content">
            <br /><br />
            <div class="input-group">
                <label class="control-label" for="old_password">角色名称 :</label>
                <input type="text" placeholder="请输入角色名称" value="[role_name]" maxlength="15" id="role_name" name="role_name" class="form-control">
            </div>
            <br /><br />
            <div class="clearfix" style="text-align: center;">
                <button class="btn btn-primary lg" data-lock-txt="保存中..." data-unlock-txt="保 存" type="button" onclick="save(this)">保 存</button>
            </div>
        </div>
</script>
<script type="text/template" id="template-role-data">
    <li class="role_[class]">
        <span>[role_name]</span>
        <div class="icon-box"><a href="javascript:void(0);" onclick="update_role([role_id],'[role_name]')" class="t-edit"></a>
            <a href="javascript:void(0);" onclick="kkd_delete([role_id],this)" class="t-delete"></a></div>
    </li>
</script>
<script type="text/javascript">
    var kkd_form_save_type = 'post';
    var kkd_form_save_url = '/role';
    var add_role_obj = "<li class=\"role-add\"><a href=\"javascript:add_role();\">添加新角色</a></li>";
    function add_role(){
        kkd_form_save_type = 'post';
        kkd_form_save_url = '/role';
        var maincontent = $("#template-role-edit").html();
        kkd_dialog_ini('添加新角色',maincontent.replace('[role_name]',''));
    }

    function kkd_init(){
        kkd_data_init();
    }

    function update_role(rid,rname)
    {
        kkd_form_save_type = 'put';
        kkd_form_save_url = '/role/'+rid;
        var maincontent = $("#template-role-edit").html();
        kkd_dialog_ini('编辑角色',maincontent.replace('[role_name]',rname));
    }

    function kkd_delete(uid,obj)
    {
        $.ajax({
            url:"/role/"+uid,
            dataType:'json',
            type:'delete',
            success:function(result){
                if(result.code == 200) {
                    $(obj).parent().parent().hide(1000,function(){$(this).remove();});
                }
                else alert(result.info);
            }
        });
    }

    function save(obj)
    {
        KKD_AJAX_OBJ = $(obj);
        //验证参数
        var role_name = $.trim($("#role_name").val());
        if(role_name.length < 1 || role_name.length >15) return alert('请设置15字以内的角色名');

        //组织请求体
        var req_datas = 'role_name='+role_name;
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

    function kkd_data_init(){
        $.ajax({
            url: '/role',
            dataType:'json',
            type:'get',
            beforeSend:function(){
                $("#kkd-data-target").html('<li class="load">'+ kkd_loading_txt +'</li>');
            },
            success: load_success,
            error:kkd_ajax_error
        });
    }
    function load_success(result)
    {
        if(result.code == 200 ){
            var temp_data = [];
            var temp = $("#template-role-data").html();
            var tempC = 0;

            $(result.data).each(function(i,o){
                var ls = temp.replace(/\[role_id\]/g, o.role_id).replace(/\[role_name\]/g, o.role_name).replace(/\[class\]/g, tempC);
                if(tempC == 2) tempC =0;
                else tempC++;
                temp_data.push(ls);
            });
            temp_data.push(add_role_obj);
            $("#kkd-data-target").html(temp_data.join(''));
        }else {
            alert(result.info);
        }
    }
</script>