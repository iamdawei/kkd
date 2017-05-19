<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//自定义路由规则
$route['logout'] = 'home/logout';
$route['teachers/:num'] = 'teachers/index';
$route['teachers/password/:num'] = 'teachers/update_password';
$route['assessment/:num'] = 'assessment/index';
$route['rank/:num'] = 'rank/index';
$route['role/:num'] = 'role/index';
$route['school/:num'] = 'school/index';
