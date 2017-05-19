<style type="text/css">
    .count-row{margin-top:20px;margin-right: -20px;}
    .count-item{
        color:#0078ff;
        margin-bottom: 20px;
        display:block;
        float:left;
    }
    .col-3{width:33.3333%;}
    .item-content{
        margin-right: 20px;
    }
    .bg-f{
        background-color:#ffffff;
    }
    .item-header{
        margin: -15px 15px 0px;
        height:80px;
        line-height: 80px;
        border-bottom:1px solid #dfdfdf;
    }
    .item-header font{font-size:24px;color:#2258A0;}
    .item-header a{float:right;font-size:16px;padding-top: 5px;color:#0078ff;}
    .item-canvas{margin:30px 15px;min-height:300px;}
    .item-canvas h3{font-size:24px;text-align: center;}
    .item-percent-number{text-align: center;margin-top: 50px;font-size: 16px;}
    .item-percent-number font{font-size: 24px;}
    .item-percent{width:100%;height:10px;background-color:#EAF3F8;margin-top: 30px;overflow:hidden;}
    .item-percent-fill{width:0px;height:10px;background-color:#00B6FF;}
    .none-data{text-align: center;line-height: 300px;color:#9f9f9f;}
</style>
<div class="main">
    <div class="count-row">
        <div class="count-item col-3">
            <div class="item-content bg-f">
                <div class="item-header">
                    <font>专业标准</font>
                    <a href="/Home/rank_info" target="_blank">了解详情</a>
                </div>
                <div class="item-canvas">
                    <h3>项目分值占比</h3>
                    <div id="container_type0"></div>
                </div>
                <div class="item-percent-number">学校均分 <font  id="chart_avg0">0</font>分&nbsp;&nbsp;&nbsp;&nbsp;个人得分 <font id="chart_count0">0</font>分</div>
                <div class="item-percent">
                    <div class="item-percent-fill"></div>
                </div>
            </div>
        </div>
        <div class="count-item col-3">
            <div class="item-content bg-f">
                <div class="item-header">
                    <font>素养标准</font>
                    <a href="/Home/rank_info?t=1" target="_blank">了解详情</a>
                </div>
                <div class="item-canvas">
                    <h3>项目分值占比</h3>
                    <div id="container_type1"></div>
                </div>
                <div class="item-percent-number">学校均分 <font  id="chart_avg1">0</font>分&nbsp;&nbsp;&nbsp;&nbsp;个人得分 <font id="chart_count1">0</font>分</div>
                <div class="item-percent">
                    <div class="item-percent-fill"></div>
                </div>
            </div>
        </div>
        <div class="count-item col-3">
            <div class="item-content bg-f">
                <div class="item-header">
                    <font>学术标准</font>
                    <a href="/Home/rank_info?t=2" target="_blank">了解详情</a>
                </div>
                <div class="item-canvas">
                    <h3>项目分值占比</h3>
                    <div id="container_type2"></div>
                </div>
                <div class="item-percent-number">学校均分 <font  id="chart_avg2">0</font>分&nbsp;&nbsp;&nbsp;&nbsp;个人得分 <font id="chart_count2">0</font>分</div>
                <div class="item-percent">
                    <div class="item-percent-fill"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var count_chart0_avg_number = <?php echo ($count_chart0_avg_number)?$count_chart0_avg_number:0; ?>;
    var count_chart1_avg_number = <?php echo ($count_chart1_avg_number)?$count_chart1_avg_number:0; ?>;
    var count_chart2_avg_number = <?php echo ($count_chart2_avg_number)?$count_chart2_avg_number:0; ?>;
    var count_chart = <?php echo $count_chart_all; ?>;
    var none_data = '<p class="none-data">还没有数据，无法生成图表</p>';
    var chart_data0 = [];
    var chart_data1 = [];
    var chart_data2 = [];
    var chart_count0 = 0;
    var chart_count1 = 0;
    var chart_count2 = 0;
    function init_count_data()
    {
        if(count_chart.length == 0) return;
        for(var i =0 ;i< count_chart.length;i++)
        {
            if(count_chart[i]['assessment_type'] == 0){
                var temp_arr = new Array(2);
                temp_arr[0] = count_chart[i]['assessment_name'];
                temp_arr[1] = parseInt(count_chart[i]['sum_type']);
                chart_data0.push(temp_arr);
                chart_count0 += temp_arr[1];
            }
            else if(count_chart[i]['assessment_type'] == 1){
                var temp_arr = new Array(2);
                temp_arr[0] = count_chart[i]['assessment_name'];
                temp_arr[1] = parseInt(count_chart[i]['sum_type']);
                chart_data1.push(temp_arr);
                chart_count1 += temp_arr[1];
            }
            else if(count_chart[i]['assessment_type'] == 2){
                var temp_arr = new Array(2);
                temp_arr[0] = count_chart[i]['assessment_name'];
                temp_arr[1] = parseInt(count_chart[i]['sum_type']);
                chart_data2.push(temp_arr);
                chart_count2 += temp_arr[1];
            }
        }
        $("#chart_count0").text(chart_count0);
        $("#chart_count1").text(chart_count1);
        $("#chart_count2").text(chart_count2);
        $("#chart_avg0").text(count_chart0_avg_number);
        $("#chart_avg1").text(count_chart1_avg_number);
        $("#chart_avg2").text(count_chart2_avg_number);
    }
    function kkd_init()
    {
        init_count_data();
        var ipf = $(".item-percent-fill");
        var f_0 = chart_count0/count_chart0_avg_number * 100;
        var f_1 = chart_count1/count_chart1_avg_number * 100;
        var f_2 = chart_count2/count_chart2_avg_number * 100;
        ipf.eq(0).animate({
            width: f_0.toFixed(2) + "%"
        }, 1000 );
        ipf.eq(1).animate({
            width: f_1.toFixed(2) + "%"
        }, 1000 );
        ipf.eq(2).animate({
            width: f_2.toFixed(2) + "%"
        }, 1000 );
        load_charts();
    }

    function load_charts () {
        Highcharts.setOptions({
            colors: ['#D375E3', '#37DAE9', '#6EE184', '#FADD43', '#F59139', '#F67D74', '#6AF9C4', '#058DC7', '#FFF263'],
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                spacing : [20, 0 , 10, 0]
            },
            title: {
                floating:true,
                text: ' ',
                margin:30
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '{series.name} {point.y} <br>占比<b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
        });
        $('#container_type0').highcharts({
            series: [{
                type: 'pie',
                innerSize: '50%',
                name: '得分',
                data:chart_data0
            }]
        });
        if(chart_data0.length == 0) $('#container_type0').html(none_data);
        $('#container_type1').highcharts({
            series: [{
                type: 'pie',
                innerSize: '50%',
                name: '分值占比',
                data:chart_data1
            }]
        });
        if(chart_data1.length == 0) $('#container_type1').html(none_data);
        $('#container_type2').highcharts({
            series: [{
                type: 'pie',
                innerSize: '50%',
                name: '分值占比',
                data:chart_data2
            }]
        });
        if(chart_data2.length == 0) $('#container_type2').html(none_data);
    }

</script>