<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 排行榜控制器
 * 主要包括：
 *  排行榜操作
 *
 * 暂时提供get接口 待有需求再增加；
 */

class Rank extends Base_Controller
{

    function __construct()

    {
        parent::__construct();
        $this->load->model('rank_model');

    }

    public function index()
    {

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


   // 教师个人详细列表；
    protected function rank_item($teacher_id)
    {
        //整合传入必要分页参数；
        $where['assessment_type'] = $this->input->get('assessment_type');
        $where['teacher_id'] = $teacher_id;
        $where['page'] = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;

        //确定每页显示，初始化总条数；
        $limit = 10;
        $total = 0;

        //默认起始页；
        if (empty($where['page'])) {
            $where['page'] = 1;
        }

        // 返回数组；
        $ranklist = array();
        $ranklist['data'] = $this->rank_model->get_item_list($where, $limit, $total);

        // 返回总条数
        $ranklist['total'] = $total;

        // 返回当前页
        $ranklist['current_page'] = $where['page'];

        // 返回总页数
        $ranklist['total_page'] = ceil($total / $limit);

        $this->ajax_return(200, MESSAGE_SUCCESS, $ranklist);
    }

    // 排行榜整体列表；
    protected function rank_list()
    {
        //整合传入必要分页参数；
        $where['assessment_type'] = $this->input->get('assessment_type');
        $where['teacher_subject'] = $this->input->get('teacher_subject');
        $where['grade_number'] = $this->input->get('grade_number');
        $where['keywords'] = $this->input->get('keywords');

        $where['page'] = intval($this->input->get('page'));
        $where['school_id'] = $this->school_id;

        //确定每页显示，初始化总条数；
        $limit = 10;
        $total = 0;

        //默认起始页；
        if (empty($where['page'])) {
            $where['page'] = 1;
        }

        // 返回数组；
        $ranklist = array();
        $ranklist['data'] = $this->rank_model->get_rank_list($where, $limit, $total);

        // 返回总条数
        $ranklist['total'] = $total;

        // 返回当前页
        $ranklist['current_page'] = $where['page'];

        // 返回总页数
        $ranklist['total_page'] = ceil($total / $limit);
        
        //名次
        $places = ($where['page'] - 1) * $limit+1;

        foreach( $ranklist['data'] as $key=>$value) {
            //把名次插入到查询结果中，方便调用
            $ranklist['data'][$key]->stu_place = $places;
            $places++;
        }

        $this->ajax_return(200, MESSAGE_SUCCESS, $ranklist);
    }

}