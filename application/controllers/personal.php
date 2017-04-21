<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 程序控制器默认入口
 * 登录，登出，分配Token
 */

class Personal extends Base_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['HEADER_CSS']="<link rel=\"stylesheet\" href=\"".KKD_HOST."/src/styles/main/personalInfo.css\">";
        $this->load->view('header',$data);
        $this->load->view('personal');
        $this->load->view('footer');
    }
}