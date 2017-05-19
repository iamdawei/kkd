<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 个人中心控制器
 * 包括个人信息、修改密码、上传头像
 */
class User extends API_Conotroller
{
    protected $user_type = '';
    protected $user_id = '';
    function __construct()
    {
        parent::__construct();
        $this->user_type = $this->HTTP_TOKEN_SIGN['type'];
        $this->user_id = $this->HTTP_TOKEN_SIGN['uid'];
    }

    public function index()
    {
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $this->user_info();
                break;
            case REQUEST_POST:
                $this->user_photo_upload();
                break;
            case REQUEST_PUT :
                $this->user_password_change();
                break;
        }
    }

    protected function user_info()
    {
        switch ($this->user_type) {
            case 't' :
                $where['teacher_id'] = $this->user_id;
                $this->load->model('teacher_model');
                $data = $this->teacher_model->get_teacher($where, 'teacher_name,teacher_role,teacher_gender,teacher_subject,teacher_class,teacher_born_date,teacher_indution_date,teacher_account,teacher_photo,school_name');
                if ($data) {
                    $this->ajax_return(200, MESSAGE_SUCCESS, $data);
                } else{
                    $this->ajax_return(404, MESSAGE_ERROR_NON_DATA);
                }

            case 's' :
                $where['student_id'] = $this->user_id;
                $this->load->model('student_model');
                $data = $this->student_model->get_student($where, 'student_name,student_role,student_grade,student_class,student_indution_date,student_account,student_photo');
                if ($data) {
                    $this->ajax_return(200, MESSAGE_SUCCESS, $data);
                } else{
                    $this->ajax_return(404, MESSAGE_ERROR_NON_DATA);
                }

        }
    }


    protected function user_password_change()
    {
        switch ($this->user_type) {
            case 't' :
                $where['teacher_password'] =md5($this->input->input_stream('password').ENCRYPT_KEY);
                $where['teacher_id'] = $this->user_id;
                $this->load->model('teacher_model');

                //匹配个人信息；
                $data = $this->teacher_model->get_teacher($where,'teacher_name');

                if(empty($data))
                {
                    $this->ajax_return(400, MESSAGE_ERROR_CHANGE_PASSWORD);
                }

                $newpassword = $this->input->input_stream('newpassword');
                $confirm = $this->input->input_stream('confirm');

                if($newpassword != $confirm){

                    $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);
                }

                $newpassword = md5($newpassword.ENCRYPT_KEY);
                $res = $this->teacher_model->change_teacher_password($where['teacher_id'],$newpassword);

                if ($res < 0) {

                    $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);

                } else {
                    $this->ajax_return(200, MESSAGE_SUCCESS);
                }


            case 's' :
                $where['student_password'] =md5($this->input->input_stream('password').ENCRYPT_KEY);
                $where['student_id'] = $this->user_id;
                $this->load->model('student_model');

                //匹配信息；
                $data = $this->student_model->get_student($where,'student_name');

                if(empty($data))
                {
                    $this->ajax_return(400, MESSAGE_ERROR_ACCOUNT_PASSWORD);
                }

                $newpassword = $this->input->input_stream('newpassword');
                $confirm = $this->input->input_stream('confirm');

                if($newpassword != $confirm){

                    $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);
                }

                $newpassword = md5($newpassword.ENCRYPT_KEY);
                $res = $this->student_model->change_student_password($where['student_id'],$newpassword);
                if ($res < 0) {
                    $this->ajax_return(400,MESSAGE_ERROR_PARAMETER );

                } else {
                    $this->ajax_return(200, MESSAGE_SUCCESS);
                }

        }
    }

    protected function user_photo_upload()
    {
        switch ($this->user_type) {
            case 't' :
                if(! file_exists("./upload/teacher"))
                {
                    mkdir("./upload/teacher",0777,true);    //make_filed_to_save
                }

                $config['upload_path'] = "./upload/teacher";//upload_save_filed
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $this->user_id;
                $config['max_size'] = 50;
                $config['overwrite'] = true;
                $this->load->library('upload',$config);
                $res = $this->upload->do_upload('photo'); //upload

                if(! $res)
                {
                    $this->ajax_return(400,$this->upload->error_msg[0]);
                }
                $data = $this->upload->data();
                $photo['teacher_photo'] = "/upload/teacher/".$data['file_name']."?".time();

                //写入数据库
                $this->load->model('teacher_model');
                $res = $this->teacher_model->update_teacher_info($this->user_id,$photo);
                if($res < 0 ){
                    $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);
                }
                $this->ajax_return(200, MESSAGE_SUCCESS,$photo['teacher_photo']);

            case 's' :

                if(! file_exists("./upload/student"))
                {
                    mkdir("./upload/student",0777,true);    //make_filed_to_save
                }

                $config['upload_path'] = "./upload/student";//upload_save_filed
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $this->user_id;
                $config['max_size'] = 50;
                $config['overwrite'] = true;
                $this->load->library('upload',$config);
                $res = $this->upload->do_upload('photo'); //upload

                if(! $res)
                {
                    $this->ajax_return(400,$this->upload->error_msg[0]);
                }

                $data = $this->upload->data();

                $photo['student_photo'] = "/upload/student/small_thumb/".$data['file_name']."?".time();

                //写入数据库
                $this->load->model('student_model');
                $res = $this->student_model->update_student_info($this->user_id,$photo);
                if($res < 0 ){
                    $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);
                }
                $this->ajax_return(200, MESSAGE_SUCCESS,$photo['student_photo']);
        }

    }

}