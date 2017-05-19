$.ajaxSetup({
    headers: {
        'TOKEN': $.cookie('token')
    },
    error:kkd_ajax_error
});

getUrlParam = function (name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return decodeURI(r[2]);
    return null;
}

//公共信息常量配置部分
var KKD_CONST_LOGIN_USERNAME = '您输入的账号有误';
var KKD_MESSAGE_ERROR_PARAMETER = '请求参数不正确';

var kkd_loading_txt = '<p class="kkd-loading"><img src="/images/loading.gif" /><br />正在载入数据...</p>';
var kkd_nonedata_txt = '<p class="kkd-nonedata">别找了，真没东西啦~！</p>';
var kkd_class_values = {0:'未设置',1:'一',2:'二',3:'三',4:'四',5:'五',6:'六'};
var kkd_assessment_type  = ['专业标准','素养标准','学术标准'];
var kkd_file_arr = {doc:'icon-file-word',docx:'icon-file-word',xls:'icon-file-excel',xlsx:'icon-file-excel',ppt:'icon-file-ppt',pptx:'icon-file-ppt'
    ,jpg:'icon-file-img',png:'icon-file-img',gif:'icon-file-img',mp3:'icon-file-mp3',rar:'icon-file-rar',zip:'icon-file-rar',mp4:'icon-file-video'};
/*动态载入JS,CSS文件*/
function load_file(filename,filetype,callback){

    if(filetype == "js"){
        var fileref = document.createElement('script');
        fileref.setAttribute("type","text/javascript");
        fileref.setAttribute("src",filename);
        document.body.appendChild(fileref);
        fileref.onload=fileref.onreadystatechange=function(){
            if(!this.readyState||this.readyState=='loaded'||this.readyState=='complete'){
                callback();
            }
            fileref.onload=fileref.onreadystatechange=null;
        }
    }else if(filetype == "css"){
        var fileref = document.createElement('link');
        fileref.setAttribute("rel","stylesheet");
        fileref.setAttribute("type","text/css");
        fileref.setAttribute("href",filename);
        document.getElementsByTagName("head")[0].appendChild(fileref);
    }
}
function kkd_show_child(obj)
{
    var s = $(obj).parent();
    if(s.hasClass("show")){
        s.toggleClass("show");
    }else{
        s.toggleClass("show");
    }
}

Date.prototype.Format = function(fmt)
{
    var o = {
        "M+" : this.getMonth()+1,                 //月份
        "d+" : this.getDate(),                    //日
        "h+" : this.getHours(),                   //小时
        "m+" : this.getMinutes(),                 //分
        "s+" : this.getSeconds(),                 //秒
        "q+" : Math.floor((this.getMonth()+3)/3), //季度
        "S"  : this.getMilliseconds()             //毫秒
    };
    if(/(y+)/.test(fmt))
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    for(var k in o)
        if(new RegExp("("+ k +")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
    return fmt;
}

//当前AJAX请求对象
var KKD_AJAX_OBJ = '';

function kkd_ajax_beforeSend()
{
    var is_lock = KKD_AJAX_OBJ.attr('data-lock');
    if(is_lock === 'lock') return false;
    else request_lock();
}

function kkd_ajax_complete()
{
    request_unlock();
}

function kkd_ajax_error()
{
    alert('warming : ajax , site error.');
}

function request_lock()
{
    var obj = KKD_AJAX_OBJ;
    var text = obj.attr('data-lock-txt');
    obj.attr('data-lock','lock');
    obj.text(text);
}

function request_unlock()
{
    var obj = KKD_AJAX_OBJ;
    var text = obj.attr('data-unlock-txt');
    obj.attr('data-lock','unlock');
    obj.text(text);
}

function kkd_dialog_ini(title,content,cssClass)
{
    if(!cssClass) cssClass='';
    var temp_obj = '<div class="kkd-dialog-shadow"></div><div class="kkd-dialog-wrap" style="top:50px;"><div class="kkd-dialog-container"><div class="kkd-dialog-header"><span>[dialog-title]</span><a class="kkd-dialog-close" href="javascript:kkd_dialog_close();"></a></div><div class="kkd-dialog-content [cssClass]">[dialog-content]</div></div></div>';
    temp_obj = temp_obj.replace('[dialog-title]',title).replace('[dialog-content]',content).replace('[cssClass]',cssClass);
    $('body').append(temp_obj);
}

function kkd_dialog_close()
{
    //$("html,body").animate({scrollTop: 0}, 500);
    $('.kkd-dialog-shadow').length ? $('.kkd-dialog-shadow').remove() : '';
    $('.kkd-dialog-wrap').length ? $('.kkd-dialog-wrap').remove() : '';
}

function kkd_delete_confirm(){

}
function kkd_select_int()
{
    (function() {
        [].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
            new SelectFx(el);
        } );
    })();
}
function page_loading_wait(obj)
{
    (obj)?obj = "#main-content":'';
    $(obj).html(kkd_loading_txt);
}
function pages_init(total,current_page,total_page)
{
    if(total == 0) {
        $("#kkd-pagination").html('');
        return;
    }
    var tempa='<li><a href="javascript:location_url([page]);">[page]</a></li>';
    var tempspan='<li class="active"><span>[page]</span></li>';
    var firstli='<li><a href="javascript:location_url([page]);" class="previous">&nbsp;</a></li>';
    var temp_data = [];
    var temp_data_item = '';

    //若当前页是第一页，则上一页按钮取消
    //非首页时，则有上一页
    temp_data.push('<li><span class="total">共 '+total+' 条记录</span></li>');
    if(current_page == 1){
        temp_data.push('<li><span class="previous">&nbsp;</span></li>');
    }
    else if(current_page > 1){
        temp_data.push("<li><a href=\"javascript:location_url(" + (current_page-1) + ");\" class=\"previous\">&nbsp;</a></li>");
    }

    for(var i =0;i<total_page;i++)
    {
        var ti = i+1;
        if(current_page == ti)
            temp_data_item = tempspan.replace(/\[page\]/g,ti);
        else
            temp_data_item = tempa.replace(/\[page\]/g,ti);
        temp_data.push(temp_data_item);
    }

    //若当前页尾页，则下一页按钮取消
    //尾页，则有下一页
    if(current_page == total_page){
        temp_data.push('<li><span class="next">&nbsp;</a></span>');
    }
    else if(current_page < total_page){
        temp_data.push("<li><a href=\"javascript:location_url(" + (current_page+1) + ");\" class=\"next\">&nbsp;</a></li>");
    }
    $("#kkd-pagination").html(temp_data.join(''));
}

window.alert=function(txt){
    $.gritter.add({
        position: 'top-center',
        title: '温馨提示您',
        text: txt,
        class_name: 'clean',
        time: ''
    });
};
//调用案例：confirm('你确定要修改密码',function(){alert('OK');},function(){alert('cancel');});
window.confirm=function(txt,ok,cancel){
    var temp_obj = '<div class="kkd-dialog-shadow"></div><div class="kkd-dialog-wrap" style="top:150px;"><div class="kkd-dialog-container" style="min-width: 420px;width:420px;"><span class="kkd-confirm-icon"></span><div class="kkd-confirm-header"><a class="kkd-dialog-close" href="javascript:kkd_dialog_close();"></a></div><div class="kkd-confirm-content"><p class="txt">[dialog-content]</p></div>'+
        '<div class="kkd-confirm-btns"><button class="btn btn-primary md" style="margin-right:20px;" type="button">确 定</button><button class="btn btn-cancel md" type="button">取 消</button></div></div></div>';
    temp_obj = temp_obj.replace('[dialog-content]',txt);
    $('body').append(temp_obj);
    $(".kkd-confirm-btns>.btn").on('click',kkd_dialog_close);
    $(".kkd-confirm-btns>.btn-primary").on('click',ok);
    $(".kkd-confirm-btns>.btn-cancel").on('click',cancel);
};

//查看单条提交项详情
function get_assessment_item_info(url,obj)
{
    var maincontent = '<table class="kkd-table dialog"><thead><tr><th class="dialog-t-title">【[assessment_type]】[item_title]</th><th class="dialog-t-time">[teacher_name] [commit_datetime]</th></tr>'
        +'</thead><tbody><tr><td colspan="2"><div class="item-content-box">[item_content] </div></td> </tr> </tbody>' +
        '<tfoot><tr><td colspan="2"><ul class="item-files"><li class="icon-file"><span>文档附件：</span></li>[item_zip]</ul></td></tr></tfoot></table>';
    if(obj) maincontent = $(obj).html();

    $.ajax({
        url: url,
        dataType:'json',
        type:'get',
        success:function(result){
            if(result.code == 200) {
                var o = result.data;

                if(o.item_title){
                    maincontent = maincontent.replace('[assessment_type]', kkd_assessment_type[o.assessment_type]).replace('[item_title]', o.item_title)
                        .replace(/\[assessment_name\]/g, o.assessment_name).replace('[teacher_name]', o.teacher_name).replace('[item_zip]',item_split_zip(o.files))
                        .replace('[commit_datetime]', o.commit_datetime).replace('[item_content]', o.item_content).replace(/\[assessment_item_id\]/g, o.assessment_item_id);
                }else
                {
                    maincontent = maincontent.replace('[assessment_type]', '被删除').replace('[item_title]', '该项目被删除了')
                        .replace(/\[assessment_name\]/g, '').replace('[teacher_name]', '').replace('[item_zip]',item_split_zip(o.files))
                        .replace('[commit_datetime]', '').replace('[item_content]', '').replace(/\[assessment_item_id\]/g, '');
                }

                kkd_dialog_ini('考核项目详情',maincontent,'table-style');
            }else alert(result.info);
        }
    });
}
function item_split_zip(files)
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