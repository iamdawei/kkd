<?php
/**
 * 权限关系表
 *
 */

class Auth_role_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_auth_group($u_id)
    {
        $this->db->select( 'ar.auth_id,auth_controller,auth_method,auth_action,group_name,group_model_name,group_sort' );
        $this->db->from('kkd_teacher_role as tr');
        $this->db->join( 'kkd_auth_role as ar','tr.role_id = ar.role_id');
        $this->db->join( 'kkd_auth as au','au.auth_id = ar.auth_id');
        $this->db->join( 'kkd_auth_group as ag','ag.group_id = au.group_id');
        $this->db->where('teacher_id',$u_id);
        $this->db->order_by('group_sort','ASC');
        $res = $this->db->get()->result();
        return $res;
    }

    public function get_role_list()
    {
        $this->db->select( 'role_id,role_name' );
        $this->db->from('kkd_role');
        $res = $this->db->get()->result();
        return $res;
    }

    //设置教师权限,支持批量新增
    //特殊操作：先删除关联数据，后新增
    public function set_teacher_role($teacher_id,$data,$type='batch',$ex_delete = true)
    {
        if($ex_delete === true) $this->delete_teacher_role($teacher_id);
        //批量插入
        if($type === 'batch') $this->db->insert_batch('kkd_teacher_role', $data);
        else $this->db->insert('kkd_teacher_role',$data);

        return $this->db->affected_rows();
    }

    public function delete_teacher_role($teacher_id)
    {
        $this->db->where('teacher_id', $teacher_id);
        $this->db->delete('kkd_teacher_role');
        return $this->db->affected_rows();
    }

    public  function add_teacher_role($teacher_id,$role_id)
    {
        $data['teacher_id'] = $teacher_id;
        $data['role_id'] = $role_id;
        $va = $this->db->insert('kkd_teacher_role', $data);
        return $va;
    }
}