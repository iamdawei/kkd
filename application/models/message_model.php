<?php
/**
 * 消息中心方法模型
 *
 */

class Message_model extends CI_Model
{

    protected $columns = 'message_id,assessment_type,assessment_name,item_title,auditor_datetime,status_descript,message_status';
    public function __construct()
    {
        parent::__construct();
    }

    public function get_message($message_id,$school_id)
    {
        $this->db->select($this->columns);
        $this->db->from('kkd_message');
        $this->db->where('message_id',$message_id);
        $this->db->where('school_id',$school_id);
        $res = $this->db->get()->result();
        return $res;
    }

    public function get_message_list($where=array(), $limit = 10, & $total = null ,& $new_mesage = null)
    {

        $school_id = isset($where['school_id']) ? $where['school_id'] : null;
        $teacher_id = isset($where['teacher_id']) ? $where['teacher_id'] : null;

        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (! is_null($total)) {
            $this->db->from('kkd_message ');
            $this->db->where('teacher_id', $teacher_id);
            $this->db->where('school_id', $school_id);
            $total = $this->db->count_all_results();

        }

        if (! is_null($new_mesage)) {
            $this->db->from('kkd_message ');
            $this->db->where('teacher_id', $teacher_id);
            $this->db->where('school_id', $school_id);
            $this->db->where('message_status',1);
            $new_mesage = $this->db->count_all_results();

        }

        $this->db->select($this->columns);
        $this->db->from('kkd_message');
        $this->db->where('school_id', $school_id);
        $this->db->where('teacher_id',$teacher_id);

        $this->db->order_by('auditor_datetime', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();

    }

    public function delete_message($message_id,$school_id)
    {
        $this->db->where('message_id', $message_id);
        $this->db->where('school_id',$school_id);
        $this->db->delete('kkd_message');
        return $this->db->affected_rows();
    }

    public  function add_message($message_array)
    {
        $this->db->insert('kkd_message', $message_array);
        return $this->db->insert_id();
    }

    public  function put_message($teacher_id)
    {
        $date['message_status'] =0 ;
        $this->db->where('teacher_id', $teacher_id);
        $this->db->update('kkd_message', $date);
        return $this->db->affected_rows();
    }
}