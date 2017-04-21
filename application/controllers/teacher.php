<?php
/**
 * 教师用户控制器
 * 主要包括：
 *  教师自己的信息CRUD操作
 * 权限：
 *   必须验证Token
 */

class Teacher extends Base_Controller
{
    protected $teacher_id = 0;
    function __construct()
    {
        parent::__construct();
        $this->load->model('teacher_model');
    }

    public function index()
    {
        $this->teacher_id = $this->uri->segment(2,0);

        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                if(!$this->teacher_id) $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
                $this->teacher_info();
                break;
            case REQUEST_POST :
                $this->add_teacher();
                break;
            case REQUEST_DELETE :
                if(!$this->teacher_id) $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
                $this->delete_teacher();
                break;
            case REQUEST_PUT :
                if(!$this->teacher_id) $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
                $this->update_teacher();
                break;
        }
    }

    public function mytable()
    {
        $data['HEADER_CSS']="<link rel=\"stylesheet\" href=\"".KKD_HOST."/src/styles/main/mytable.css\">";
        $this->load->view('header',$data);
        $this->load->view('mytable');
        $data['FOOTER_JAVASCRIPT']="<script src=\"".KKD_HOST."/src/scripts/js/mytable.js\"></script>";
		$this->load->view('footer',$data);
    }
}