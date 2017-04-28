<?php
/**
 * 评估方法模型；
 * 新增，成功返回：true
 * 修改，成功返回：影响的行数
 * 查询返回的是一个数组，如果为空，则是一个空数组
 */

class Assessment_model extends CI_Model
{
    protected $columns = 'assessment_set_id,assessment_type,assessment_name,have_title,have_content,have_zip,is_open,assessment_number,assessment_role,
                          kkd_assessment_set.file_number';


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

    public function get_list($where=array(), $limit = 8, & $total = null)
    {
        $school_id = isset($where['school_id']) ? intval($where['school_id']) : 0;
        $is_open = isset($where['is_open']) ? $where['is_open'] : null;
        $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
        $keywords = isset($where['keywords']) ? $where['keywords'] : '';
        $file_number = isset($where['file_number']) ? $where['file_number'] : null;

        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (! is_null($total)) {
            $this->db->from('kkd_assessment_set');

            if(! is_null($file_number)) {
                $this->db->where('file_number', $file_number);
            }else{
                $this->db->join('kkd_school_config','kkd_assessment_set.school_id = kkd_school_config.school_id');
                $this->db->where('kkd_assessment_set.file_number = kkd_school_config.file_number');
            }

            if (! is_null($is_open)) {
                $this->db->where('is_open', $is_open);
            }

            if (! is_null($assessment_type)) {
                $this->db->where('assessment_type', $assessment_type);
            }

            if (! empty($keywords)) {
                $this->db->like('assessment_name', $keywords);
            }
            $this->db->where('kkd_assessment_set.school_id', $school_id);
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns);
        $this->db->from('kkd_assessment_set');

        if(! is_null($file_number)) {
            $this->db->where('file_number', $file_number);
        }else{
            $this->db->join('kkd_school_config','kkd_assessment_set.school_id = kkd_school_config.school_id');
            $this->db->where('kkd_assessment_set.file_number = kkd_school_config.file_number');
        }

        if (! is_null($is_open)) {
            $this->db->where('is_open', $is_open);
        }

        if (! is_null($assessment_type)) {
            $this->db->where('assessment_type', $assessment_type);
        }

        if (! empty($keywords)) {
            $this->db->like('assessment_name', $keywords);
        }

        $this->db->where('kkd_assessment_set.school_id', $school_id);
        $this->db->order_by('is_open');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function get_name_list($where,$cols='assessment_set_id,assessment_name')
    {
        if(!is_array($where)) return -1;
        $this->db->from('kkd_assessment_set');
        $this->db->join('kkd_school_config','kkd_assessment_set.file_number = kkd_school_config.file_number');
        $this->db->where($where);
        $this->db->select($cols);
        $this->db->order_by('assessment_type ASC');
        return $this->db->get()->result();
    }

    public function get_info($assessment_set_id,$cols='')
    {
        $cols = ($cols) ? $cols : $this->columns;
        $this->db->select($cols);
        $this->db->where('assessment_set_id',$assessment_set_id);
        $query = $this->db->get('kkd_assessment_set');
        return $query->row_array();
    }

    //提高考核版本方法；
    public function put_file_number($school_id)
    {
        $this->db->where('school_id', $school_id);
        $this->db->set('file_number','file_number + 1',FALSE);
        $this->db->update('kkd_school_config');
        return $this->db->affected_rows();
    }
}
