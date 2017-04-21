<?php
/**
 * 学生方法模型.
 *
 *
 */

class Student_model extends CI_Model
{
    protected $columns = 'student_id,student_name,student_register_number,student_born_date,student_gender,student_indution_date,student_grade,
                          student_status,student_parent,student_parent_phone';

    public function add_student($data)
    {
        return $this->db->insert('kkd_student', $data);
    }

    public function delete_student($student_id)
    {
        $this->db->where('student_id',$student_id);
        $this->db->delete('kkd_student');
        return $this->db->affected_rows();
    }

    public function match_student_info($account,$password)
    {

        $this->db->select('student_id,student_name,student_account,student_photo');
        $this->db->where('student_account',$account);
        $this->db->where('student_password',$password);
        $query = $this->db->get('kkd_student');
        return $query->row_array();
    }

    public function get_student_list($school_id, $student_grade ,$student_class,$keywords,$page = 1, $limit = 12, & $total = null)
    {
        $school_id = intval($school_id);
        $page = intval($page);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if(! is_null($total))
        {
            $this->db->from('kkd_student');
            if (! empty($student_grade))
            {
                $student_grade = intval($student_grade);
                $this->db->where('student_grade', $student_grade);
            }

            if (! empty($student_class))
            {
                $student_class = intval($student_class);
                $this->db->where('student_class', $student_class);
            }

            if (! empty($keywords))
            {
                $this->db->like('student_name', $keywords);
            }
            $this->db->where('school_id', $school_id);
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns);
        $this->db->from('kkd_student');
        if (! empty($student_grade))
        {
            $student_grade = intval($student_grade);
            $this->db->where('student_grade', $student_grade);
        }

        if (! empty($student_class))
        {
            $this->db->where('student_class', $student_class);
        }

        if (! empty($keywords))
        {
            $this->db->like('student_name', $keywords);
        }
        $this->db->where('school_id', $school_id);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

//    public function get_student_list()
//    {
//        $sql = "select $this->columns from kkd_student";
//        $query = $this->db->query($sql);
//        return $query->result();
//    }

    public function get_student($where,$cols='')
    {
        if(!is_array($where)) return -1;
        $cols = ($cols) ? $cols : $this->columns;
        $this->db->select($cols);
        $this->db->where($where);
        $query = $this->db->get('kkd_student');
        return $query->row_array();
    }

    public function change_student_password($user_id,$newpass)
    {
        $sql = "update kkd_student set student_password = '{$newpass}' where student_id = $user_id";
        $query = $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function reset_student_password($student_id)
    {
        $password = md5('000000'.'KKD_SYSTEM');
        $sql = "update kkd_student set student_password = '{$password}' where student_id = $student_id";
        $query = $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function update_student_info($user_id,$data)
    {
        $this->db->where('student_id', $user_id);
        $this->db->update('kkd_student',$data);
        return $this->db->affected_rows();
    }

    public function check_account_unique($account,$user_id = 0)
    {
        $this->db->select('student_id');
        $this->db->where('student_account', $account);
        if($user_id !== 0) $this->db->where('student_id !=', $user_id);
        $query = $this->db->get('kkd_student');
        return $query->row_array();
    }
}