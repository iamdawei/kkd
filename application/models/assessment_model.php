<?php
/**
 * 评估方法模型；
 * 新增，成功返回：true
 * 修改，成功返回：影响的行数
 * 查询返回的是一个数组，如果为空，则是一个空数组
 */

class Assessment_model extends CI_Model
{
    protected $columns = 'assessment_set_id,assessment_type,assessment_name,have_title,have_content,have_zip,is_open,assessment_number,assessment_role';

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->insert('kkd_assessment_set', $data);
        return $this->db->insert_id();
    }

    public function delete($assessment_set_id)
    {
        $this->db->where('assessment_set_id', $assessment_set_id);
        $this->db->delete('kkd_assessment_set');
        return $this->db->affected_rows();
    }

    public function put($assessment_set_id, $data)
    {
        $this->db->where('assessment_set_id', $assessment_set_id);
        $this->db->update('kkd_assessment_set', $data);
        return $this->db->affected_rows();
    }

    public function get($assessment_set_id = false,$where=array(), $limit = 8, & $total = null)
    {
        if ($assessment_set_id === false) {

            $school_id = isset($where['school_id']) ? intval($where['school_id']) : 0;
            $is_open = isset($where['is_open']) ? $where['is_open'] : null;
            $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
            $keywords = isset($where['keywords']) ? $where['keywords'] : '';

            $page = intval($where['page']);
            $limit = intval($limit);
            $start = ($page - 1) * $limit;

            if (! is_null($total)) {
                $this->db->from('kkd_assessment_set');
                if (! is_null($is_open)) {
                    $this->db->where('is_open', $is_open);
                }

                if (! is_null($assessment_type)) {
                    $this->db->where('assessment_type', $assessment_type);
                }

                if (! empty($keywords)) {
                    $this->db->like('assessment_name', $keywords);
                }
                $this->db->where('school_id', $school_id);
                $total = $this->db->count_all_results();
            }

            $this->db->select($this->columns);
            $this->db->from('kkd_assessment_set');
            if (! is_null($is_open)) {
                $this->db->where('is_open', $is_open);
            }

            if (! is_null($assessment_type)) {
                $this->db->where('assessment_type', $assessment_type);
            }

            if (! empty($keywords)) {
                $this->db->like('assessment_name', $keywords);
            }

            $this->db->where('school_id', $school_id);
            $this->db->order_by('is_open');
            $this->db->limit($limit, $start);
            return $this->db->get()->result();
        } else {
            return $this->_item($assessment_set_id);
        }
    }

       protected function _item($assessment_set_id)
        {
            $cols = $this->columns;
            $this->db->select($cols);
            $this->db->where('assessment_set_id',$assessment_set_id);
            $query = $this->db->get('kkd_assessment_set');
            return $query->row_array();
       }

}
