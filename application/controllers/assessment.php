<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 学生控制器
 * 主要包括：
 *  学生信息CRUD操作
 */

class Assessment extends Base_Controller
{

    protected $teacher_id = '';
    protected $teacher_name = '';

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

    /**
     * 以下是提交申请控制器
     * 主要包括
     * 申请内容的CRUD操作
     */
    public function item()
    {
        $this->teacher_id = $this->HTTP_TOKEN_SIGN['uid'];
        $this->load->model('assessment_item_model');
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                if ($assessment_item_id) {
                    //本条申请的具体内容；
                    $this->assessment_item_info($assessment_item_id);
                    break;
                } else {
                    $this->user_assessment_item_list();
                    break;
                }
            case REQUEST_POST :
                $this->assessment_item_add();
                break;
            case REQUEST_DELETE :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                $this->assessment_item_delete($assessment_item_id);
                break;
            case REQUEST_PUT :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                $this->assessment_item_update($assessment_item_id);
                break;
        }
    }

//    上传附件接口需要即调取；
    public function assessment_item_upfile()
    {

        if (! file_exists("./upload/item")) {
            mkdir("./upload/item", 0777, true);    //make_filed_to_save
        }

        $config['upload_path'] = "./upload/item"; //upload_save_filed
        $config['allowed_types'] = 'gif|jpg|png|doc|ppt|xls|mp3|mp4|rar|txt';
        $config['max_size'] = '500';
//        $config['overwrite'] = true;
        $this->load->library('upload', $config);
        $res = $this->upload->do_upload('file'); //upload

        if (!$res) {
            $this->ajax_return(400, $this->upload->error_msg[0]);
        }

        $data = $this->upload->data();

        $res = array();
        $res['file_name'] = $data['file_name'];
        $res['url'] = "/upload/item/".$data['file_name']."?".time();

        //返回附件名称，相对地址供前端调取；
        $this->ajax_return(200, MESSAGE_SUCCESS, $res);
    }

    //上传文章插入图片处理接口
//    public function assessment_item_insertimg()
//    {
//
//        if (! file_exists("./upload/item_img")) {
//            mkdir("./upload/item_img", 0777, true);    //make_filed_to_save
//        }
//
//        $config['upload_path'] = "./upload/item_img"; //upload_save_filed
//        $config['allowed_types'] = 'gif|jpg|png';
//        $config['file_name'] = rand(1, 100) . time();
//        $config['max_size'] = '20000';
//        $this->load->library('upload', $config);
//        $res = $this->upload->do_upload('upfile'); //upload
//
//        if (!$res) {
//            $this->ajax_return(400, MESSAGE_ERROR_PARAMETER);
//        }
//
//        //make_small_thumb;
//        if (! file_exists("./upload/item_img/small_thumb")) {
//
//            mkdir("./upload/item_img/small_thumb", 0777, true);
//
//        }
//
//        $data = $this->upload->data();
//        $res = array();
//        $res['url'] = "/upload/item_img/" . $data['file_name'];
//
//        $this->ajax_return(200, MESSAGE_SUCCESS, $res);
//    }

    //登陆用户提交的审核列表；
    protected function user_assessment_item_list()
    {
        //整合传入必要分页参数；
        $assessment_type = $this->input->get('assessment_type');
        if (isset($assessment_type) && $assessment_type !== 'all') {
            $where['assessment_type'] = $assessment_type;
        }
        $where['teacher_id'] = $this->teacher_id;
        $where['page'] = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;
        $where['keywords'] = $this->input->get('keywords');

        //确定每页显示，初始化总条数；
        $limit = 10;
        $total = 0;

        //默认起始页；
        if (empty($where['page'])) {
            $where['page'] = 1;
        }

        // 返回数组；
        $assessment_itemlist = array();
        $assessment_itemlist['data'] = $this->assessment_item_model->get($assessment_item_id = false, $where, $limit, $total);

        // 返回总条数
        $assessment_itemlist['total'] = $total;

        // 返回当前页
        $assessment_itemlist['current_page'] = $where['page'];

        // 返回总页数
        $assessment_itemlist['total_page'] = ceil($total / $limit);

        $this->ajax_return(200, MESSAGE_SUCCESS, $assessment_itemlist);
    }

    protected function assessment_item_add()
    {
        $where['assessment_type'] = $this->input->post('assessment_type');
        $where['assessment_name'] = $this->input->post('assessment_name');
        $assessment_set = $this->assessment_model->get_info($where, 'assessment_set_id,assessment_number,file_number,assessment_role');
        $item_array = array(
            'teacher_id' => $this->teacher_id,
            'teacher_name' => $this->input->post('teacher_name'),
            'assessment_set_id' => $assessment_set['assessment_set_id'],
            'assessment_type' => $this->input->post('assessment_type'),
            'assessment_name' => $this->input->post('assessment_name'),
            'item_number' => $assessment_set['assessment_number'],
            'item_title' => $this->input->post('item_title'),
            'item_content' => $this->input->post('item_content'),
            'item_zip' => join(',', $this->input->post('item_zip')),   //note：调用附件上传接口数据处理；
            'commit_datetime' => date('Y-m-d'),
            'item_status' => 0,
            'file_number' => $assessment_set['file_number'],
            'school_id' => $this->school_id,
            'assessment_role' => $assessment_set['assessment_role']
        );
        $item_id = $this->assessment_item_model->add($item_array);
        if (!$item_id) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS, $item_id);
    }

    //用户删除未审核内容；
    protected function assessment_item_delete($assessment_item_id)
    {
        $status = $this->assessment_item_model->get($assessment_item_id);
        if ($status['item_status'] == 1) {
            $this->ajax_return(400, MESSAGE_ERROR_USER_ROLE);
        }
        $res = $this->assessment_item_model->delete($assessment_item_id);
        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    protected function assessment_item_update($assessment_item_id)
    {
        $status = $this->assessment_item_model->get($assessment_item_id);
        if ($status['item_status'] == 1) {
            $this->ajax_return(400, MESSAGE_ERROR_USER_ROLE);
        }
        $where['assessment_type'] = $this->input->input_stream('assessment_type');
        $where['assessment_name'] = $this->input->input_stream('assessment_name');
        $assessment_set = $this->assessment_model->get_info($where, 'assessment_set_id,assessment_number,file_number,assessment_role');
        $item_array = array(
            'teacher_id' => $this->teacher_id,
            'teacher_name' => $this->input->input_stream('teacher_name'),
            'assessment_set_id' => $assessment_set['assessment_set_id'],
            'assessment_type' => $this->input->input_stream('assessment_type'),
            'assessment_name' => $this->input->input_stream('assessment_name'),
            'item_number' => $assessment_set['assessment_number'],
            'item_title' => $this->input->input_stream('item_title'),
            'item_content' => $this->input->input_stream('item_content'),
            'item_zip' => join(',', $this->input->post('item_zip')),   //note：调用附件上传接口数据处理；
            'commit_datetime' => date('Y-m-d'),
            'item_status' => 0,
            'file_number' => $assessment_set['file_number'],
            'school_id' => $this->school_id,
            'assessment_role' => $assessment_set['assessment_role']
        );
        $res = $this->assessment_item_model->put($assessment_item_id, $item_array);
        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    /**
     * 以下是评估实体控制器
     * 主要包括
     * 申请的审核操作
     *
     */

    public function check()
    {
        $this->teacher_id = $this->HTTP_TOKEN_SIGN['uid'];
        $where['teacher_id'] = $this->teacher_id;
        $this->load->model('teacher_model');
        $date = $this->teacher_model->get_teacher($where,'teacher_name');
        $this->teacher_name = $date['teacher_name'];
        $this->load->model('assessment_item_model');

        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                if ($assessment_item_id) {
                    //本条申请的具体内容；
                    $this->assessment_item_info($assessment_item_id);
                    break;
                } else {
                    $this->assessment_item_list();
                    break;
                }
            //批量通过
            case REQUEST_POST :
                $assessment_item_id_str = $this->uri->segment(3, 0);
                $this->assessment_item_batchpass($assessment_item_id_str);
                break;
            //驳回
            case REQUEST_DELETE :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                $this->assessment_item_rebut($assessment_item_id);
                break;
            //单条通过
            case REQUEST_PUT :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                $this->assessment_item_pass($assessment_item_id);
                break;
        }
    }

    //单条详细信息；
    protected function assessment_item_info($assessment_item_id)
    {
        $data = $this->assessment_item_model->get($assessment_item_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }

    //待审列表
    protected function assessment_item_list()
    {

        //获取teacher_role；
        $date['teacher_id'] = $this->teacher_id;
        $teacher_role = $this->teacher_model->get_teacher($date,'teacher_role');

        //整合传入必要分页参数；
        $assessment_type = $this->input->get('assessment_type');
        if (isset($assessment_type) && $assessment_type !== 'all') {
            $where['assessment_type'] = $assessment_type;
        }

        $where['page'] = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;
        $where['keywords'] = $this->input->get('keywords');
        $where['teacher_role'] = $teacher_role['teacher_role'];

        //确定每页显示，初始化总条数；
        $limit = 10;
        $total = 0;

        //默认起始页；
        if (empty($where['page'])) {
            $where['page'] = 1;
        }

        // 返回数组；
        $assessment_itemlist = array();
        $assessment_itemlist['data'] = $this->assessment_item_model->get($assessment_item_id = false, $where, $limit, $total);

        // 返回总条数
        $assessment_itemlist['total'] = $total;

        // 返回当前页
        $assessment_itemlist['current_page'] = $where['page'];

        // 返回总页数
        $assessment_itemlist['total_page'] = ceil($total / $limit);

        $this->ajax_return(200, MESSAGE_SUCCESS, $assessment_itemlist);
    }

    protected function assessment_item_pass($assessment_item_id)
    {
        $item_array['assessment_item_id'] = $assessment_item_id;
        $item_status['item_status'] = 1;
        $item_status['auditor_id'] = $this->teacher_id;
        $item_status['auditor_name'] = $this->teacher_name;
        $item_status['auditor_datetime'] = date('Y-m-d');

        $res = $this->assessment_item_model->put_status($item_array, $item_status);

        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS, $item_status);
    }

    //批量通过
    protected function assessment_item_batchpass($assessment_item_id_str)
    {
        $item_array = explode(',', $assessment_item_id_str);

        $item_status['item_status'] = 1;
        $item_status['auditor_id'] = $this->teacher_id;
        $item_status['auditor_name'] = $this->teacher_name;
        $item_status['auditor_datetime'] = date('Y-m-d');

        $this->assessment_item_model->put_status($item_array,$item_status);
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    //驳回
    protected function assessment_item_rebut($assessment_item_id)
    {
        $item_array['assessment_item_id'] = $assessment_item_id;
        $item_status['item_status'] = 2;
        $item_status['status_descript'] = $this->input->input_stream('status_descript');
        $item_status['auditor_id'] = $this->teacher_id;
        $item_status['auditor_name'] = $this->teacher_name;
        $item_status['auditor_datetime'] = date('Y-m-d H:m:s');

        $item_status['status_descript'] = $this->input->input_stream('status_descript');
        $res = $this->assessment_item_model->put_status($item_array, $item_status);

        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS, $item_status);
    }

}