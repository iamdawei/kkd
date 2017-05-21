<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 考核标准控制器
 * 主要包括：
 *  管理员对考核标准CRUD操作
 *  教师对考核的CRUD操作
 *
 *   TODO-KKD 文件上传的XSS验证 2017-04-27，此功能留在以后优化，特注明
 */

class Assessment extends API_Conotroller
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
                $assessment_set_id = intval($this->uri->segment(2, 0));
                if($assessment_set_id){
                    $this->assessment_update();
                    break;
                }else{
                    //put模式没有id值的时候，默认为更改file_number入口；
                    $this->assessment_file_number();
                    break;
                }
        }
    }

    //更新学校配置的file_number字段；
    protected function assessment_file_number()
    {
        $res = $this->assessment_model->put_file_number($this->school_id);
        if (!$res) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200, MESSAGE_SUCCESS);

    }

    public function open()
    {
        if(REQUEST_METHOD != REQUEST_PUT ) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);

        $assessment_set_id = $this->uri->segment(3,0);
        //查看当前发布状态；
        $is_open = $this->assessment_model->get_info($assessment_set_id,'is_open,assessment_role');

        if($is_open['is_open'] == 1){
            $this->ajax_return(400,MESSAGE_ERROR_USER_ROLE);
        }
        //发布
        $open['is_open'] = 1;
        $res = $this->assessment_model->put($assessment_set_id,$open);
        //插入角色审核权限于assessment_role;
        $role_array = array();
        $role_str = $is_open['assessment_role'];
        $role = explode(',',$role_str);
        foreach ($role as $value){
            $value = explode(':',$value);
            $value = array(
                'assessment_set_id'=>$assessment_set_id,
                'role_id'=>$value[0]
            );
            array_push($role_array,$value);
        }
        $this->assessment_model->add_role($role_array);
        if (! $res)
        {
            $this->ajax_return(400,MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200,MESSAGE_SUCCESS,$open);

    }

    protected function assessment_item($assessment_set_id)
    {
        $data = $this->assessment_model->get_info($assessment_set_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }


    protected function assessment_list()
    {
        $where['file_number'] = $this->input->get('file_number');

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
        $assessmentlist['data'] = $this->assessment_model->get_list($where, $limit, $total);

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
        $this->load->model('school_model');
        $file_number = $this->school_model->get($this->school_id,'file_number');
        $data = array(
            'assessment_descript' =>  $this->input->post('assessment_descript'),
            'max_number' =>  $this->input->post('max_number'),
            'assessment_type' => $this->input->post('assessment_type'),
            'assessment_name' => $this->input->post('assessment_name'),
            'have_title'=> $this->input->post('have_title'),
            'have_content'=> $this->input->post('have_content'),
            'have_zip'=> $this->input->post('have_zip'),
            'assessment_number'=> $this->input->post('assessment_number'),
            'school_id'=> $this->school_id,
            'file_number' => $file_number['file_number'],
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
        $is_open = $this->assessment_model->get_info($assessment_set_id);
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
        $is_open = $this->assessment_model->get_info($assessment_set_id);
        if($is_open['is_open'] == 1){
            $this->ajax_return(400,MESSAGE_ERROR_USER_ROLE);
        }

        $data = array(
            'assessment_descript' =>  $this->input->input_stream('assessment_descript'),
            'max_number' =>  $this->input->input_stream('max_number'),
            'assessment_type' => $this->input->input_stream('assessment_type'),
            'assessment_name' => $this->input->input_stream('assessment_name'),
            'have_title'=> $this->input->input_stream('have_title'),
            'have_content'=> $this->input->input_stream('have_content'),
            'have_zip'=> $this->input->input_stream('have_zip'),
            'assessment_number'=> $this->input->input_stream('assessment_number'),
            'assessment_role'=> join(',',$this->input->input_stream('assessment_role'))
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

    public function assessment_item_check()
    {
        if(REQUEST_METHOD != REQUEST_GET ) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);
        $teacher_id = $this->HTTP_TOKEN_SIGN['uid'];
        $assessment_set_id = $this->uri->segment(3,0);
        $data = $this->assessment_model->get_info($assessment_set_id,'have_title,have_content,have_zip,assessment_descript,max_number');
        $this->load->model('assessment_item_model');
        if(empty($data)){
            $this->ajax_return(400,MESSAGE_ERROR_NON_DATA);
        }
        $data['count'] = $this->assessment_item_model->get_max_number($assessment_set_id ,$teacher_id);
        $this->ajax_return(200,MESSAGE_SUCCESS,$data);
    }
//    上传附件接口需要即调取；
    public function item_upfile()
    {
        $teacher_id = $this->HTTP_TOKEN_SIGN['uid'];
        //临时文件夹，执行保存后，将此文件夹对应文件移动到/item/下
        $file_path = '/upload/item/temp/';

        if (! file_exists(".".$file_path)) {
            mkdir(".".$file_path, 0777, true);
        }

        $config['upload_path'] = ".".$file_path;
        $config['allowed_types'] = KKD_UPLOAD_FILE;
        $config['max_size'] = 0;
        $config['file_name'] = $teacher_id."-".time();
        $config['overwrite'] = true;
        $this->load->library('upload', $config);
        $res = $this->upload->do_upload('kkd_file');

        if (!$res) {
            $this->ajax_return(400, $this->upload->error_msg[0]);
        }

        $data = $this->upload->data();

        $re_data['file_name'] = $file_path.$data['file_name'];
        $re_data['client_name'] = $data['client_name'];

        //返回附件名称，相对地址供前端调取；
        $this->ajax_return(200, MESSAGE_SUCCESS, $re_data);
    }

    public function item_delfile()
    {
        if(REQUEST_METHOD != REQUEST_DELETE ) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);
        //DELETE请求；
        $file_name = $this->input->input_stream('file_name');
        $file_id = $this->input->input_stream('file_id');
        if($file_id){
            $this->load->model('assessment_item_model');
            $this->assessment_item_model->file_delete($file_id);
        }
        $file = "./".$file_name;
        if (!file_exists($file)) {
            $this->ajax_return(400, MESSAGE_ERROR_NON_DATA);
        }
        if (! @unlink($file))
        {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        else
        {
            $this->ajax_return(200, MESSAGE_SUCCESS);
        }
    }

    //上传文章插入图片处理接口
    public function item_img()
    {
        $teacher_id = $this->HTTP_TOKEN_SIGN['uid'];
        //临时文件夹，执行保存后，将此文件夹对应文件移动到/item_img/下
        $file_path = '/upload/item_img/temp/';
        if (! file_exists(".".$file_path)) {
            mkdir(".".$file_path, 0777, true);    //make_filed_to_save
        }

        $config['upload_path'] = ".".$file_path; //upload_save_filed
        $config['allowed_types'] = 'gif|jpg|png';
        $config['file_name'] = $teacher_id.'-' . time();
        $config['max_size'] = 256;
        $this->load->library('upload', $config);
        $res = $this->upload->do_upload('upfile'); //upload

        if (!$res) {
            $this->ajax_return(400, $this->upload->error_msg[0]);
        }

        $data = $this->upload->data();
        $res = $file_path . $data['file_name'];

        $this->ajax_return(200, MESSAGE_SUCCESS, $res);
    }

    //登陆用户提交的审核列表；
    protected function user_assessment_item_list()
    {
        //整合传入必要分页参数；
        $assessment_type = $this->input->get('assessment_type');
        if (isset($assessment_type) && $assessment_type !== 'all') {
            $where['assessment_type'] = $assessment_type;
        }
        $item_status = $this->input->get('item_status');
        if (isset($item_status) && $item_status !== 'all') {
            $where['item_status'] = $item_status;
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
        $assessment_itemlist['data'] = $this->assessment_item_model->get_list($where, $limit, $total);

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
        $where['assessment_set_id'] = $this->input->post('assessment_set_id');
        $assessment_set = $this->assessment_model->get_info($where['assessment_set_id'], 'assessment_number,assessment_type,assessment_name,file_number,assessment_role,max_number');
        if(!$assessment_set) $this->ajax_return(400, MESSAGE_ERROR_NON_DATA);
        $this->load->model('teacher_model');
        $tea_va = $this->teacher_model->get_teacher(array('teacher_id'=>$this->teacher_id),'teacher_name');
        if(!$tea_va) $this->ajax_return(400, MESSAGE_ERROR_NON_DATA);
        $count = $this->assessment_item_model->get_max_number($where['assessment_set_id'] ,$this->teacher_id);
        if($count >= $assessment_set['max_number']) $this->ajax_return(400,MESSAGE_ERROR_ENOUGH);
        $save_content_imgs = $this->input->post('imgs');
        $save_content = $this->input->post('item_content',false);
        if(!isset($save_content)) $save_content = '';
        $item_array = array(
            'teacher_id' => $this->teacher_id,
            'teacher_name' => $tea_va['teacher_name'],
            'assessment_set_id' => $where['assessment_set_id'],
            'assessment_type' => $assessment_set['assessment_type'],
            'assessment_name' => $assessment_set['assessment_name'],
            'item_number' => $assessment_set['assessment_number'],
            'item_title' => $this->input->post('item_title'),
            'item_content' => $save_content,
            'commit_datetime' => date('Y-m-d H:i:s'),
            'item_status' => 1,
            'file_number' => $assessment_set['file_number'],
            'school_id' => $this->school_id,
            'assessment_role' => $assessment_set['assessment_role']
        );
        $item_id = $this->assessment_item_model->add($item_array);
        if (!$item_id) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }

        $this->move_files($item_id,$this->input->post('files'),$save_content_imgs);
        $this->ajax_return(200, MESSAGE_SUCCESS, $item_id);
    }

    protected function move_files($item_id,$item_zip,$content_imgs)
    {
        //TODO-KKD （已完成，更新后删除） 将文本图片也记录到数据中，将kkd_item_file两个字段长度变更为40
        $file_data = array();
        //移动内容图片
        //移动附件
        if(! empty($content_imgs)){
            $arr_imgs = explode(',,,',$content_imgs);
            foreach ($arr_imgs as $img){
                $temp_path = $img;
                if(stripos($temp_path,'/temp/') !== false){
                    //如果目标文件存在/temp/则表示是临时文件
                    $save_path = str_replace('/temp/','/',$temp_path);
                    rename('.'.$temp_path,'.'.$save_path);
                    @unlink('.'.$temp_path);
                    $file_data[] = array(
                        'file_name' => $save_path,
                        'file_real_name' => $save_path,
                        'item_id' => $item_id
                    );
                }
            }
        }
        //移动附件
        if(! empty($item_zip)){
            $item_zip = explode(',,,',$item_zip);
            foreach ($item_zip as $value){
                $file_name = explode('===',$value);
                $temp_path = $file_name[1];
                if(stripos($temp_path,'/temp/') !== false){
                    //如果目标文件存在/temp/则表示是临时文件
                    $save_path = str_replace('/temp/','/',$temp_path);
                    rename('.'.$temp_path,'.'.$save_path);
                    @unlink('.'.$temp_path);
                    $file_data[] = array(
                        'file_name' => $file_name[0],
                        'file_real_name' => $save_path,
                        'item_id' => $item_id
                    );
                }
            }
        }

        if(count($file_data)>0)
            $this->assessment_item_model->file_insert_batch($file_data);
    }

    //用户删除未审核内容；
    protected function assessment_item_delete($assessment_item_id)
    {
        $status = $this->assessment_item_model->get_item($assessment_item_id);
        if ($status['item_status'] == 0) {
            $this->ajax_return(400, MESSAGE_ERROR_USER_ROLE);
        }
        //循环删除file_real_name这个路径的文件
        $files = $this->assessment_item_model->get_item_file($assessment_item_id);
        if(! empty($files)){
            foreach($files as $key => $value){
                $temp_path = $files[$key]->file_real_name;
                @unlink('.'.$temp_path);
            }
        }

        $res = $this->assessment_item_model->delete($assessment_item_id);
        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }

        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    protected function assessment_item_update($assessment_item_id)
    {
        $where['assessment_set_id'] = $this->input->input_stream('assessment_set_id');
        $assessment_set = $this->assessment_model->get_info($where['assessment_set_id'], 'assessment_number,assessment_type,assessment_name,file_number,assessment_role,max_number');
        if(!$assessment_set) $this->ajax_return(400, MESSAGE_ERROR_NON_DATA);
        $count = $this->assessment_item_model->get_max_number($where['assessment_set_id'] ,$this->teacher_id);
        //此处是更新，和新增的逻辑不同，当count > max_number 时，才不允许提交
        if($count > $assessment_set['max_number']) $this->ajax_return(400,MESSAGE_ERROR_ENOUGH);
        $this->load->model('teacher_model');
        $tea_va = $this->teacher_model->get_teacher(array('teacher_id'=>$this->teacher_id),'teacher_name');
        if(!$tea_va) $this->ajax_return(400, MESSAGE_ERROR_NON_DATA);
        $save_content = $this->input->input_stream('item_content',false);
        if(!isset($save_content)) $save_content = '';
        $item_array = array(
            'teacher_id' => $this->teacher_id,
            'teacher_name' => $tea_va['teacher_name'],
            'assessment_set_id' => $where['assessment_set_id'],
            'assessment_type' => $assessment_set['assessment_type'],
            'assessment_name' => $assessment_set['assessment_name'],
            'item_number' => $assessment_set['assessment_number'],
            'item_title' => $this->input->input_stream('item_title'),
            'item_content' => $save_content,
            'commit_datetime' => date('Y-m-d H:i:s'),
            'item_status' => 1,
            'assessment_role' => $assessment_set['assessment_role'],
        );
        $res = $this->assessment_item_model->put($assessment_item_id, $item_array);
        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }

        $this->move_files($assessment_item_id,$this->input->input_stream('files'),$this->input->input_stream('imgs'));
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
        $data = $this->assessment_item_model->get_item($assessment_item_id,'assessment_item_id,assessment_type,assessment_set_id,assessment_name,item_title,item_content,item_number,teacher_name,commit_datetime,item_status');
        $data['files'] = $this->assessment_item_model->get_item_file($assessment_item_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }

    //待审列表
    protected function assessment_item_list()
    {
        $assessment_type = $this->input->get('assessment_type');
        if (isset($assessment_type) && $assessment_type !== 'all') {
            $where['assessment_type'] = $assessment_type;
        }
        $where['item_status'] = $this->input->get('status');
        $where['teacher_id'] = $this->teacher_id;
        $page = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;
        $where['keywords'] = $this->input->get('keywords');

        $limit = 10;
        $total = 0;
        if (!$page) $page = 1;
        $assessment_itemlist = array();
        $cols = 'assessment_item_id,assessment_type,ai.assessment_set_id,assessment_name,item_title,item_number,teacher_name,commit_datetime,auditor_name';
        $assessment_itemlist['data'] = $this->assessment_item_model->get($where, $limit, $total,$page,$cols);
        $assessment_itemlist['total'] = $total;
        $assessment_itemlist['current_page'] = $page;
        $assessment_itemlist['total_page'] = ceil($total / $limit);
        $this->ajax_return(200, MESSAGE_SUCCESS, $assessment_itemlist);
    }

    protected function assessment_item_pass($assessment_item_id)
    {
        $item_array['assessment_item_id'] = $assessment_item_id;
        $item_status['item_status'] = 0;
        $item_status['auditor_id'] = $this->teacher_id;
        $item_status['auditor_name'] = $this->teacher_name;
        $item_status['auditor_datetime'] = date('Y-m-d H:i:s');

        $res = $this->assessment_item_model->put_status($item_array, $item_status);

        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }

        $message = $this->assessment_item_model->get_item($assessment_item_id,'teacher_id,assessment_item_id,assessment_type,
                                                assessment_name,item_title');
        $this->load->model('message_model');
        $message['item_status'] = 0;
        $message['auditor_name'] = $this->teacher_name;
        $message['auditor_datetime' ] = $item_status['auditor_datetime'];
        $message['message_status'] = 1;
        $this->message_model->add($message);

        $this->ajax_return(200, MESSAGE_SUCCESS, $item_status);
    }

    //批量通过
    protected function assessment_item_batchpass($assessment_item_id_str)
    {
        $item_array = explode(',', $assessment_item_id_str);

        $item_status['item_status'] = 0;
        $item_status['auditor_id'] = $this->teacher_id;
        $item_status['auditor_name'] = $this->teacher_name;
        $item_status['auditor_datetime'] = date('Y-m-d H:i:s');

        $this->assessment_item_model->put_status($item_array,$item_status);

        $message_array = $this->assessment_item_model->get_item($item_array,'teacher_id,assessment_item_id,assessment_type,
                                                assessment_name,item_title');

        foreach ($message_array as $k=>$value) {
            $message_array[$k]->item_status = 0;
            $message_array[$k]->auditor_name = $this->teacher_name;
            $message_array[$k]->auditor_datetime = $item_status['auditor_datetime'];
            $message_array[$k]->message_status = 1;
        }
        $this->load->model('message_model');
        $this->message_model->add_batch($message_array);

        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    //驳回
    protected function assessment_item_rebut($assessment_item_id)
    {
        $item_array['assessment_item_id'] = $assessment_item_id;

        //查询状态如果为0或2，则返回400，提示本条已审核通过或驳回，返回操作的管理员姓名或姓名和驳回原因；
        $now_status = $this->assessment_item_model->get_item($assessment_item_id,'item_status,auditor_name,status_descript');
        if($now_status['item_status'] == 0 )
        {
            $this->ajax_return(400,MESSAGE_ERROR_HAVE_DONE_PASS,$now_status['auditor_name']);
        } else if ($now_status['item_status'] == 2 ) {
            $this->ajax_return(400,MESSAGE_ERROR_HAVE_DONE_REBUT,$now_status);
        }
        $item_status['item_status'] = 2;
        $item_status['status_descript'] = $this->input->input_stream('status_descript');
        $item_status['auditor_id'] = $this->teacher_id;
        $item_status['auditor_name'] = $this->teacher_name;
        $item_status['auditor_datetime'] = date('Y-m-d H:i:s');

        $res = $this->assessment_item_model->put_status($item_array, $item_status);

        if ($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }

        $message = $this->assessment_item_model->get_item($assessment_item_id,'teacher_id,assessment_item_id,assessment_type,
                                                assessment_name,item_title');
        $this->load->model('message_model');
        $message['item_status'] = 2;
        $message['auditor_name'] = $this->teacher_name;
        $message['auditor_datetime' ] = $item_status['auditor_datetime'];
        $message['message_status'] = 1;
        $message['status_descript'] = $item_status['status_descript'];
        $this->message_model->add($message);

        $this->ajax_return(200, MESSAGE_SUCCESS, $item_status);
    }

    public function source()
    {
        $this->load->model('assessment_item_model');
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $assessment_item_id = intval($this->uri->segment(3, 0));
                if ($assessment_item_id) {
                    //本条申请的具体内容；
                    $this->assessment_item_info($assessment_item_id);
                    break;
                } else {
                    $this->assessment_item_source();
                    break;
                }
        }
    }

    protected function assessment_item_source()
    {
        $assessment_type = $this->input->get('assessment_type');
        $where['keywords'] = $this->input->get('keywords');
        $page = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;
        if (isset($assessment_type) && $assessment_type !== 'all') {
            $where['assessment_type'] = $assessment_type;
        }

        $limit = 10;
        $total = 0;
        if (!$page) $page = 1;
        $assessment_itemlist = array();
        $cols = 'assessment_item_id,assessment_type,assessment_set_id,assessment_name,item_title,item_number,teacher_name,commit_datetime,auditor_name';
        $assessment_itemlist['data'] = $this->assessment_item_model->get_source_list($where, $limit, $total,$page,$cols);
        $assessment_itemlist['total'] = $total;
        $assessment_itemlist['current_page'] = $page;
        $assessment_itemlist['total_page'] = ceil($total / $limit);
        $this->ajax_return(200, MESSAGE_SUCCESS, $assessment_itemlist);
    }
}