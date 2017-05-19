<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="shortcut icon" href="">
    <title>领袖教师</title>
    <script src="/assets/js/html5shiv.js"></script>
    <script src="/assets/js/respond.min.js"></script>
    <?php
    echo isset($HEADER_CSS)?$HEADER_CSS:'';
    ?>
    <link href="/css/common.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/js/jquery.gritter/css/jquery.gritter.css" />
</head>

<body>
<!--[if lt IE 9]>
<style>
    html,body{overflow:hidden;}
</style>
<div class="kkd-dialog-shadow"></div>
<div class="kkd-dialog-wrap" style="top:50px;">
    <div class="kkd-dialog-container">
    <div class="kkd-dialog-content table-style">
        <div style="text-align: center;"><p>你的浏览器实在<strong>太....太旧了</strong>，放学别走，升级完浏览器再说</p><br/>
        <a target="_blank" class="btn btn-primary" href="http://browsehappy.com">立即升级</a></div>
    </div>
    </div>
</div>
<![endif]-->
<header class="header">
    <div class="logo"><img src="/images/logo.png" height="60px" /></div>
    <?php if(isset($_SESSION['read_count'])){
        if($_SESSION['read_count']) echo "<a class=\"message\" href=\"/Home/message\"><img src=\"/images/common/icon-message.png\" /><span class=\"bubble\">".$_SESSION['read_count']."</span></a>";
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
        $user_role_type = $_SESSION['user_role_type'];
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
                    if($user_role_type == 'hybrid')
                    {
                        echo "<li class=\"model s-18\"><a href=\"/Home\">我的评估中心</a></li>";
                        echo "<li class=\"model s-18\"><a href=\"/Home?all\">学校评估中心</a></li>";
                    }
                    else
                        echo "<li class=\"model\"><a href=\"/Home\">评估中心</a></li>";
                    foreach($group_name as $menu){
                        switch($menu){
                            case '待审列表':
                                echo "<li><a href=\"/Home/pend\" class=\"nav-icon-status\">待审列表</a></li>";
                                break;
                            case '资源中心':
                                echo "<li><a href=\"/Home/source\" class=\"nav-icon-source\">资源中心</a></li>";
                                break;
                            case '排行榜':
                                echo "<li><a href=\"/Home/rank\" class=\"nav-icon-order\">排行榜</a></li>";
                                break;
                            //教师身份的权限菜单
                            case '申请列表':
                                echo "<li><a href=\"/Home/apply\" class=\"icon-assessment-list\">申请列表</a></li>";
                                break;
                            case '考核申请':
                                $ass_menu = $_SESSION['assessment_menu'];
                                $ass0 =[];
                                $ass1 = [];
                                $ass2 = [];
                                foreach($ass_menu as $a_m)
                                {
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
                case '基础配置':
                    echo "<li><a href=\"javascript:void(0);\" onclick=\"kkd_show_child(this)\" class=\"icon-base-setting\">基础配置<i class=\"icon-child\"></i></a><ul class=\"navchild navchild-icons\">";
                    echo "<li><a href=\"/Home/role\" class=\"nav-icon-role\">角色管理</a></li>";
                    echo "<li><a href=\"/Home/teacher\" class=\"nav-icon-user\">用户设置</a></li>";
                    echo "<li><a href=\"/Home/assessment\" class=\"nav-icon-assessment\">考核标准</a></li>";
                    echo "</ul></li>";
                    break;
            }
        }
        ?>
    </ul>
</nav>
