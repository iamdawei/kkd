<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 消息中心控制器
 *管理角色CRUD操作
 *
 */

class Message extends Base_Controller
{
    //测试；
    protected $school_id = 1;

    protected $user_id = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('message_model');
        $this->user_id = $this->HTTP_TOKEN_SIGN['uid'];
    }

    public function index()
    {

        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $message_id = $this->uri->segment(2, 0);
                if ($message_id) {
                    $this->message_info($message_id);
                    break;
                } else {
                    $this->message_list();
                    break;
                }
                case REQUEST_DELETE :
                    $message_id = $this->uri->segment(2, 0);
                    $this->message_delete($message_id);
                    break;
                case REQUEST_PUT :
                    $message_id = $this->uri->segment(2,0);
                    $this->message_update($message_id);
                    break;
                }
        }

    protected function message_list()
    {
        //整合传入必要分页参数；
        $where['teacher_id'] = $this->user_id;
        $where['page'] = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;

        //确定每页显示，初始化总条数；
        $limit = 10;
        $total = 0;
        $new_message = 0;

        //默认起始页；
        if (empty($where['page'])) {
            $where['page'] = 1;
        }

        // 返回数组；
        $messagelist = array();
        $messagelist['data'] = $this->message_model->get_message_list($where, $limit, $total,$new_message);

        //返回最新消息；
        $messagelist['new_message'] = $new_message;

        // 返回总条数
        $messagelist['total'] = $total;

        // 返回当前页
        $messagelist['current_page'] = $where['page'];

        // 返回总页数
        $messagelist['total_page'] = ceil($total / $limit);

        $this->ajax_return(200, MESSAGE_SUCCESS, $messagelist);
    }

    protected function message_info($message_id)
    {
        $school_id = $this->school_id;
        $data = $this->message_model->get_message($message_id,$school_id);
        $this->ajax_return(200, MESSAGE_SUCCESS, $data);
    }


    protected function message_delete($message_id)
    {
        $school_id = $this->school_id;
        $res = $this->message_model->delete_message($message_id,$school_id);
        if($res < 0) {
            $this->ajax_return(400, MESSAGE_ERROR_DATA_WRITE);
        }
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

    //更改消息状态；
    //todo
    //1 进入获取列表
    //2 全部标记已读；无按钮？
    //以下put 方法 保留更改所有消息状态；

    protected function message_update($message_id)
    {
        $this->message_model->put_message($message_id);
        $this->ajax_return(200, MESSAGE_SUCCESS);
    }

}
