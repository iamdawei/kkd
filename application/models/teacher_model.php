<?php
/**
 * 教师模型方法
 * 新增，成功返回：true
 * 修改，成功返回：影响的行数
 * 查询返回的是一个数组，如果为空，则是一个空数组
 */

class Teacher_model extends CI_Model
{
    protected $columns = 'teacher_id,teacher_account,teacher_photo,teacher_name,teacher_email,teacher_born_date,teacher_gender,
    teacher_indution_date,teacher_subject,teacher_class,teacher_role';

    public function __construct()
    {
        parent::__construct();
    }

    public function add_teacher($data)
    {
        $this->db->insert('kkd_teacher', $data);
        return $this->db->insert_id();
    }

    public function delete_teacher($teacher_id)
    {
        $this->db->where('teacher_id', $teacher_id);
        $this->db->delete('kkd_teacher');
        return $this->db->affected_rows();
    }


    public function get_teacher_list($school_id, $teacher_class ,$teacher_subject,$keywords,$page = 1, $limit = 12, & $total = null)
    {
        $school_id = intval($school_id);
        $page = intval($page);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if(! is_null($total))
        {
            $this->db->from('kkd_teacher');
            if (! empty($teacher_class))
            {
                $teacher_class = intval($teacher_class);
                $this->db->like('teacher_class', $teacher_class.":");
            }

            if (! empty($teacher_subject))
            {
                $this->db->where('teacher_subject', $teacher_subject);
            }

            if (! empty($keywords))
            {
                $this->db->like('teacher_name', $keywords);
            }
            $this->db->where('school_id', $school_id);
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns);
        $this->db->from('kkd_teacher');
        if (! empty($teacher_class))
        {
            $teacher_class = intval($teacher_class);
            $this->db->like('teacher_class', $teacher_class.":");
        }

        if (! empty($teacher_subject))
        {
            $this->db->where('teacher_subject', $teacher_subject);
        }

        if (! empty($keywords))
        {
            $this->db->like('teacher_name', $keywords);
        }
        $this->db->where('school_id', $school_id);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    public function get_teacher($where,$cols='')
    {
        if(!is_array($where)) return -1;
        $cols = ($cols) ? $cols : $this->columns;
        $this->db->select($cols);
        $this->db->where($where);
        $query = $this->db->get('kkd_teacher');
        return $query->row_array();
    }

    public function get_teacher_class($uid)
    {
        $this->db->select('teacher_class_id,grade_number,class_number');
        $this->db->where("teacher_id = $uid");
        $query = $this->db->get('kkd_teacher_class');
        return $query->result();
    }

    public function change_teacher_password($user_id, $newpass)
    {
        $sql = "update kkd_teacher set teacher_password = '{$newpass}' where teacher_id = $user_id";
        $query = $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function update_password($teacher_id,$pass)
    {
        if(!$pass) return -1;
        $this->db->where('teacher_id', $teacher_id);
        $data['teacher_password']=md5($pass.ENCRYPT_KEY);
        $this->db->update('kkd_teacher',$data);
        return $this->db->affected_rows();
    }

    public function update_teacher_info($user_id, $data)
    {
        $this->db->where('teacher_id', $user_id);
        $this->db->update('kkd_teacher', $data);
        return $this->db->affected_rows();
    }

    public function check_account_unique($account,$user_id = 0)
    {
        $this->db->select('teacher_id');
        $this->db->where('teacher_account', $account);
        if($user_id !== 0) $this->db->where('teacher_id !=', $user_id);
        $query = $this->db->get('kkd_teacher');
        return $query->row_array();
    }
}