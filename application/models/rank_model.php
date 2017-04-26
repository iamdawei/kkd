<?php
/**
 * 排行榜方法模型；
 * 暂时提供get方法；有需求再增加；
 */

class Rank_model extends CI_Model
{
    protected $columns_person = 'assessment_item_id,assessment_name,item_number,item_title,item_content,item_zip,commit_datetime,auditor_name';


    public function __construct()
    {
        parent::__construct();
    }

    //整体排行榜方法；
    public function get_rank_list($where = array(), $limit = 10, & $total = null)
    {

        $school_id = isset($where['school_id']) ? intval($where['school_id']) : 0;
        $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
        $teacher_subject = isset($where['teacher_subject']) ? $where['teacher_subject'] : null;
        $grade_number = isset($where['grade_number']) ? $where['grade_number'] : null;
        $keywords = isset($where['keywords']) ? $where['keywords'] :'';

        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (! is_null($total)) {
            $this->db->select('kt.teacher_name ,teacher_subject ,grade_number,
                           SUM(IF(assessment_type = 0,teacher_subtotal,0))AS sum_type0,
                           SUM(IF(assessment_type = 1,teacher_subtotal,0))AS sum_type1,
                           SUM(IF(assessment_type = 2,teacher_subtotal,0))AS sum_type2');
            $this->db->from('kkd_teacher as kt');
            $this->db->join('kkd_teacher_class as tc','kt.teacher_id = tc.teacher_id');
            $this->db->join('kkd_assessment_item as ai','kt.teacher_id = ai.teacher_id');

            if (! is_null($teacher_subject)) {
                $this->db->where('teacher_subject', $teacher_subject);
            }

            if (! is_null($grade_number)) {
                $this->db->where('tc.grade_number', $grade_number);
            }

            if (! empty($keywords)) {
                $this->db->like('kt.teacher_name', $keywords);
            }

            $this->db->where('ai.school_id', $school_id);
            $this->db->group_by('ai.teacher_id');
            $total = $this->db->count_all_results();
        }

        $this->db->select('kt.teacher_name ,teacher_subject ,grade_number,
                           SUM(IF(assessment_type = 0,teacher_subtotal,0))AS sum_type0,
                           SUM(IF(assessment_type = 1,teacher_subtotal,0))AS sum_type1,
                           SUM(IF(assessment_type = 2,teacher_subtotal,0))AS sum_type2');
        $this->db->from('kkd_teacher as kt');
        $this->db->join('kkd_teacher_class as tc','kt.teacher_id = tc.teacher_id');
        $this->db->join('kkd_assessment_item as ai','kt.teacher_id = ai.teacher_id');

        if (! is_null($teacher_subject)) {
            $this->db->where('teacher_subject', $teacher_subject);
        }

        if (! is_null($grade_number)) {
            $this->db->where('tc.grade_number', $grade_number);
        }

        if (! empty($keywords)) {
            $this->db->like('kt.teacher_name', $keywords);
        }


        $this->db->where('ai.school_id', $school_id);
        $this->db->group_by('ai.teacher_id');
        if (! is_null($assessment_type)){
            switch ($assessment_type) {
                case 0 : $this->db->order_by('sum_type0','DESC');
                    break;
                case 1 : $this->db->order_by('sum_type1','DESC');
                    break;
                case 2 : $this->db->order_by('sum_type2','DESC');
                    break;
            }
        }else {
            $this->db->order_by('sum_type0','DESC');
        }

        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

   //教师个人详细列表；
    public function get_item_list($where = array(), $limit = 10, & $total = null)
    {

        $school_id = isset($where['school_id']) ? intval($where['school_id']) : 0;
        $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
        $teacher_id = isset($where['teacher_id']) ? $where['teacher_id'] : null;


        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (!is_null($total)) {
            $this->db->from('kkd_assessment_item');

            if (!is_null($assessment_type)) {
                $this->db->where('assessment_type', $assessment_type);
            }

            $this->db->where('school_id', $school_id);
            $this->db->where('teacher_id',$teacher_id);
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns_person);
        $this->db->from('kkd_assessment_item');

        if (!is_null($assessment_type)) {
            $this->db->where('assessment_type', $assessment_type);
        }

        $this->db->where('school_id', $school_id);
        $this->db->where('teacher_id',$teacher_id);
        $this->db->order_by('commit_datetime','DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }
}
