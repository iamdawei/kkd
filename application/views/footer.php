<div class="clearfix"></div>
<footer class="footer">
<!--    <p style="font-size: 36px;">课课达</p>-->
<!--    <p style="font-size: 14px;">V0.14.15</p>-->
    <p><img src="/images/copy-right.png" /></p>
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
        var localpath = window.location.pathname.toLowerCase()+window.location.search.toLowerCase();
        $(".navitem a").each(function(i,o){
            if(o.href.toLowerCase().indexOf(localpath) > -1){
                var op = $(o).parent().parent();
                if(op.hasClass('navchild')){
                    $(o).parent().parent().parent().addClass('active show');
                }
                else{
                    $(o).parent().addClass('active');
                }
                return false;
            }
        });
    });
</script>
</html>