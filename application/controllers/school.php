<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 学校管理
 * 超级管理：对学校进行CRUD操作
 *
 */

class School extends API_Conotroller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('school_model');

    }

    public function index()
    {
        $school_id = $this->uri->segment(2, 0);
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                    $this->school_list();
                    break;
            case REQUEST_POST :
                    $this->school_add();
                    break;
            case REQUEST_PUT :
                $this->school_update($school_id);
                break;
        }

    }

    protected function school_list()
    {
        $school_open = $this->input->get('school_open');
        $where['keywords'] = $this->input->get('keywords');

        if(isset($school_open) && $school_open !== 'all')
        {
            $where['school_open'] = $school_open ;
        }

        $where['page'] = intval($this->input->get('page'));

        $limit = 10;
        $total = 0;

        if(empty($where['page'])) { $where['page'] = 1; }

        $sch_list = array();
        $sch_list['data'] = $this->school_model->get_list($where, $limit, $total);
        $sch_list['total'] = $total;
        $sch_list['current_page'] = $where['page'];
        $sch_list['total_page'] = ceil($total / $limit);
        $this->ajax_return(200,MESSAGE_SUCCESS,$sch_list);
    }

    public function school_open()
    {
        if(REQUEST_METHOD != REQUEST_PUT ) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);
        $school_id = $this->uri->segment(3,0);
        //查看当前学校状态；
        $sch_data = $this->school_model->get_info($school_id);
        if($sch_data['school_open'] == 1){
            $data = array('school_open' => 0);
            $res = $this->school_model->put($school_id,$data);
        } else if($sch_data['school_open'] == 0){
            $data = array('school_open' => 1);
            $res = $this->school_model->put($school_id,$data);
        }

        if ($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }


    protected function school_add()
    {
        $data = array(
            'school_name' => $this->input->post('school_name'),
            'school_address'=> $this->input->post('school_address'),
            'school_contact'=> $this->input->post('school_contact'),
            'school_contact_type'=> $this->input->post('school_contact_type'),
            'register_datetime'=> date('Y-m-d H:i:s'),
        );
        $new_id = $this->school_model->add($data);
        $this->load->model('role_model');
        $role = array(
            0 =>array(
                'role_type' => 2,
                'role_name' => '普通教师',
                'school_id' => $new_id
            ),
            1 =>array(
                'role_type'=>1,
                'role_name'=>'系统管理',
                'school_id' => $new_id
            ),
        );
        $this->role_model->add_role_batch($role);
        $this->load->model('auth_role_model');
        $role_id = $this->role_model->get_role_list($new_id);
            $role_array = array(
                0 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>16),
                1 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>17),
                2 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>18),
                3 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>19),
                4 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>20),
                5 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>21),
                6 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>23),
                7 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>27),
                8 =>array('role_id'=>$role_id['1']->role_id, 'auth_id'=>23),
                9 =>array('role_id'=>$role_id['1']->role_id, 'auth_id'=>24),
                10 =>array('role_id'=>$role_id['1']->role_id, 'auth_id'=>25),
                11 =>array('role_id'=>$role_id['1']->role_id, 'auth_id'=>28),
                12 =>array('role_id'=>$role_id['1']->role_id, 'auth_id'=>29),
                13 =>array('role_id'=>$role_id['1']->role_id, 'auth_id'=>30),
                14 =>array('role_id'=>$role_id['0']->role_id, 'auth_id'=>22)
            );

        $this->auth_role_model->add_auth_role($role_array);

        if (! $new_id)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS,$new_id);
    }

    protected function school_update($school_id)
    {
        $data = array(
            'school_name' => $this->input->input_stream('school_name'),
            'school_address'=> $this->input->input_stream('school_address'),
            'school_contact'=> $this->input->input_stream('school_contact'),
            'school_contact_type'=> $this->input->input_stream('school_contact_type'),
        );

        $res = $this->school_model->put($school_id,$data);

        if ($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

    /**
     * 学校设施管理
     * 超级管理：对学校设施进行CRUD操作
     *
     */

    public function config()
    {
        $school_id = $this->uri->segment(3, 0);
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $this->config_info($school_id);
                break;
            case REQUEST_POST :
                $this->config_add($school_id);
                break;
            case REQUEST_PUT :
                $this->config_update($school_id);
                break;
        }
    }

    protected function config_info($school_id)
    {
        $va = $this->school_model->get($school_id);
        $this->ajax_return(200,MESSAGE_SUCCESS,$va);
    }

    protected function config_add($school_id)
    {
        $va = $this->school_model->get($school_id,'config_id');
        if(! empty($va)){
            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_UNIQUE);
        }
        $data = array(
            'school_id' => $school_id,
            'day_periods'=> $this->input->post('day_periods'),
            'school_subject'=> $this->input->post('school_subject'),
            'school_grade_class'=> $this->input->post('school_grade_class'),
            'grade_1'=> $this->input->post('grade_1'),
            'grade_2'=> $this->input->post('grade_2'),
            'grade_3'=> $this->input->post('grade_3'),
            'grade_4'=> $this->input->post('grade_4'),
            'grade_5'=> $this->input->post('grade_5'),
            'grade_6'=> $this->input->post('grade_6'),
            'fixed_period'=> $this->input->post('fixed_period'),
            'file_number'=> 100001,
        );
        $new_id = $this->school_model->add_config($data);
        if (! $new_id)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS,$new_id);
    }

    protected function config_update($school_id)
    {

        $data = array(
            'day_periods'=> $this->input->input_stream('day_periods'),
            'school_subject'=> $this->input->input_stream('school_subject'),
            'school_grade_class'=> $this->input->input_stream('school_grade_class'),
            'grade_1'=> $this->input->input_stream('grade_1'),
            'grade_2'=> $this->input->input_stream('grade_2'),
            'grade_3'=> $this->input->input_stream('grade_3'),
            'grade_4'=> $this->input->input_stream('grade_4'),
            'grade_5'=> $this->input->input_stream('grade_5'),
            'grade_6'=> $this->input->input_stream('grade_6'),
            'fixed_period'=> $this->input->input_stream('fixed_period'),
        );
        $res = $this->school_model->put_config($school_id,$data);
        if ($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }

}