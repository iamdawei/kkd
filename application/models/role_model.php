<?php
/**
 * 角色表
 *
 */

class Role_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_role($role_id,$school_id)
    {
        $this->db->select( 'role_id,role_name,role_type' );
        $this->db->from('kkd_role');
        $this->db->where('role_id',$role_id);
        $this->db->where('school_id',$school_id);
        $res = $this->db->get()->result();
        return $res;
    }

    public function get_role_list($school_id)
    {
        $this->db->select('role_id,role_name' );
        $this->db->order_by('role_id', 'ASC');
        $this->db->from('kkd_role');
        $this->db->where('school_id',$school_id);
        $res = $this->db->get()->result();
        return $res;
    }

    public function delete_role($role_id,$school_id)
    {
        $this->db->where('role_id', $role_id);
        $this->db->where('school_id',$school_id);
        $this->db->delete('kkd_role');
        return $this->db->affected_rows();
    }

    public  function add_role($role_array)
    {
       $this->db->insert('kkd_role', $role_array);
        return $this->db->insert_id();
    }

    public  function put_role($role_id,$role_array)
    {
        $this->db->where('role_id', $role_id);
        $this->db->update('kkd_role',$role_array);
        return $this->db->affected_rows();
    }

    public function add_role_batch($role_array)
    {
        $this->db->insert_batch('kkd_role', $role_array);
    }
    public function get_teacher_id($role_id)
    {
        $this->db->select( 'kr.teacher_id,kt.teacher_role' );
        $this->db->from('kkd_teacher_role as kr');
        $this->db->join('kkd_teacher as kt','kr.teacher_id = kt.teacher_id');
        $this->db->where('kr.role_id',$role_id);
        $res = $this->db->get()->result_array();
        return $res;
    }
}