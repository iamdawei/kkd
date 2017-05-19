<?php
/**
 * 管理角色控制器
 *管理角色CRUD操作
 *
 */

class Role extends API_Conotroller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('role_model');
        $this->load->model('auth_role_model');
    }

    public function index()
    {

        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $role_id = $this->uri->segment(2, 0);
                if ($role_id) {
                    //获取角色；
                    $this->role_info($role_id);
                    break;
                } else {
                    //角色列表；
                    $this->role_list();
                    break;
                }
            //增加角色
            case REQUEST_POST :
                $this->role_add();
                break;

            //删除角色
            case REQUEST_DELETE :
                $role_id = $this->uri->segment(2, 0);
                $this->role_delete($role_id);
                break;
            //更改角色
            case REQUEST_PUT :
                $role_id = $this->uri->segment(2, 0);
                $this->role_update($role_id);
                break;
        }
    }

    protected function role_info($role_id)
    {
        $school_id = $this->school_id;
        $data = $this->role_model->get_role($role_id,$school_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }

    protected function role_list()
    {
        $school_id = $this->school_id;
        $data = $this->role_model->get_role_list($school_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }

    protected function role_delete($role_id)
    {
        $school_id = $this->school_id;
        $data = $this->role_model->get_role($role_id,$school_id);
        $role_user = $this->role_model->get_teacher_id($role_id);
        if($data['0']->role_type == 1 || $data['0']->role_type == 2 ){
            $this->ajax_return(400,MESSAGE_ERROR_SYSTEM_ROLE_D);
        } else if($role_user) {
            $this->ajax_return(400,MESSAGE_ERROR_HAVE_DATE);
        }
        $this->auth_role_model->delete_auth_role($role_id);
        $res = $this->role_model->delete_role($role_id,$school_id);
        if($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    protected function role_add()
    {
        $school_id = $this->school_id;
        $new_role['role_name'] = $this->input->post('role_name');
        $new_role['school_id'] = $this->school_id;
        $data = $this->role_model->get_role_list($school_id);
        foreach ($data as $k => $v) {
            if($data[$k]->role_name == $new_role['role_name']){
                $this->ajax_return(400,MESSAGE_ERROR_NAME_UNIQUE);
            }
        }
        $role_id = $this->role_model->add_role($new_role);

        $role_array = array(
            0 =>array(
                'role_id'=>$role_id,
                'auth_id'=>23
            ),
            1 =>array(
                'role_id'=>$role_id,
                'auth_id'=>26
            ),
            2 =>array(
                'role_id'=>$role_id,
                'auth_id'=>27
            )
        );
        $this->auth_role_model->add_auth_role($role_array);
        if($role_id){
            $this->ajax_return(200, MESSAGE_SUCCESS,$role_id);
        }
        else $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
    }

    protected function role_update($role_id)
    {
        $school_id = $this->school_id;
        $new_role['role_name'] = $this->input->input_stream('role_name');
        $new_role['school_id'] = $this->school_id;
        $data = $this->role_model->get_role($role_id,$school_id);
        if($data['0']->role_type == 1 || $data['0']->role_type == 2){
            $this->ajax_return(400,MESSAGE_ERROR_SYSTEM_ROLE_P);
        }
        $res = $this->role_model->put_role($role_id,$new_role);
        $role_user = $this->role_model->get_teacher_id($role_id);
        foreach($role_user as $k => $v){
            $role_user[$k]['teacher_role'] = str_ireplace($role_id.':'.$data['0']->role_name,$role_id.':'.$new_role['role_name'],$role_user[$k]['teacher_role']);
        }
        $this->load->model('teacher_model');
        $_res = $this->teacher_model->put_teacher_batch($role_user);
        if($_res < 0){ $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);}
        if($res < 0){
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }
}
