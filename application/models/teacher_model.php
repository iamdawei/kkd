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


    public function get_teacher_list($school_id,$teacher_role, $teacher_class ,$teacher_subject,$keywords,$page = 1, $limit = 12, & $total = null)
    {
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
            if(! empty($teacher_role))
            {
                $this->db->like('teacher_role',$teacher_role);
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

        if(! empty($teacher_role))
        {
            $this->db->like('teacher_role',$teacher_role);
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
        $this->db->from('kkd_teacher as tea');
        $this->db->join( 'kkd_school as sch','tea.school_id = sch.school_id');
        $this->db->where($where);
        $this->db->where('school_open',1);
        return $this->db->get()->row_array();
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
    public function set_teacher_class($teacher_id,$class_array,$ex_delete = true)
    {
        if($ex_delete === true) $this->delete_teacher_class($teacher_id);
        $this->db->insert_batch('kkd_teacher_class',$class_array);
    }
    public function delete_teacher_class($teacher_id)
    {
        $this->db->where('teacher_id', $teacher_id);
        $this->db->delete('kkd_teacher_class');
        return $this->db->affected_rows();
    }
    //todo 批量修改：
    public function put_teacher_batch($data)
    {
        $this->db->update_batch('kkd_teacher', $data,'teacher_id');
        return $this->db->affected_rows();
    }
}