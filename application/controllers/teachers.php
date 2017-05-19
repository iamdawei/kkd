<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 教师控制器
 * 主要包括：
 *  教师信息CRUD操作
 */

class Teachers extends API_Conotroller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('teacher_model');
    }

    public function index()
    {
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $teacher_id = $this->uri->segment(2,0);
                if($teacher_id) $this->teacher_item($teacher_id);
                else $this->teacher_list();
                break;
            case REQUEST_POST :
                $this->teacher_add();
                break;
            case REQUEST_DELETE :
                $this->teacher_delete();
                break;
            case REQUEST_PUT :
                $this->teacher_update();
                break;
        }
    }

    protected function teacher_item($teacher_id)
    {
        $where['teacher_id'] = $teacher_id;
        $data = $this->teacher_model->get_teacher($where);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }

    protected function teacher_list()
    {

        $school_id = $this->school_id;
        $page = intval($this->input->get('page'));
        $teacher_class = $this->input->get('teacher_class');
        $teacher_subject = $this->input->get('teacher_subject');
        $keywords = $this->input->get('keywords');
        $teacher_role = $this->input->get('teacher_role');

        //确定每页显示，初始化总条数；
        $limit = 12;
        $total = 0;

        if(empty($page)) { $page = 1; } //默认起始页；

        $teacherlist = array();  // 返回数组；
        $teacherlist['data'] = $this->teacher_model->get_teacher_list($school_id, $teacher_role,$teacher_class,$teacher_subject,$keywords,$page, $limit, $total);
        $teacherlist['total'] = $total;       // 返回总条数
        $teacherlist['current_page'] = $page;    // 返回当前页
        $teacherlist['total_page'] = ceil($total / $limit);     // 返回总页数

        $this->ajax_return(200,MESSAGE_SUCCESS,$teacherlist);
    }

    protected function teacher_add()
    {
        $data = array(
            'teacher_account' => $this->input->post('teacher_account'),
            'teacher_password' => md5(DEFAULT_PASSWORD.ENCRYPT_KEY),
            'teacher_name' => $this->input->post('teacher_name'),
            'teacher_gender' => $this->input->post('teacher_gender'),
            'teacher_subject' => $this->input->post('teacher_subject'),
            'teacher_email' => $this->input->post('teacher_email'),
            'teacher_class' => $this->input->post('teacher_class'),
            'teacher_role' => $this->input->post('teacher_role'),
            'teacher_indution_date' => $this->input->post('teacher_indution_date'),
            'teacher_photo' => $this->input->post('teacher_photo'),
            'teacher_born_date' => $this->input->post('teacher_born_date'),
            'teacher_register_datetime' => date('Y-m-d H-i-s'),
            'school_id' => $this->school_id
        );

        if($this->teacher_model->check_account_unique($data['teacher_account'])) {

            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_UNIQUE);
        }

        $teacher_id = $this->teacher_model->add_teacher($data);
        $class = explode(',',$data['teacher_class']);
        if($data['teacher_class'] && count($class)>0)
        {
            $class_array = array();
            foreach($class as $Value) {
                $ex_value = explode(':', $Value);
                array_push($class_array, array(
                    'teacher_id' => $teacher_id,
                    'grade_number' => $ex_value[0],
                    'class_number' => $ex_value[1],
                ));
            }
            $this->teacher_model->set_teacher_class($teacher_id,$class_array,false);
        }

        $for_datas = explode(',',$data['teacher_role']);
        if($data['teacher_role'] && count($for_datas)>0){
            $role_data  = array();
            foreach($for_datas as $tempValue){
                $ex_temp = explode(':',$tempValue);
                array_push($role_data,array(
                    'teacher_id' => $teacher_id,
                    'role_id' => $ex_temp[0]
                ));
            }
            $this->load->model('auth_role_model');
            $this->auth_role_model->set_teacher_role($teacher_id,$role_data,'batch',false);
        }

        if (!$teacher_id)
        {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    protected function teacher_delete()
    {
        $user_id = $this->HTTP_TOKEN_SIGN['uid'];
        $teacher_id = intval($this->uri->segment(2,0));
        $this->load->model('auth_role_model');
        if($user_id == $teacher_id){$this->ajax_return(400,MESSAGE_ERROR_SELF_INFO);}
        $this->auth_role_model->delete_teacher_role($teacher_id);
        $res = $this->teacher_model->delete_teacher($teacher_id);
        $this->teacher_model->delete_teacher_class($teacher_id);
        if ($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    protected function teacher_update()
    {
        $teacher_id = $this->uri->segment(2,0);
        if(!$teacher_id) $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        $data = array(
            'teacher_account' => $this->input->input_stream('teacher_account'),
            'teacher_name' => $this->input->input_stream('teacher_name'),
            'teacher_gender' => $this->input->input_stream('teacher_gender'),
            'teacher_subject' => $this->input->input_stream('teacher_subject'),
            'teacher_email' => $this->input->input_stream('teacher_email'),
            'teacher_class' => $this->input->input_stream('teacher_class'),
            'teacher_role' => $this->input->input_stream('teacher_role'),
            'teacher_indution_date' => $this->input->input_stream('teacher_indution_date'),
            'teacher_photo' => $this->input->input_stream('teacher_photo'),
            'teacher_born_date' => $this->input->input_stream('teacher_born_date'),
            'school_id' => $this->school_id
        );

        if($this->teacher_model->check_account_unique($data['teacher_account'],$teacher_id)){
            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_UNIQUE);
        }
        //更新前检查用户权限是否变更
        $where['teacher_id'] = $teacher_id;
        $va = $this->teacher_model->get_teacher($where,'teacher_role,teacher_class');
        if($va['teacher_role'] != $data['teacher_role']){
            $this->load->model('auth_role_model');
            $for_datas = explode(',',$data['teacher_role']);
            if($data['teacher_role'] && count($for_datas)>0){
                $update_data  = array();
                foreach($for_datas as $tempValue){
                    $ex_temp = explode(':',$tempValue);
                    array_push($update_data,array(
                        'teacher_id' => $teacher_id,
                        'role_id' => $ex_temp[0]
                    ));
                }
                $this->auth_role_model->set_teacher_role($teacher_id,$update_data);
            }
            else $this->auth_role_model->delete_teacher_role($teacher_id);
        }

        if($va['teacher_class'] != $data['teacher_class']) {
            $class = explode(',', $data['teacher_class']);
            if($data['teacher_class'] && count($class)>0) {
                $class_array = array();
                foreach ($class as $Value) {
                    $ex_value = explode(':', $Value);
                    array_push($class_array, array(
                        'teacher_id' => $teacher_id,
                        'grade_number' => $ex_value[0],
                        'class_number' => $ex_value[1]
                    ));
                }

                if ($class_array) $this->teacher_model->set_teacher_class($teacher_id, $class_array);
            }
            else $this->teacher_model->delete_teacher_class($teacher_id);
        }

        $res = $this->teacher_model->update_teacher_info($teacher_id,$data);

        if($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    public function update_password()
    {
        //DELETE 重置密码
        //PUT 更新密码
        if(REQUEST_METHOD != REQUEST_DELETE ) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);
        $teacher_id = $this->uri->segment(3,0);
        $res = $this->teacher_model->update_password($teacher_id,DEFAULT_PASSWORD);
        if($res < 0)
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        else $this->ajax_return(200, MESSAGE_SUCCESS);
    }

}