<?php
/**
 * 提交审核方法模型；
 * 新增，成功返回：true
 * 修改，成功返回：影响的行数
 * 查询返回的是一个数组，如果为空，则是一个空数组
 *
 * TODO 2017-04-27 优化功能
 * 所有的查询列，不采取columns_set，columns_detail，columns_user这种内置的方式
 * 如果需要有获取的参数，全部由控制器中传入cols参数，如果cols参数为空，则返回-1
 */

class Assessment_item_model extends CI_Model
{
    //管理员取值
    protected $columns_set = 'assessment_item_id,assessment_type,ai.assessment_set_id,assessment_name,item_title,item_number,teacher_name,commit_datetime';
    //单条申请内容详细
    protected $columns_detail ='assessment_item_id,assessment_type,assessment_set_id,assessment_name,item_title,item_number,teacher_name,commit_datetime,item_status';
    //用户个人申请记录；
    protected $columns_user = 'assessment_item_id,assessment_type,item_title,commit_datetime,item_status,status_descript,assessment_name';

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->insert('kkd_assessment_item', $data);
        return $this->db->insert_id();
    }

    public function delete($assessment_item_id)
    {
        $this->db->where('item_id', $assessment_item_id);
        $this->db->delete('kkd_item_file');

        $this->db->where('assessment_item_id', $assessment_item_id);
        $this->db->delete('kkd_assessment_item');

        return $this->db->affected_rows();
    }

    public function put($assessment_item_id, $data)
    {
        $this->db->where('assessment_item_id', $assessment_item_id);
        $this->db->update('kkd_assessment_item', $data);
        return $this->db->affected_rows();
    }
    //审核判断方法
    public function put_status($item_array, $data)
    {
        if(! is_array($item_array)) return -1;
        $this->db->where_in('assessment_item_id', $item_array);
        $this->db->where('item_status',1);
        $this->db->update('kkd_assessment_item', $data);
        return $this->db->affected_rows();
    }

    //管理员取列表；
    public function get($where=array(), $limit = 10, & $total = null)
    {
        $school_id = isset($where['school_id']) ? $where['school_id'] : null;
        $teacher_id = isset($where['teacher_id']) ? $where['teacher_id'] : null;
        $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
        $keywords = isset($where['keywords']) ? $where['keywords'] : '';

        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (! is_null($total)) {
            $this->db->from('kkd_assessment_item as ai');
            $this->db->join('kkd_assessment_role as ar','ai.assessment_set_id = ar.assessment_set_id');
            $this->db->join('kkd_teacher_role as tr','tr.role_id = ar.role_id');
            $this->db->where('ai.school_id',$school_id);
            $this->db->where('ai.item_status',1);
            $this->db->where('tr.teacher_id',$teacher_id);

            if (! is_null($assessment_type)) {
                $this->db->where('assessment_type', $assessment_type);
            }

            if (! empty($keywords))
            {
                $this->db->like('assessment_name', $keywords);
            }

            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns_set);
        $this->db->from('kkd_assessment_item as ai');
        $this->db->join('kkd_assessment_role as ar','ai.assessment_set_id = ar.assessment_set_id','ai.school_id = ar.school_id');
        $this->db->join('kkd_teacher_role as tr','tr.role_id = ar.role_id');
        $this->db->where('ai.school_id',$school_id);
        $this->db->where('ai.item_status',1);
        $this->db->where('tr.teacher_id',$teacher_id);

        if (! is_null($assessment_type)) {
            $this->db->where('ai.assessment_type', $assessment_type);
        }
        if (! empty($keywords)) {
            $this->db->like('ai.assessment_name', $keywords);
        }

        $this->db->order_by('ai.assessment_type');
        $this->db->limit($limit, $start);

        return $this->db->get()->result();

    }


    //用户查看个人提交的申请；
    public function get_list($where=array(), $limit = 10, & $total = null)
    {

        $school_id = isset($where['school_id']) ? $where['school_id'] : null;
        $teacher_id = isset($where['teacher_id']) ? $where['teacher_id'] : null;
        $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
        $keywords = isset($where['keywords']) ? $where['keywords'] : '';
        $item_status = isset($where['item_status']) ? $where['item_status'] : null;

        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (! is_null($total)) {

            $this->db->from('kkd_assessment_item ');
            $this->db->where('teacher_id', $teacher_id);
            $this->db->where('school_id', $school_id);
            if (! is_null($item_status)) {
                $this->db->where('item_status', $item_status);
            }
            if (! is_null($assessment_type)) {
                $this->db->where('assessment_type', $assessment_type);
            }
            if (! empty($keywords)) {
                $this->db->like('item_title', $keywords);
            }
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns_user);
        $this->db->from('kkd_assessment_item');
        if (! is_null($assessment_type)) {
            $this->db->where('assessment_type', $assessment_type);
        }
        if (! empty($keywords)) {
            $this->db->like('item_title', $keywords);
        }
        if (! is_null($item_status)) {
            $this->db->where('item_status', $item_status);
        }
        $this->db->where('teacher_id', $teacher_id);
        $this->db->where('school_id', $school_id);
        $this->db->order_by('item_status DESC,commit_datetime DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();

    }

    public function get_item($assessment_item_id,$cols='')
    {
        $cols = ($cols) ? $cols : $this->columns_detail;
        $this->db->select($cols);
        $this->db->where('assessment_item_id',$assessment_item_id);
        $query = $this->db->get('kkd_assessment_item');
        return $query->row_array();
    }

    public function get_item_file($item_id)
    {
        $this->db->from('kkd_item_file');
        $this->db->select('file_id,file_name,file_real_name');
        $this->db->where('item_id', $item_id);
        return $this->db->get()->result();
    }

    public function file_insert_batch($data)
    {
        $va = $this->db->insert_batch('kkd_item_file', $data);
        return $va;
    }

    public function file_delete($file_id)
    {
        $this->db->where('file_id', $file_id);
        $this->db->delete('kkd_item_file');
        return $this->db->affected_rows();
    }
}
