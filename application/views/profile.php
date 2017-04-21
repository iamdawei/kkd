<style type="text/css">
    .main-content-warp{width:70%;margin:20px auto;min-width:430px;}
    .title{padding:5px 10px;color:#0078ff;font-size: 18px;line-height: 18px;border-left:4px solid #0078ff;}
    .title.red{border-color:#EA1A0E;}
    .user-info {
        width:80%;
        min-width: 300px;
        height: auto;
        display: inline-block;
        padding: 10px;
        line-height: 22px;
        vertical-align: middle;
        text-align: left;
    }
    .user-photo {
        display: inline-block;
        text-align: center;
        padding:10px;
        background-color:#eaf4f8;
        float: right;
    }
    hr{width:100%;height:1px;background-color:#eaf4f8;border: none;}
    .form-content{width: 350px;margin: 0 auto;}
    .input-group{margin-bottom: 20px;}
    .input-group>label{font-size: 16px;}

    .kkd-dialog-wrap .kkd-dialog-container {
        width: 580px;
        min-width: 580px;
    }
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">个人设置</div>
        <div class="main-content">
            <div class="main-content-warp">
                <div class="title red">基本信息 <font style="color:#999;font-size: 12px;">( 头像支持 png / jpg / gif 格式，大小在 50kb 以内)</font></div>
                <p class="user-info" id="main-content"></p>
                <div class="user-photo">
                    <a href="javascript:void(0);" onclick="$('#photo').trigger('click')"><img id="teacher_photo" src="/images/default_user.png" width="90px" height="90px"><br />点击上传头像</a>
                    <input type="file" id="photo" name="photo" onchange ="upload_photo()" style="display: none;" id="input-user-photo" accept="image/gif, image/jpeg, image/png" value="" />
                </div>
                <hr class="clearfix" />
                <div class="title">账号管理</div>
                <p class="user-info">
                    账号：<font id="user-account"></font><br />
                    密码：**********
                </p>
                <div class="clearfix" style="text-align: center;margin:100px 0 40px;">
                    <button class="btn btn-primary lg" data-lock-txt="重置中..." data-unlock-txt="重置密码" type="button" onclick="change_password_dialog()">修改密码</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="temp_pass">
    <form id="pass_form">
        <div class="form-content">
            <p id="login_status" style="font-size:12px;color:red;display:block;text-align: right;">&nbsp;</p>
            <div class="input-group">
                <label class="control-label" for="old_password">初始密码 :</label>
                <input type="text" placeholder="" id="old_password" name="password" class="form-control">
            </div>
            <div class="input-group">
                <label class="control-label" for="new_password">修改密码 :</label>
                <input type="password" placeholder="" id="new_password" name="newpassword" class="form-control">
            </div>
            <div class="input-group">
                <label class="control-label" for="new_password1">确认密码 :</label>
                <input type="password" placeholder="" id="new_password1" name="confirm" class="form-control">
            </div>
            <br /><br /><br /><br />
            <div class="clearfix" style="text-align: center;">
                <button class="btn btn-primary lg" data-lock-txt="修改中..." data-unlock-txt="确认修改" type="button" onclick="change_password(this)">确认修改</button>
            </div>
        </div>
    </form>
</script>
<script type="text/javascript">
    function kkd_init(){
        $.ajax({
            url: '/user',
            dataType:'json',
            type:'get',
            beforeSend:page_loading_wait,
            success: load_success,
            error:kkd_ajax_error
        });
    }
    function load_success(result)
    {
        if(result.code == 200 ){
            var temp_data = [];
            temp_data.push('姓名：'+result.data.teacher_name);
            temp_data.push('<br />角色：'+kkd_data_format(result.data.teacher_role,'role'));
            temp_data.push('<br />入职时间：'+result.data.teacher_indution_date);
            temp_data.push('<br />任教学科：'+result.data.teacher_subject);
            temp_data.push('<br />任教班级：'+kkd_data_format(result.data.teacher_class,'class'));
            $("#main-content").html(temp_data.join(''));
            $("#teacher_photo").attr('src',result.data.teacher_photo);

            $("#user-account").text(result.data.teacher_account);
        }
        else {
            alert(result.info);
        }
    }
    function kkd_data_format(source,type)
    {
        var temp_data = [];
        var ibs=source.split(',');
        if(type === 'role'){
            for(var i = 0 ;i < ibs.length ;i++)
            {
                var ins=ibs[i].split(':');
                temp_data.push(ins[1]);
            }
        }else
        {
            for(var i =0;i<ibs.length;i++)
            {
                var ins=ibs[i].split(':');
                for(var j=0;j<ins[1];j++)
                {
                    var ls = kkd_class_values[ins[0]]+'（'+(j+1)+'）班';
                    temp_data.push(ls);
                }
            }
        }
        return temp_data.join('，');
    }

    function change_password_dialog()
    {
        kkd_dialog_ini('修改密码',$("#temp_pass").html());
    }

    function change_password(obj)
    {
        KKD_AJAX_OBJ = $(obj);
        $.ajax({
            url: '/user',
            dataType:'json',
            type:'put',
            data:$("#pass_form").serialize(),
            beforeSend:kkd_ajax_beforeSend,
            success:function(data){
                if(data.code == 200) {
                    kkd_dialog_close();
                    alert('请妥善保管您的密码');
                }
                else $("#login_status").text(data.info);
            },
            error:kkd_ajax_error,
            complete:kkd_ajax_complete
        });
    }

    function upload_photo()
    {
        $.ajaxFileUpload(
            {
                url: '/user',
                secureuri: false,
                fileElementId:'photo',
                dataType : 'json',
                success:function(result){
                    if(result.code == 200) {
                        $("#teacher_photo").attr('src',result.data);
                    }
                    else alert(result.info);
                }
            });
    }
</script>