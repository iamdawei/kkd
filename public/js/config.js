$.ajaxSetup({
    headers: {
        'TOKEN': $.cookie('token')
    },
    error:kkd_ajax_error
});
(function ($) {
    $.getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]);
        return null;
    }
})(jQuery);
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
    request_unlock()
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

function kkd_dialog_ini(title,content)
{
    var temp_obj = '<div class="kkd-dialog-shadow"></div><div class="kkd-dialog-wrap"><div class="kkd-dialog-container"><div class="kkd-dialog-header"><span>[dialog-title]</span><a class="kkd-dialog-close" href="javascript:kkd_dialog_close();"></a></div><div class="kkd-dialog-content">[dialog-content]</div></div></div>';
    temp_obj = temp_obj.replace('[dialog-title]',title).replace('[dialog-content]',content);
    $('body').append(temp_obj);
}

function kkd_dialog_close()
{
    $("html,body").animate({scrollTop: 0}, 500);
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
    if(current_page == 1){
        temp_data.push('<li><span class="previous">&nbsp;</a></span>');
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
        position: 'bottom-right',
        title: '温馨提示您',
        text: txt,
        class_name: 'clean',
        time: ''
    });

}