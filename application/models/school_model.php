<?php
/**
 * 学校方法模型
 * 新增，成功返回：true
 * 修改，成功返回：影响的行数
 * 查询返回的是一个数组，如果为空，则是一个空数组
 */

class School_model extends CI_Model
{
    protected $columns = 'config_id,school_id,day_periods,school_subject,school_grade_class,grade_1,grade_2,grade_3,grade_4,grade_5,grade_6,fixed_period';
    protected $sch_columns = 'school_id,school_name,school_address,school_contact,school_contact_type,school_open,register_datetime';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_info($school_id,$cols='')
    {
        $cols = ($cols) ? $cols : $this->sch_columns;
        $this->db->select($cols);
        $this->db->where('school_id',$school_id);
        $query = $this->db->get('kkd_school');
        return $query->row_array();
    }
    public function get_list($where=array(), $limit = 10, & $total = null )
    {
        $school_open = isset($where['school_open']) ? $where['school_open'] : null;
        $keywords = isset($where['keywords']) ? $where['keywords'] : '';
        $page = $where['page'];
        $start = ($page - 1) * $limit;

        if (! is_null($total)) {
            $this->db->from('kkd_school');
            if (! is_null($school_open)) {
                $this->db->where('school_open', $school_open);
            }

            if (! empty($keywords)) {
                $this->db->like('school_name', $keywords);
            }
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->sch_columns);
        $this->db->from('kkd_school');
        if (! is_null($school_open)) {
            $this->db->where('school_open', $school_open);
        }

        if (! empty($keywords)) {
            $this->db->like('school_name', $keywords);
        }
        $this->db->order_by('school_id');
        $this->db->limit($limit, $start);
        return $this->db->get()->result_array();
    }

    public function add($data)
    {
        $this->db->insert('kkd_school', $data);
        return $this->db->insert_id();
    }


    public function put($school_id,$data)
    {
        $this->db->where('school_id', $school_id);
        $this->db->update('kkd_school', $data);
        return $this->db->affected_rows();
    }

    //下面是学校设置方法列；
    public function get($school_id,$cols='')
    {
        $cols = ($cols) ? $cols : $this->columns;
        $this->db->select($cols);
        $query = $this->db->get_where('kkd_school_config', array('school_id' => $school_id));
        return $query->row_array();
    }

    public function add_config($data)
    {
        $this->db->insert('kkd_school_config', $data);
        return $this->db->insert_id();
    }

    public function put_config($school_id,$data)
    {
        $this->db->where('school_id', $school_id);
        $this->db->update('kkd_school_config', $data);
        return $this->db->affected_rows();
    }
}