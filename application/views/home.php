<style type="text/css">
    .rank{width:100px;text-align: center;margin: 30px auto;display:block;float:none;}
    .rank.type{
        padding:5px 15px;
        -moz-box-shadow: 0px 5px 15px rgba(0, 120, 255,0.4);
        -webkit-box-shadow: 0px 5px 15px rgba(0, 120, 255,0.4);
        box-shadow: 0px 5px 15px rgba(0, 120, 255,0.4);
        -moz-border-radius: 15px;
        -webkit-border-radius: 15px;
        border-radius: 15px;
        background-color:#0078ff;
        color:#efefef;
    }
    .count-row{margin-right: -20px;}
    .count-item{
        margin-bottom: 20px;
        display:block;
        float:left;
    }
    .col-4{width:25%;}
    .col-2{width:50%;}
    .item-content{
        padding:15px;
        margin-right: 20px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
    }
    .item-content.bg-0:hover{
        -moz-box-shadow: 0px 5px 15px rgba(149, 215, 241,0.6);
        -webkit-box-shadow: 0px 5px 15px rgba(149, 215, 241,0.6);
        box-shadow: 0px 5px 15px rgba(149, 215, 241,0.6);
    }
    .item-content.bg-1:hover{
        -moz-box-shadow: 0px 5px 15px rgba(132, 228, 165,0.6);
        -webkit-box-shadow: 0px 5px 15px rgba(132, 228, 165,0.6);
        box-shadow: 0px 5px 15px rgba(132, 228, 165,0.6);
    }
    .item-content.bg-2:hover{
        -moz-box-shadow: 0px 5px 15px rgba(246, 157, 139,0.6);
        -webkit-box-shadow: 0px 5px 15px rgba(246, 157, 139,0.6);
        box-shadow: 0px 5px 15px rgba(246, 157, 139,0.6);
    }
    .item-content.bg-3:hover{
        -moz-box-shadow: 0px 5px 15px rgba(145, 158, 237,0.6);
        -webkit-box-shadow: 0px 5px 15px rgba(145, 158, 237,0.6);
        box-shadow: 0px 5px 15px rgba(145, 158, 237,0.6);
    }
    .bg-f{
        background-color:#ffffff;
        border:1px solid #dfdfdf;
    }
    .bg-0{
        background-color:#95D7F1;
    }
    .bg-1{
        background-color:#84E4A5;
    }
    .bg-2{
        background-color:#F69D8B;
    }
    .bg-3{
        background-color:#919EED;
    }
    .count-icon{
        color:#ffffff;
        height:180px;
    }
    .home-icon-1{background: url('/images/home-icon-1.png') no-repeat right bottom;}
    .home-icon-2{background: url('/images/home-icon-2.png') no-repeat right bottom;}
    .home-icon-3{background: url('/images/home-icon-3.png') no-repeat right bottom;}
    .home-icon-4{background: url('/images/home-icon-4.png') no-repeat right bottom;}
    .home-icon-5{background: url('/images/home-icon-5.png') no-repeat 0 center;padding-left:35px;}
    .count-icon p{padding:30px 0px 30px 30px; font-size: 24px;line-height: 60px;}
    .count-icon p font{font-size: 40px;}
    .count-icon a{color:#ffffff;font-size:16px;}
    .count-icon a:hover{color:#0078FF;}
    .item-header{
        margin: -15px 15px 0px;
        height:80px;
        line-height: 80px;
        border-bottom:1px solid #dfdfdf;
    }
    .item-header font{font-size:24px;color:#2258A0;}

    .item-header a{float:right;font-size:16px;color:#0078ff;}

    .item-top10,.kkd-chart{width:100%;height:430px;padding:0px 15px;display:block;overflow:hidden;}
    .item-top10{padding:0px;}
    .kkd-chart .none-data{text-align: center;line-height: 300px;color:#9f9f9f;}
    .item-type0,.item-type1,.item-type2{width:33.33%;display:block;float:left;}
    .item-type0,.item-type1{border-right:1px solid #efefef;}
    .item-top10 ul{padding:0px;margin-bottom:15px;height:325px;}
    .item-top10 ul li{padding:5px 20px 0 20px;font-size:14px;display:table;width:100%;text-align: center;}
    .item-top10 ul li i,.item-top10 ul li span{display:table-cell;}
    li.o1{
        color:#ef2e2f;
    }
    li.o2{
        color:#16ce56;
    }
    li.o3{
        color:#0078ff;
    }
    li.o1 i.order,li.o2 i.order,li.o3 i.order{font-size:16px;font-weight: 800;}
    li i.order{width:1em;text-align: left;}
    li i.number{width:3em;text-align: right;}
</style>
<div class="main">
    <div class="count-row">
        <div class="count-item col-4">
            <div class="item-content bg-0">
                <div class="count-icon home-icon-1">
                    <p><font><?php echo $count_result;?></font>&nbsp;&nbsp;位教师<br><a href="/Home/teacher">了解详情</a></p>
                </div>
            </div>
        </div>
        <div class="count-item col-4">
            <div class="item-content bg-1">
                <div class="count-icon home-icon-2">
                    <p><font><?php echo ($count_type['t0'])?$count_type['t0']:0;?></font>&nbsp;&nbsp;份专业资源<br><a href="/Home/source">了解详情</a></p>
                </div>
            </div>
        </div>
        <div class="count-item col-4">
            <div class="item-content bg-2">
                <div class="count-icon home-icon-3">
                    <p><font><?php echo ($count_type['t1'])?$count_type['t1']:0;?></font>&nbsp;&nbsp;份素养资源<br><a href="/Home/source">了解详情</a></p>
                </div>
            </div>
        </div>
        <div class="count-item col-4">
            <div class="item-content bg-3">
                <div class="count-icon home-icon-4">
                    <p><font><?php echo ($count_type['t2'])?$count_type['t2']:0;?></font>&nbsp;&nbsp;份学术资源<br><a href="/Home/source">了解详情</a></p>
                </div>
            </div>
        </div>
    </div>
    <div class="count-row">
        <div class="count-item col-2">
            <div class="item-content bg-f" style="padding-bottom:0px;">
                <div class="item-header">
                    <font class="home-icon-5">三项Top 10</font>
                    <a href="/Home/rank">了解详情</a>
                </div>
                <div class="item-top10">
                    <div class="item-type0">
                        <span class="rank type">专业标准</span>
                        <ul>
                            <?php
                            if(!$count_top10_0) echo "<li><i class=\"order\"></i><span class=\"name\">还没有数据</span><i class=\"number\"></i></li>";
                            foreach($count_top10_0 as $key=>$va)
                            {
                                if($key === 0) echo "<li class=\"o1\"><i class=\"order\">1</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else if($key === 1) echo "<li class=\"o2\"><i class=\"order\">2</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else if($key === 2) echo "<li class=\"o3\"><i class=\"order\">3</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else echo "<li><i class=\"order\">".($key+1)."</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="item-type1">
                        <span class="rank type">素养标准</span>
                        <ul>
                            <?php
                            if(!$count_top10_1) echo "<li><i class=\"order\"></i><span class=\"name\">还没有数据</span><i class=\"number\"></i></li>";
                            foreach($count_top10_1 as $key=>$va)
                            {
                                if($key === 0) echo "<li class=\"o1\"><i class=\"order\">1</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else if($key === 1) echo "<li class=\"o2\"><i class=\"order\">2</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else if($key === 2) echo "<li class=\"o3\"><i class=\"order\">3</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else echo "<li><i class=\"order\">".($key+1)."</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="item-type2">
                        <span class="rank type">学术标准</span>
                        <ul>
                            <?php
                            if(!$count_top10_2) echo "<li><i class=\"order\"></i><span class=\"name\">还没有数据</span><i class=\"number\"></i></li>";
                            foreach($count_top10_2 as $key=>$va)
                            {
                                if($key === 0) echo "<li class=\"o1\"><i class=\"order\">1</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else if($key === 1) echo "<li class=\"o2\"><i class=\"order\">2</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else if($key === 2) echo "<li class=\"o3\"><i class=\"order\">3</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                                else echo "<li><i class=\"order\">".($key+1)."</i><span class=\"name\">".$va['teacher_name']."</span><i class=\"number\">".$va['sum_type']."分</i></li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="count-item col-2">
            <div class="item-content bg-f" style="padding-bottom:0px;">
                <div class="item-header">
                    <font>专业标准均分线</font>
                </div>
                <div class="kkd-chart" id="container_type0"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="count-item col-2">
            <div class="item-content bg-f" style="padding-bottom:0px;">
                <div class="item-header">
                    <font>素养标准均分线</font>
                </div>
                <div class="kkd-chart" id="container_type1"></div>
            </div>
        </div>
        <div class="count-item col-2">
            <div class="item-content bg-f" style="padding-bottom:0px;">
                <div class="item-header">
                    <font>学术标准均分线</font>
                </div>
                <div class="kkd-chart" id="container_type2"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var count_chart0_avg_number = <?php echo ($count_chart0_avg_number)?$count_chart0_avg_number:0; ?>;
    var count_chart0_type_count = <?php echo ($count_chart0['type_count'])?$count_chart0['type_count']:0; ?>;
    var count_chart0_type_count1 = <?php echo ($count_chart0['type_count1'])?$count_chart0['type_count1']:0; ?>;
    var count_chart1_avg_number = <?php echo ($count_chart1_avg_number)?$count_chart1_avg_number:0; ?>;
    var count_chart1_type_count = <?php echo ($count_chart1['type_count'])?$count_chart1['type_count']:0; ?>;
    var count_chart1_type_count1 = <?php echo ($count_chart1['type_count1'])?$count_chart1['type_count1']:0; ?>;
    var count_chart2_avg_number = <?php echo ($count_chart2_avg_number)?$count_chart2_avg_number:0; ?>;
    var count_chart2_type_count = <?php echo ($count_chart2['type_count'])?$count_chart2['type_count']:0; ?>;
    var count_chart2_type_count1 = <?php echo ($count_chart2['type_count1'])?$count_chart2['type_count1']:0; ?>;
    var none_data = '<p class="none-data">还没有数据，无法生成图表</p>';

    function kkd_init()
    {
        load_charts();
    }

    function load_charts() {
        Highcharts.setOptions({
            colors: ['#D375E3', '#37DAE9', '#6EE184', '#FADD43', '#F59139', '#F67D74', '#6AF9C4', '#058DC7', '#FFF263'],
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                spacing : [20, 0 , 10, 0]
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '人数：{point.y}<br>{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        format: '<b>{point.name}</b>（{point.y}）人'
                    },
                    showInLegend: true
                }
            },
        });
        if(count_chart0_avg_number > 0){
            var data_type0 = [
                ['均分（含）以上',  count_chart0_type_count],
                ['均分以下',       count_chart0_type_count1]
            ];
            $('#container_type0').highcharts({
                series: [{
                    type: 'pie',
                    innerSize: '50%',
                    name: '占比',
                    data:data_type0
                }],
                title: {
                    text: '学校均分：'+count_chart0_avg_number
                }
            });
        }else $('#container_type0').html(none_data);

        if(count_chart1_avg_number > 0){
            var data_type1 = [
                ['均分（含）以上',  count_chart1_type_count],
                ['均分以下',       count_chart1_type_count1]
            ];
            $('#container_type1').highcharts({
                series: [{
                    type: 'pie',
                    innerSize: '50%',
                    name: '占比',
                    data:data_type1
                }],
                title: {
                    text: '学校均分：'+count_chart1_avg_number
                }
            });
        }else $('#container_type1').html(none_data);

        if(count_chart2_avg_number > 0){
            var data_type2 = [
                ['均分（含）以上',  count_chart2_type_count],
                ['均分以下',       count_chart2_type_count1]
            ];
            $('#container_type2').highcharts({
                series: [{
                    type: 'pie',
                    innerSize: '50%',
                    name: '占比',
                    data:data_type2
                }],
                title: {
                    text: '学校均分：'+count_chart2_avg_number
                }
            });
        }else $('#container_type2').html(none_data);
    }
</script>