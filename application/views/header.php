<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="shortcut icon" href="">
    <title>空白模板页面</title>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/assets/js/html5shiv.js"></script>
    <script src="/assets/js/respond.min.js"></script>
    <![endif]-->
    <?php
    echo isset($HEADER_CSS)?$HEADER_CSS:'';
    ?>
    <link href="/css/common.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/js/jquery.gritter/css/jquery.gritter.css" />
</head>

<body>
<header class="header">
    <div class="logo">课课达</div>
    <?php if(isset($read_count)){
        if($read_count) echo "<a class=\"message\" href=\"/Home/message\"><img src=\"/images/common/icon-message.png\" /><span class=\"bubble\">".$read_count."</span></a>";
        else echo "<a class=\"message\" href=\"/Home/message\"><img src=\"/images/common/icon-message.png\" /></a>";
    }?>
    <div id="top-profile" class="top-profile">
        <a class="top-profile-user" href="javascript:void(0);">
            <img class="top-profile-user-img" src="<?php echo $_SESSION['user_photo']; ?>" width="50px" height="50px">
            <span><?php echo $_SESSION['user_name']; ?></span>
        </a>
        <div class="top-profile-popover">
            <a href="/Home/profile"><img src="/images/common/settings.png" width="24px" height="24px">&nbsp;&nbsp;个人设置</a>
            <a href="/logout"><img src="/images/common/exit.png" width="22px" height="24px">&nbsp;&nbsp;退&nbsp;出</a>
        </div>
    </div>
</header>
<nav class="nav">
    <div class="nav-profile"><img src="<?php echo $_SESSION['user_photo']; ?>" width="120px" height="120px"><p><span style="font-size: 20px;">welcome</span><br /><?php echo $_SESSION['user_name']; ?></p></div>
    <ul class="navitem">
        <?php
        $headerMenu = $_SESSION['group_model'];
        $group_model_name = array_unique(array_column($headerMenu,'group_model_name'));
        $group_name = array_unique(array_column($headerMenu,'group_name'));
        foreach($group_model_name as $gmn)
        {
            switch($gmn){
                case '教务管理':
                    echo "<li class=\"model\">教务管理</li>";
                    foreach($group_name as $menu){
                        switch($menu){
                            case '教师管理':
                                echo "<li><a href=\"/Home/teacher\" class=\"nav-icon-teacher\">教师管理</a></li>";
                                break;
                            case '我的课表':
                                echo "<li><a href=\"javascript:void(0);\" class=\"nav-icon-table\">我的课表</a></li>";
                                break;
                            case '学籍管理':
                                echo "<li><a href=\"javascript:void(0);\" class=\"nav-icon-schoolroll\">学籍管理</a></li>";
                                break;
                            case '一键排课':
                                echo "<li><a href=\"javascript:void(0);\" class=\"nav-icon-schedule\">一键排课</a></li>";
                                break;
                        }
                    }
                    break;
                case '评估中心':
                    echo "<li class=\"model\"><a href=\"/Home\">评估中心</a></li>";
                    foreach($group_name as $menu){
                        switch($menu){
                            case '考核标准':
                                echo "<li><a href=\"/Home/assessment\" class=\"nav-icon-assessment\">考核标准</a></li>";
                                break;
                            case '用户设置':
                                echo "<li><a href=\"/Home/teacher\" class=\"nav-icon-user\">用户设置</a></li>";
                                break;
                            case '角色管理':
                                echo "<li><a href=\"/Home/role\" class=\"nav-icon-role\">角色管理</a></li>";
                                break;
                            case '待审列表':
                                echo "<li><a href=\"/Home/pend\" class=\"nav-icon-status\">待审列表</a></li>";
                                break;
                            case '资源中心':
                                echo "<li><a href=\"javascript:void(0);\" class=\"nav-icon-source\">资源中心</a></li>";
                                break;
                            case '排行榜':
                                echo "<li><a href=\"/Home/rank\" class=\"nav-icon-order\">排行榜</a></li>";
                                break;
                            //教师身份的权限菜单
                            case '申请列表':
                                echo "<li><a href=\"/Home/apply\" class=\"nav-icon-assessment\">申请列表</a></li>";
                                break;
                            case '考核申请':
                                $ass_menu = $_SESSION['assessment_menu'];
                                $ass0 =[];
                                $ass1 = [];
                                $ass2 = [];
                                foreach($ass_menu as $a_m)
                                {
                                    //[0专业,1素养,2学术]
                                    if($a_m->assessment_type === '0'){
                                        $ass0[] = "<li><a href=\"/Home/item?type=0&sid=$a_m->assessment_set_id\">$a_m->assessment_name</a></li>";
                                    }else if($a_m->assessment_type === '1')
                                    {
                                        $ass1[] = "<li><a href=\"/Home/item?type=1&sid=$a_m->assessment_set_id\">$a_m->assessment_name</a></li>";
                                    }
                                    else if($a_m->assessment_type === '2')
                                    {
                                        $ass2[] = "<li><a href=\"/Home/item?type=2&sid=$a_m->assessment_set_id\">$a_m->assessment_name</a></li>";
                                    }
                                }
                                //var_dump($ass1);
                                if($ass0){
                                    $child = implode('',$ass0);
                                    echo "<li><a href=\"javascript:void(0);\" onclick=\"kkd_show_child(this)\" class=\"nav-icon-ass-0\">专业标准<i class=\"icon-child\"></i></a><ul class=\"navchild\">$child</ul></li>";
                                }
                                if($ass1){
                                    $child = implode('',$ass1);
                                    echo "<li><a href=\"javascript:void(0);\" onclick=\"kkd_show_child(this)\" class=\"nav-icon-ass-1\">素养标准<i class=\"icon-child\"></i></a><ul class=\"navchild\">$child</ul></li>";
                                }
                                if($ass2){
                                    $child = implode('',$ass2);
                                    echo "<li><a href=\"javascript:void(0);\" onclick=\"kkd_show_child(this)\" class=\"nav-icon-ass-2\">学术标准<i class=\"icon-child\"></i></a><ul class=\"navchild\">$child</ul></li>";
                                }
                                break;
                        }
                    }
                    break;
                case '平台管理':
                    echo "<li><a href=\"javascript:void(0);\"><span>平台管理</span></a></li>";
                    break;
            }
        }
        ?>
    </ul>
</nav>
