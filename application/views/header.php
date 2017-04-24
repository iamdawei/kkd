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
        <li>
            <a href="javascript:void(0);" onclick="kkd_show_child(this)" class=\"nav-icon-status\">专业标准标准<i class="icon-child"></i></a>
            <ul class="navchild">
                <li><a href="javascript:void(0);">教育教学示范课</a></li>
                <li><a href="javascript:void(0);">教育教学示范课</a></li>
            </ul>
        </li>
        <li class="icon-child">
            <a href="javascript:void(0);" onclick="kkd_show_child(this)" class=\"nav-icon-status\">素养标准<i class="icon-child"></i></a>
            <ul class="navchild">
                <li><a href="javascript:void(0);">教育教学示范课</a></li>
                <li><a href="javascript:void(0);">教育教学示范课</a></li>
            </ul>
        </li>
        <li class="icon-child">
            <a href="javascript:void(0);" onclick="kkd_show_child(this)" class=\"nav-icon-status\">学术标准<i class="icon-child"></i></a>
            <ul class="navchild">
                <li><a href="javascript:void(0);">教育教学示范课</a></li>
                <li><a href="javascript:void(0);">教育教学示范课</a></li>
            </ul>
        </li>
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
                    echo "<li class=\"model\">评估中心</li>";
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
                                echo "<li><a href=\"javascript:void(0);\" class=\"nav-icon-order\">排行榜</a></li>";
                                break;

                            //教师身份的权限菜单
                            case '申请列表':
                                echo "<li><a href=\"/Home/apply\" class=\"nav-icon-status\">申请列表</a></li>";
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
