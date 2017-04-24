<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//自定义路由规则
$route['logout'] = 'home/logout';
$route['teachers/:num'] = 'teachers/index/teacher_id/$1';
$route['teachers/password/:num'] = 'teachers/update_password/teacher_id/$1';
$route['assessment/:num'] = 'assessment/index/assessment_id/$1';
$route['assessment/open/:num'] = 'assessment/open/assessment_id/$1';
$route['assessment/item/:num'] = 'assessment/item/assessment_item_id/$1';
$route['assessment/check/:num'] = 'assessment/check/assessment_item_id/$1';