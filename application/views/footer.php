<div class="clearfix"></div>
<footer class="footer">
    <p style="font-size: 36px;">课课达</p>
    <p style="font-size: 14px;">V0.14.15</p>
</footer>
</body>
<script src="/js/jquery.js" type="text/javascript"></script>
<script src="/js/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jquery.gritter/js/jquery.gritter.js"></script>
<script src="/js/config.js" type="text/javascript"></script>
<?php
echo isset($FOOTER_JAVASCRIPT)?$FOOTER_JAVASCRIPT:'';
?>
<script>
    $(function(){
        $(".main-warp").css('min-height',$(".main").height());
        if(typeof(kkd_init) === 'function') kkd_init();
        var localpath = window.location.pathname.toLowerCase();
        $(".navitem>li>a").each(function(i,o){
            if(o.href.toLowerCase().indexOf(localpath) > -1){
                $(o).parent().addClass('active');
                return false;
            }
        });
    });
</script>
</html>