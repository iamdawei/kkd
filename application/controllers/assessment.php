<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 学生控制器
 * 主要包括：
 *  学生信息CRUD操作
 */

class Assessment extends Base_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('assessment_model');
    }

    public function index()
    {

        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $assessment_set_id = intval($this->uri->segment(2,0));
                if($assessment_set_id){
                    $this->assessment_item($assessment_set_id);
                    break;
                }
                    else{
                    $this->assessment_list();
                    break;
                }
            case REQUEST_POST :
                $this->assessment_add();
                break;
            case REQUEST_DELETE :
                $this->assessment_delete();
                break;
            case REQUEST_PUT :
                $this->assessment_update();
                break;
        }
    }
    public function open()
    {
        if(REQUEST_METHOD != REQUEST_PUT ) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);

        $assessment_set_id = $this->uri->segment(3,0);
        //查看当前发布状态；
        $is_open = $this->assessment_model->get($assessment_set_id);

        if($is_open['is_open'] == 1){
            $this->ajax_return(400,MESSAGE_ERROR_USER_ROLE);
        }
        //发布
        $open['is_open'] = 1;
        $res = $this->assessment_model->put($assessment_set_id,$open);
        if (! $res)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS,$open);

    }

    protected function assessment_item($assessment_set_id)
    {
        $data = $this->assessment_model->get($assessment_set_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }


    protected function assessment_list()
    {
        //整合传入必要分页参数；
        $is_open = $this->input->get('is_open');
        if(isset($is_open) && $is_open !== 'all')
        {
            $where['is_open'] = $is_open ;
        }

        $assessment_type = $this->input->get('assessment_type');
        if(isset($assessment_type) && $assessment_type !== 'all')
        {
            $where['assessment_type'] = $assessment_type;
        }

        $where['page'] = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;
        $where['keywords'] = $this->input->get('keywords');

        //确定每页显示，初始化总条数；
        $limit = 8;
        $total = 0;

        //默认起始页；
        if(empty($where['page'])) { $where['page'] = 1; }

        // 返回数组；
        $assessmentlist = array();
        $assessmentlist['data'] = $this->assessment_model->get($assessment_set_id = false, $where, $limit, $total);

        // 返回总条数
        $assessmentlist['total'] = $total;

        // 返回当前页
        $assessmentlist['current_page'] = $where['page'];

        // 返回总页数
        $assessmentlist['total_page'] = ceil($total / $limit);

        $this->ajax_return(200,MESSAGE_SUCCESS,$assessmentlist);
    }

    protected function assessment_add()
    {
        $data = array(
            'assessment_type' => $this->input->post('assessment_type'),
            'assessment_name' => $this->input->post('assessment_name'),
            'have_title'=> $this->input->post('have_title'),
            'have_content'=> $this->input->post('have_content'),
            'have_zip'=> $this->input->post('have_zip'),
            'assessment_number'=> $this->input->post('assessment_number'),
            'school_id'=> $this->school_id,
            'file_number' => date('Ym'),
            'is_open' => 0,
            'assessment_role'=> join(',',$this->input->post('assessment_role')),
        );
        $new_id = $this->assessment_model->add($data);

        if (! $new_id)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS,$new_id);
    }

    protected function assessment_delete()
    {
        $assessment_set_id = intval($this->uri->segment(2,0));
        //查看当前发布状态；
        $is_open = $this->assessment_model->get($assessment_set_id);
        if($is_open['is_open'] == 1){
          $this->ajax_return(400,MESSAGE_ERROR_USER_ROLE);
        }

        $res = $this->assessment_model->delete($assessment_set_id);
        if ($res < 0)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);
    }


    protected function assessment_update()
    {
        $assessment_set_id = intval($this->uri->segment(2,0));
        //查看当前发布状态；
        $is_open = $this->assessment_model->get($assessment_set_id);
        if($is_open['is_open'] == 1){
            $this->ajax_return(400,MESSAGE_ERROR_USER_ROLE);
        }

        $data = array(
            'assessment_type' => $this->input->input_stream('assessment_type'),
            'assessment_name' => $this->input->input_stream('assessment_name'),
            'have_title'=> $this->input->input_stream('have_title'),
            'have_content'=> $this->input->input_stream('have_content'),
            'have_zip'=> $this->input->input_stream('have_zip'),
            'assessment_number'=> $this->input->input_stream('assessment_number'),
            'assessment_role'=> join(',',$this->input->input_stream('assessment_role')),
            'school_id'=> $this->school_id,
        );

        $res = $this->assessment_model->put($assessment_set_id,$data);

        if (! $res)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS);

    }

}