<style type="text/css">
    .title{padding:5px 10px;color:#0078ff;font-size: 18px;line-height: 18px;border-left:4px solid #0078ff;max-height:34px;width:90px;vertical-align: top;}
    .none{border:none;}
    .col-4{width:40%;}
    .col-8{width:80%;}
    .title.red{border-color:#EA1A0E;}
    .title.yellow{border-color:#FFCC00;}

    .kkd-group{margin: 30px 0px;}
    .kkd-group > .title,.kkd-group > .content,.item-files{display:inline-block;}
    .kkd-group > select {padding:6px 12px;}

    .item-files>li.delete-file{border:1px solid #ffffff;}
    .item-files>li.delete-file:hover{background-color:#eaf4f8;border:1px solid #dfdfdf;}
    .item-files>li.delete-file:hover>a{background: url('/images/common/icon-file/delete.png') no-repeat right top;}

    .icon-upfile{height:40px;line-height: 40px;padding:10px 0px 10px 30px;background: url('/images/common/icon-file/upfile.png') no-repeat 0 center;vertical-align: top;}
    .icon-upfile:hover{background: url('/images/common/icon-file/upfile-hover.png') no-repeat 0 center;}

    .panel{border-radius: 0;}
    .note-editor.note-frame {border: 1px solid #dddddd;}
    .note-editor.note-frame .note-editing-area .note-codable {background-color: #444;  color:#dfdfdf;}
    .panel-default > .panel-heading {color: #333;background-color: #eaf4f8;  border-color: #efefef;  }
    .note-editor .btn-sm i{font-size:12px}
    .note-editor .dropdown-menu > li > a:hover{background:#f5f5f5;color:#333;}
    .note-editor .dropdown-menu{margin-top:0px;border: 1px solid rgba(0, 0, 0, .15);}
    .note-editor .dropdown-menu pre{margin:0 0 10px;}
    .note-editor .dropdown-menu h1,.note-editor .dropdown-menu h2,.note-editor .dropdown-menu h3,
    .note-editor .dropdown-menu h4,.note-editor .dropdown-menu h5,.note-editor .dropdown-menu h6{line-height: 1.1;}

</style>

<div class="main">
    <div class="main-warp">
        <div class="main-title">专业标准</div>
        <div class="main-content">
            <div class="main-content-warp">
                <div class="kkd-group">
                    <div class="title red">项目：</div>
                    <select class="content" name="assessment_set" id="assessment_set">
                    </select>
                </div>
                <div class="kkd-group">
                    <div class="title">标题：</div>
                    <input type="text" placeholder="标题长度在 15 字以内" maxlength="15" id="item_title" name="item_title" class="content form-control col-4">
                </div>
                <div class="kkd-group">
                    <div class="title yellow">内容：</div>
                    <div class="content col-8">
                        <div id="summernote"></div>
                    </div>
                </div>
                <div class="kkd-group">
                    <ul class="item-files col-8" id="item-files">
                        <li class="icon-file"><span>文档附件：</span></li>
                        <li class="icon-file-excel delete-file"><a href="javascript:void(0);" onclick="delete_file(0,this)">测试.xls</a></li>
                    </ul>
                    <a class="icon-upfile" href="javascript:upfile();">上传附件</a>
                    <input type="file" id="kkd_file" name="kkd_file" onchange ="do_upload_file()" style="display: none;" value="" />
                </div>
                <div class="clearfix" style="text-align: center;margin:100px 0 40px;">
                    <button class="btn btn-primary lg" data-lock-txt="执行中..." data-unlock-txt="提 交" type="button" onclick="save(this)">提 交</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var temp_file_li = '<li class="[class] delete-file"><a href="javascript:void(0);" onclick="delete_file(0,this)">[file_name]</a></li>';
    var kkd_ass_model = <?php echo $KKD_ASS_MODEL?>;
    var select_option_init = <?php echo $SELECT_OPTION_INIT?>;
    function upfile()
    {
//        var temp = '<li class="icon-file-excel delete-file"><a href="javascript:void(0);" onclick="delete_file(0,this)">测试.xls</a></li>';
//        $("#item-files").append(temp);
        $('#kkd_file').trigger('click');
    }
    function do_upload_file()
    {
        $.ajaxFileUpload({
            url: '/assessment/item_upfile',
            secureuri: false,
            fileElementId:'kkd_file',
            dataType : 'json',
            success:function(result){
                if(result.code == 200) {
                    var temp_fix = result.data.split('.');
                    var file_fix = temp_fix[temp_fix.length-1];
                    $("#item-files").append(temp_file_li.replace('[file_name]',result.data).replace('[class]',kkd_file_arr[file_fix]));
                }
                else alert(result.info);
            }
        });
    }
    function delete_file(file_id,obj)
    {
        $(obj).parent().remove();
    }
    function save(obj)
    {
        //alert($('#summernote').summernote('code'));
        KKD_AJAX_OBJ = $(obj);
        //验证参数
        var item_content = $('#summernote').summernote('code');
        var item_title = $('#item_title').val();
        var assessment_name = $.trim($("#assessment_name").val());
        if(item_title.length < 1 || item_title.length >15) return alert('标题长度在15字以内');

        if(item_content == 0) return alert('请输入内容');

        //组织请求体
        var req_datas = 'item_title=0'+item_title+'&item_content=0'+item_content+'&assessment_item_id='+$("#assessment_set").val()+'&assessment_name='+$("#assessment_set").find("option:selected").text();
        $.ajax({
            url: '/assessment/item',
            dataType:'json',
            data:req_datas,
            type:'post',
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

    function kkd_init(){
        kkd_summernote_init();

        $('#assessment_set').html('');
        var stemp_role = '<option value="[assessment_set_id]">[assessment_name]</option>';
        var temp_data = [];
        for(var irole = 0 ;irole < kkd_ass_model.length ;irole++)
        {
            var ls = stemp_role.replace('[assessment_set_id]',kkd_ass_model[irole]['assessment_set_id']).replace('[assessment_name]',kkd_ass_model[irole]['assessment_name']);
            temp_data.push(ls);
        }
        $('#assessment_set').html(temp_data.join(''));
        $('#assessment_set').val(select_option_init);
    }

    function kkd_summernote_init()
    {
        //http://wb-mgrigorov.rhcloud.com/summernote
        var summernoteConfig = {"ToolbarOptions":{
            "Style":["style","fontsize","color","bold","italic","underline","strikethrough","clear"],
            "Layout":["ul","ol","paragraph","height"],"Insert":["picture","link","video","table","hr"],
            "Misc":["fullscreen","codeview","undo","redo","help"]},
            "overlayTimeout":2000,
            "maxFilesize":512,
            "minHeight":300,
            "lang": "zh-CN",
            "airMode":false,
            "imageUploadUrl":"/assessment/item_img"
        };
        var summernote = $('#summernote');

        var toolbar = [];
        $.each(summernoteConfig.ToolbarOptions, function(key, value) {
            var category = [];
            category.push(key);
            category.push(value);
            toolbar.push(category);
        });
        var summernoteConfigDefault = {
            callbacks : {
                onImageUpload : function(files) {
                    var files = $(files);
                    var filesSize = files.length;

                    files.each(function() {
                        var file = this;
                        var data = new FormData();
                        data.append("upfile", file);
                        url = summernoteConfig.imageUploadUrl;
                        $.ajax({
                            data : data,
                            type : "POST",
                            url : url,
                            cache : false,
                            contentType : false,
                            processData : false,
                            beforeSend:function(){
                                alert('正在上传图片...');
                            },
                            success : function(result) {
                                if(result.code == 200){
                                    console.log(result);
                                    var imageUrl = result.data;
                                    $('#summernote').summernote('insertImage', imageUrl);
                                }
                                else alert(result.info);
                            }
                        });
                    });
                }
            },
            toolbar : toolbar
        };
        $.extend(summernoteConfigDefault, summernoteConfig);
        summernote.summernote(summernoteConfigDefault);
    }
</script>