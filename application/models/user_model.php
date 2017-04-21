<?php
/**
 * Created by PhpStorm.
 * User: edu
 * Date: 2017/3/28
 * Time: 14:10
 */

class User_model extends CI_model{

    public function __construct()
    {
    parent::__construct();
    }

    /**
     * AddNewStudent / AddNewTeacher
     * @return bool
     * @param $data
    */
    public function add_student($data)
    {
        $this->db->insert('kkd_student', $data);
    }

    public function add_teacher($data)
    {
        $this->db->insert('kkd_teacher',$data);
    }

    /**
     * DeleteStudent/Teacher
     */
    public function delete_student($student_id)
    {
        $this->db->where('student_id',$student_id);
        $this->db->delete('kkd_student');
    }

    public function delete_teacher($teacher_id)
    {
        $this->db->where('teacher_id',$teacher_id);
        $this->db->delete('kkd_teacher');
    }

    /**
     * MatchStudentByNamePassword
     * @param $account
     * @param $password
    */
    public function match_student_info($account,$password)
    {

      $this->db->select('student_id,student_name,student_account,student_photo');
      $this->db->where('student_account',$account);
      $this->db->where('student_password',$password);
      $query = $this->db->get('kkd_student');
      return $query->row_array();
    }

    public function match_teacher_info($account,$password)
    {
        $this->db->select('teacher_id,teacher_name,teacher_account,teacher_photo');
        $this->db->where('teacher_account',$account);
        $this->db->where('teacher_password',$password);
        $query = $this->db->get('kkd_teacher');
        return $query->row_array();
    }
    /**
     * GetStudent & TeacherList
     */
    public function get_student_list()
    {
        $sql = "select * from kkd_student";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function get_teacher_list()
    {
        $sql = "select * from kkd_teacher";
        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * GetUserInfo: student/teacher
     * @param $user_id
     */
    public function get_student_info($user_id)
    {
        $sql = "select * from kkd_student where student_id = $user_id";
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    public function get_teacher_info($user_id)
    {

        $sql = "select * from kkd_teacher where teacher_id = $user_id";
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    /**
     * ChangeStudent / TeacherPasswrod
     *
     * @param $user_id
     * @param $newpass
     * @return bool
     */
     public function change_student_password($user_id,$newpass)
     {
         $sql = "update kkd_student set student_password = '{$newpass}' where student_id = $user_id";
         $query = $this->db->query($sql);
         return $this->db->affected_rows();
     }

     public function change_teacher_password($user_id,$newpass)
     {
         $sql = "update kkd_teacher set teacher_password = '{$newpass}' where teacher_id = $user_id";
         $query = $this->db->query($sql);
         return $this->db->affected_rows();
     }


      /**
       * ResetStudent / TeacherPassword
       */
    public function reset_student_password($student_register_number)
      {
          $password = md5(md5('00000000').'LOREN');
          $sql = "update kkd_student set student_password = '{$password}' where student_register_number = $student_register_number";
          $query = $this->db->query($sql);
          return $this->db->affected_rows();
      }

    public function reset_teacher_password($teacher_account)
    {
        $password = md5(md5('00000000').'LOREN');
        $sql = "update kkd_teacher set teacher_password = '{$password}' where teacher_account = $teacher_account";
        $query = $this->db->query($sql);
        return $this->db->affected_rows();
    }

    /**
     * UpdateStudent / Teacherinfo
     */


     public function update_teacher_info($user_id,$data)
     {
         $this->db->where('teacher_id', $user_id);
         $this->db->update('kkd_teacher',$data);
         return $this->db->affected_rows();
     }

    public function update_student_info($user_id,$data)
    {
        $this->db->where('student_id', $user_id);
        $this->db->update('kkd_student',$data);
        return $this->db->affected_rows();
    }

}



