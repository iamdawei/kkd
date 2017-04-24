<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 程序控制器默认入口
 * 登录，登出，分配Token
 */

class Home extends Base_Controller
{

    function __construct()
    {
        parent::$is_ajax = 0;
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('home');
        $this->load->view('footer');
    }

    public function login()
    {
        if(REQUEST_METHOD !== REQUEST_POST) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);
        $account = $this->input->post('username');
        $password = $this->input->post('password');
        $record = $this->input->post('record');
        $type = $this->input->post('type');

        if (empty($account) || empty($password)) {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }
        $password = md5($password . ENCRYPT_KEY);
        switch ($type) {
            case 't':
                //教师
                $this->_teacher_login($account,$password,$record);
                break;
            case 's' :
                //学生
                $this->_student_login($account,$password);
                break;
            default:
                $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
                break;
        }
    }

    protected function _teacher_login($account,$password,$record)
    {
        $this->load->model('teacher_model');
        $where['teacher_account'] = $account;
        $where['teacher_password'] = $password;
        $data = $this->teacher_model->get_teacher($where, 'teacher_id,teacher_name,teacher_photo,school_id');
        if ($data) {
            $sign = $this->set_kkd_token($data['teacher_id'],$data['school_id'], 't');
            $time = ($record)?(7*86400):0;
            $this->load->helper('cookie');
            set_cookie('token',$sign,$time);

            $this->ajax_return(200,MESSAGE_SUCCESS,$sign);
        }else
            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_PASSWORD);
    }

    protected function _student_login($account,$password)
    {
        $this->load->model('student_model');
        $data = $this->student_model->match_student_info($account, $password);
        if ($data) {
            $sign = $this->set_kkd_token($data['student_id'],0, 's');

            $this->ajax_return(200,MESSAGE_SUCCESS,$sign);
        }else
            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_PASSWORD);
    }

    public function logout()
    {
        session_start();
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_photo']);
        unset($_SESSION['user_type']);
        unset($_SESSION['group_model']);
        unset($_SESSION['school_id']);
        session_destroy();
        $this->load->helper('cookie');
        set_cookie('token',0,-1);
        $this->direct('/login.html');
    }

    public function teacher()
    {
        $this->load->model('school_model');
        //读取学校配置文件
        $school_id = $this->school_id;
        $va = $this->school_model->get($school_id);

        $this->load->model('auth_role_model');
        //读取角色列表
        $roles = $this->auth_role_model->get_role_list();

        $main['KKD_ROLES'] = json_encode($roles);
        $main['KKD_SCHOOL_CONFIG'] = json_encode($va);
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/icheck/skins/square/blue.css?v=1.0.2\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>
                <script type=\"text/javascript\" src=\"/js/birthday.js\"></script>
                <script type=\"text/javascript\" src=\"/js/icheck/icheck.js?v=1.0.2\"></script>";
        $this->load->view('header',$data);
        $this->load->view('teacher',$main);
        $this->load->view('footer',$data);
    }

    public function profile()
    {
        $data['FOOTER_JAVASCRIPT'] = "<script type=\"text/javascript\" src=\"/js/ajaxfileupload.js\"></script>";
        $this->load->view('header');
        $this->load->view('profile');
        $this->load->view('footer',$data);
    }

    public function assessment()
    {
        $this->load->model('auth_role_model');
        //读取角色列表
        $roles = $this->auth_role_model->get_role_list();
        $main['KKD_ROLES'] = json_encode($roles);
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/icheck/skins/square/blue.css?v=1.0.2\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>
                <script type=\"text/javascript\" src=\"/js/birthday.js\"></script>
                <script type=\"text/javascript\" src=\"/js/icheck/icheck.js?v=1.0.2\"></script>
                <script type=\"text/javascript\" src=\"/js/jquery.spinner/jquery.spinner.js\"></script>";
        $this->load->view('header',$data);
        $this->load->view('assessment',$main);
        $this->load->view('footer',$data);
    }

    public function pend()
    {
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/icheck/skins/square/blue.css?v=1.0.2\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>
                <script type=\"text/javascript\" src=\"/js/icheck/icheck.js?v=1.0.2\"></script>";
        $this->load->view('header',$data);
        $this->load->view('pend');
        $this->load->view('footer',$data);
    }

    public function role()
    {
        $this->load->view('header');
        $this->load->view('role');
        $this->load->view('footer');
    }

    //以下公开入口属于角色为普通教师身份的用户
    public function apply()
    {
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>";
        $this->load->view('header',$data);
        $this->load->view('apply');
        $this->load->view('footer',$data);
    }
}