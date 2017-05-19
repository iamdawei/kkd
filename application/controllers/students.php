<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 学生控制器
 * 主要包括：
 *  学生信息CRUD操作
 */

class Students extends API_Conotroller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('student_model');
    }

    public function index()
    {
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $student_id = intval($this->uri->segment(2,0));
                if($student_id){$this->student_item($student_id);}
                $this->student_list();
                break;
            case REQUEST_POST :
                $this->student_add();
                break;
            case REQUEST_DELETE :
                $this->student_delete();
                break;
            case REQUEST_PUT :
                $this->student_update();
                break;
        }
    }

    protected function student_item($student_id)
    {
        $where['student_id'] = $student_id;
        $data = $this->student_model->get_student($where);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }


    protected function student_list()
    {
        $school_id = $this->school_id;
        $page = intval($this->input->get('page'));
        $student_grade = intval($this->input->get('student_grade'));
        $student_class = intval($this->input->get('student_class'));
        $keywords = $this->input->get('keywords');
        //确定每页显示，初始化总条数；
        $limit = 12;
        $total = 0;

        if(empty($page)) { $page = 1; } //默认起始页；
        // 返回数组；
        $studentlist = array();
        $studentlist['data'] = $this->student_model->get_student_list($school_id, $student_grade,$student_class,$keywords,$page, $limit, $total);

        // 返回总条数
        $studentlist['total'] = $total;

        // 返回当前页
        $studentlist['current_page'] = $page;

        // 返回总页数
        $studentlist['total_page'] = ceil($total / $limit);

        $this->ajax_return(200,MESSAGE_SUCCESS,$studentlist);
    }

    protected function student_add()
    {
        $data = array(
            'student_account' => $this->input->post('student_account'),
            'student_register_number' => $this->input->post('student_register_number'),

            //新增人员默认密码；
            'student_password' => md5('000000'.ENCRYPT_KEY),

            'student_name' => $this->input->post('student_name'),
            'student_gender' => $this->input->post('student_gender'),
            'student_born_date' => $this->input->post('student_born_date'),
            'student_grade' => intval($this->input->post('student_grade')),
            'student_class' => intval($this->input->post('student_class')),
            'student_indution_date' => $this->input->post('student_indution_date'),

            //默认新增时间为当前时间；
            'student_register_datetime' => date('Y-m-d h-i-s'),

            'student_status' => $this->input->post('student_status'),
            'student_parent' => $this->input->post('student_parent'),
            'student_parent_phone' => $this->input->post('student_parent_phone'),
            'student_backup' => $this->input->post('student_backup'),
            'student_backup_phone' => $this->input->post('student_backup_phone'),
        );

        if($this->student_model->check_account_unique($data['student_account'])) {

            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_UNIQUE);
        }

        $res = $this->student_model->add_student($data);

        if (!$res)
        {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    protected function student_delete()
    {
        $student_id = intval($this->uri->segment(2,0));
        $res = $this->student_model->delete_student($student_id);

        if ($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    protected function student_update()
    {
        $student_id = intval($this->uri->segment(2,0));
        if(! $student_id){ $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);}
        $data = array(
            'student_account' => $this->input->input_stream('student_account'),
            'student_register_number' => $this->input->input_stream('student_register_number'),
            'student_name' => $this->input->input_stream('student_name'),
            'student_gender' => $this->input->input_stream('student_gender'),
            'student_born_date' => $this->input->input_stream('student_born_date'),
            'student_grade' => $this->input->input_stream('student_grade'),
            'student_class' => $this->input->input_stream('student_class'),
            'student_indution_date' => $this->input->input_stream('student_indution_date'),
            'student_status' => $this->input->input_stream('student_status'),
            'student_parent' => $this->input->input_stream('student_parent'),
            'student_parent_phone' => $this->input->input_stream('student_parent_phone'),
            'student_backup' => $this->input->input_stream('student_backup'),
            'student_backup_phone' => $this->input->input_stream('student_backup_phone'),
        );
        if($this->student_model->check_account_unique($data['student_account'],$student_id)){

            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_UNIQUE);
        }
        $res = $this->student_model->update_student_info($student_id,$data);

        if($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }
        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    public function student_photo_upload()
    {
        $student_id = intval($this->input->post('student_id'));
        if(! file_exists("./upload/student"))
        {

            mkdir("./upload/student",0777,true);    //make_filed_to_save

        }

        $config['upload_path'] = "./upload/student"; //upload_save_filed
        $config['allowed_types'] = 'gif|jpg|png';
        $config['file_name'] = $student_id."?".time();
        $config['max_size'] = '20000';
        $this->load->library('upload',$config);
        $res = $this->upload->do_upload('student_photo'); //upload

        if(! $res)
        {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER,"wer");
        }

        //make_small_thumb;
        if(! file_exists("./upload/student/small_thumb")){

            mkdir("./upload/student/small_thumb",0777,true);

        }

        $data = $this->upload->data();

        $config_small_thumb = array(
            'image_library' => 'gd2',       //image_sdk;
            'source_image'  => $data['full_path'],
            'new_image'     => "./upload/student/small_thumb/",
            'create_thumb'  => true,       //sure_to_make
            'maintain_ratio'=> true,
            'width'         => 198,        //to_be_official_size
            'height'        => 300,
            'thumb_marker'  => ""
        );

        $this->load->library('image_lib',$config_small_thumb);
        $this->image_lib->initialize($config_small_thumb);
        $this->image_lib->resize();       //make_small_thumb

        $img['student_photo'] = KKD_HOST."/upload/student/small_thumb/".$data['file_name'];

        //写入数据库
        $res = $this->student_model->update_student_info($student_id,$img);
        if($res < 0 ){
            $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);
        }
        $this->ajax_return(200,MESSAGE_SUCCESS,$img['student_photo']);
    }


    public function student_password_reset()
    {
        if(REQUEST_METHOD != REQUEST_PUT ) $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        $student_id = intval($this->input->input_stream('student_id'));
        $res = $this->student_model->reset_student_password($student_id);
        if($res)
        {
            $this->ajax_return(200, MESSAGE_SUCCESS);
        }
    }

}