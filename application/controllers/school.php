<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 学校管理
 * 超级管理：对学校进行CRUD操作
 * 其他权限：读取学校config配置
 */

class School extends Base_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('school_model');
    }

    public function index()
    {

    }

    public function config()
    {
        //读取学校配置文件
        $school_id = $this->school_id;
        $va = $this->school_model->get($school_id);

        $this->ajax_return(200,MESSAGE_SUCCESS,$va);
    }
}