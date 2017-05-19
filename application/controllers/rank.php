<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 排行榜控制器
 * 主要包括：
 *  排行榜操作
 *
 * 暂时提供get接口 待有需求再增加；
 */

class Rank extends API_Conotroller
{
    protected $file_number=0;
    function __construct()
    {
        parent::__construct();
        $this->load->model('rank_model');
    }

    public function index()
    {
        $this->load->model('school_model');
        $va_data = $this->school_model->get($this->school_id,'file_number');
        $this->file_number = $va_data['file_number'];
        switch (REQUEST_METHOD) {
            case REQUEST_GET :
                $teacher_id = intval($this->uri->segment(2, 0));
                if ($teacher_id) {
                    $this->rank_item($teacher_id);
                    break;
                } else {
                    $this->rank_list();
                    break;
                }
        }
    }

    protected function rank_item($teacher_id)
    {
        if(!$teacher_id) $this->ajax_return(300, MESSAGE_ERROR_NON_DATA);
        $where['assessment_type'] = $this->input->get('assessment_type');
        $where['teacher_id'] = $teacher_id;
        $page = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;
        $where['file_number'] = $this->file_number;
        $limit = 10;
        $total = 0;
        if (!$page) $page = 1;
        $ranklist = array();
        $ranklist['data'] = $this->rank_model->get_item_list($where, $limit, $total,$page);
        $ranklist['total'] = $total;
        $ranklist['current_page'] = $page;
        $ranklist['total_page'] = ceil($total / $limit);
        $this->ajax_return(200, MESSAGE_SUCCESS, $ranklist);
    }

    protected function rank_list()
    {
        $where['file_number'] = $this->file_number;

        $where['assessment_type'] = $this->input->get('assessment_type');
        $where['teacher_subject'] = $this->input->get('teacher_subject');
        $where['grade_number'] = $this->input->get('grade_number');
        $where['keywords'] = $this->input->get('keywords');

        $page = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;

        $limit = 10;
        $total = 0;
        if (!$page) $page = 1;

        // 返回数组；
        $ranklist = array();
        $datas = $this->rank_model->rank_list($where, $limit, $total,$page);

        // 返回总条数
        $ranklist['total'] = $total;

        // 返回当前页
        $ranklist['current_page'] = $page;

        // 返回总页数
        $ranklist['total_page'] = ceil($total / $limit);
        
        //名次
        $places = ($page - 1) * $limit+1;
        foreach($datas as $key=>&$value) {
            //把名次插入到查询结果中，方便调用
            $value['rank_number'] = $places;
            $places++;
        }
        $ranklist['data'] = $datas;

        $this->ajax_return(200, MESSAGE_SUCCESS, $ranklist);
    }
}