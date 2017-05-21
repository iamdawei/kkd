<style type="text/css">
    .title{padding:5px 10px;color:#0078ff;font-size: 18px;line-height: 18px;border-left:4px solid #FFFFFF;max-height:34px;width:90px;vertical-align: top;}
    .none{border:none;}
    .col-4{width:40%;}
    .col-8{width:80%;}
    .title.blue{border-color:#0078ff;}
    .title.red{border-color:#EA1A0E;}
    .title.yellow{border-color:#FFCC00;}

    .kkd-group{margin: 30px 0px;}
    .kkd-group > .title,.kkd-group > .content,.item-files{display:inline-block;}
    .content.descript{height:auto;color:#aaaaaa;}
    .kkd-group > select,.kkd-group > select> option {padding:6px 12px;}

    .item-files>li.delete-file{border:1px solid #ffffff;}
    .item-files>li.delete-file:hover{background-color:#eaf4f8;border:1px solid #dfdfdf;}
    .item-files>li.delete-file:hover>a{background: url('/images/common/icon-file/delete.png') no-repeat right top;}

    .icon-upfile{display: inline-block;padding:10px 0px 10px 30px;background: url('/images/common/icon-file/upfile.png') no-repeat 0 center;vertical-align: top;}
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
    
    .popover{  color: #666666;}
</style>

<div class="main">
    <div class="main-warp">
        <div class="main-title"><?php echo $item_type; ?></div>
        <div class="main-content">
            <div class="main-content-warp">
                <div class="kkd-group">
                    <div class="title red">项目：</div>
                    <select class="content" name="assessment_set" id="assessment_set">
                    </select>
                </div>
                <div class="kkd-group">
                    <div class="title">描述：</div>
                    <div class="content form-control col-4 descript" id="assessment_descript">载入中...</div>
                </div>
                <div class="kkd-group" id="group_title">
                    <div class="title blue">标题：</div>
                    <input type="text" placeholder="标题长度在 30 字以内" tabindex="1" maxlength="30" id="item_title" name="item_title" class="content form-control col-4" value="<?php echo $item_title;?>" >
                </div>
                <div class="kkd-group" id="group_content">
                    <div class="title yellow">内容：</div>
                    <div class="content col-8">
                        <div id="summernote"><?php echo $item_content;?></div>
                    </div>
                </div>
                <div class="kkd-group" id="group_zip">
                    <ul class="item-files col-8" id="item-files">
                        <li class="icon-file"><span>文档附件：</span></li>
                    </ul>
                    <a class="icon-upfile" href="javascript:upfile();">上传附件</a>
                    <input type="file" id="kkd_file" name="kkd_file" onchange ="do_upload_file()" style="display: none;" value="" />
                </div>
                <div class="clearfix" style="text-align: center;margin:100px 0 40px;">
                    <button id="btn_save" class="btn btn-primary lg" data-lock-txt="执行中..." data-unlock-txt="提 交" type="button" onclick="save(this)">提 交</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var temp_file_li = '<li class="[class] delete-file"><a href="javascript:void(0);" onclick="delete_file(\'[file_name]\',this,[file_index],[file_id])">[client_name]</a></li>';
    var kkd_ass_model = <?php echo $KKD_ASS_MODEL?>;
    var select_option_init = <?php echo $DEFAULT_ITEM?>;
    var upload_file_arr = [];
    var upload_summernote_arr = [];
    var kkd_save_method = '<?php echo $save_method?>';
    var kkd_save_path =  '<?php echo $save_path?>';
    var have_title = 0;
    var have_content = 0;
    var have_zip = 0;
    function upfile()
    {
        if(upload_file_arr.length > 1) return alert('最多只能上传2个文件噢');
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
                    var temp_fix = result.data.client_name.split('.');
                    var file_fix = temp_fix[temp_fix.length-1];

                    $("#item-files").append(temp_file_li.replace('[client_name]',result.data.client_name).replace('[file_name]',result.data.file_name).replace('[class]',kkd_file_arr[file_fix])
                        .replace('[file_index]',upload_file_arr.length).replace('[file_id]',0));
                    upload_file_arr.push([result.data.client_name,result.data.file_name]);

                }
                else alert(result.info);
            },
            error:function(){
                alert('[上传失败] 文件过大');
            }
        });
    }
    function delete_file(file_name,obj,i,file_id)
    {
        $.ajax({
            url: '/assessment/item_delfile',
            dataType:'json',
            data:'file_name='+file_name+'&file_id='+file_id,
            type:'delete',
            success:function(result){
                if(result.code == 200){
                    $(obj).parent().remove();
                    upload_file_arr.splice(i,1);
                }
                else alert(result.info);
            }
        });
    }
    function save(obj)
    {
        KKD_AJAX_OBJ = $(obj);
        //验证参数
        var item_content = $('#summernote').summernote('code');
        var item_title = $('#item_title').val();
        if(item_content.indexOf('src="/upload/item_img/temp/') > 0) item_content=item_content.replace(/src=\"\/upload\/item_img\/temp\//g,'src="/upload/item_img/');
        //组织请求体
        //根据组件渲染值，决定传输数据
        var req_datas = [];
        req_datas.push('assessment_set_id='+$("#assessment_set").val());
        if(have_title == 1) {
            if(item_title.length < 1 || item_title.length >30) return alert('标题长度在 30 字以内');
            req_datas.push('item_title='+item_title);
        }

        if(have_content == 1){
            req_datas.push('item_content='+item_content);
            req_datas.push('imgs='+upload_summernote_arr.join(',,,'));
        }

        if(have_zip == 1){
            var files_temp_data = [];
            for(var i =0 ;i<upload_file_arr.length;i++)
            {
                files_temp_data.push(upload_file_arr[i][0]+"==="+upload_file_arr[i][1]);
            }
            req_datas.push('files='+files_temp_data.join(",,,"));
        }

        $.ajax({
            url: kkd_save_path,
            dataType:'json',
            data:req_datas.join('&'),
            type:kkd_save_method,
            success:function(data){
                if(data.code == 200) {
                    window.location.href="/Home/apply";
                }
                else alert(data.info);
            },
            beforeSend:kkd_ajax_beforeSend,
            error:kkd_ajax_error,
            complete:kkd_ajax_complete
        });
    }

    //组件渲染函数
    function page_drawing(title,content,zip)
    {
        (title == 1)?$("#group_title").show():$("#group_title").hide();
        if(content == 1){
            kkd_summernote_init();
            $("#group_content").show();
        }
        else $("#group_content").hide();
        (zip == 1)?$("#group_zip").show():$("#group_zip").hide();
    }

    //异步读取项目详细数据,并对保存按钮设置请求锁
    function select_assessment()
    {
        var as_id =$('#assessment_set').val();
        $.ajax({
            url: '/assessment/assessment_item_check/'+as_id,
            dataType:'json',
            type:'get',
            success:function(result){
                if(result.code == 200) {
                    var des = result.data.assessment_descript;
                    if(des.length === 0) des='暂无描述';
                    $("#assessment_descript").text(des);
                    var count = result.data.count;
                    var max_number = result.data.max_number;
                    have_title= result.data.have_title;
                    have_content= result.data.have_content;
                    have_zip= result.data.have_zip;

                    if(kkd_save_method == 'put') max_number++;
                    if(count < max_number){
                        $("#btn_save").prop("disabled", false);
                        $("#btn_save").text('提 交');
                        page_drawing(have_title,have_content,have_zip);
                    }
                    else $("#btn_save").text('本项提交量已达标');
                }
                else alert(result.info);
            },
            beforeSend:function(){
                $("#btn_save").prop("disabled", true);
                $("#assessment_descript").text('载入中...');
                page_drawing(0,0,0);
            },
            error:kkd_ajax_error
        });
    }

    //系统初始化，初始化时，根据标准的最大提交数判断是否可提交
    //如果可提交，则根据组件设置的值进行页面元素渲染
    function kkd_init(){
        $(".icon-upfile").popover({
            title:'上传说明',
            trigger:'hover',
            placement:'left',
            html:true,
            content:'附件大小不能超过 2 MB <br> 图片 : gif , jpg , png <br> 文档 : word , ppt , excel <br> 压缩包 : rar , zip<br>媒体 : mp3 , mp4'
        });
        $("#group_title,#group_content,#group_zip").hide();
        $('#assessment_set').html('');
        var stemp_role = '<option value="[assessment_set_id]">[assessment_name]</option>';
        var temp_data = [];
        for(var irole = 0 ;irole < kkd_ass_model.length ;irole++)
        {
            var ls = stemp_role.replace('[assessment_set_id]',kkd_ass_model[irole]['assessment_set_id']).replace('[assessment_name]',kkd_ass_model[irole]['assessment_name']);
            temp_data.push(ls);
        }
        $('#assessment_set').html(temp_data.join(''));
        $('#assessment_set').on('change',function(){
            select_assessment();
        });
        $('#assessment_set').val(select_option_init);
        $('#assessment_set').trigger('change');
        //如果请求模式为put，则表示对该项进行修改，此时进行附件渲染与下拉禁用
        if(kkd_save_method == 'put') {
            split_zip();
            $('#assessment_set').prop("disabled", true);
        }
    }


    function split_zip()
    {
        var files = <?php echo $ass_item_files?>;
        if(!files) return;
        var temp_data = [];
        $(files).each(
            function(i,o){
                var temp_fix = o.file_name.split('.');
                var file_fix = temp_fix[temp_fix.length-1];
                temp_data.push(temp_file_li.replace('[name]',o.file_name).replace('[class]',kkd_file_arr[file_fix]));

                $("#item-files").append(temp_file_li.replace('[client_name]',o.file_name).replace('[file_name]',o.file_real_name).replace('[class]',kkd_file_arr[file_fix])
                    .replace('[file_index]',upload_file_arr.length).replace('[file_id]', o.file_id));
                upload_file_arr.push([o.file_name,o.file_real_name]);
            }
        );
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
                                    var imageUrl = result.data;
                                    $('#summernote').summernote('insertImage', imageUrl);
                                    upload_summernote_arr.push(imageUrl);
                                }
                                else alert(result.info);
                            }
                        });
                    });
                },
                onPaste: function (ne) {
                    var bufferText = ((ne.originalEvent || ne).clipboardData || window.clipboardData).getData('Text/plain');
                    //    ne.preventDefault();
                    ne.preventDefault ? ne.preventDefault() : (ne.returnValue = false);
                    // Firefox fix
                    setTimeout(function () {
                        document.execCommand("insertText", false, bufferText);
                    }, 10);
                    /*  */
                }
            },
            toolbar : toolbar
        };
        $.extend(summernoteConfigDefault, summernoteConfig);
        summernote.summernote(summernoteConfigDefault);
    }

</script>