<?php
/**
 * 教师模型方法
 * 新增，成功返回：true
 * 修改，成功返回：影响的行数
 * 查询返回的是一个数组，如果为空，则是一个空数组
 */

class School_model extends CI_Model
{
    protected $columns = 'config_id,school_id,day_periods,school_subject,school_grade_class,grade_1,grade_2,grade_3,grade_4,grade_5,grade_6,fixed_period';

    public function __construct()
    {
        parent::__construct();
    }

    public function add()
    {
    }

    public function delete()
    {
    }

    public function set()
    {
    }

    public function get($school_id,$cols='')
    {
        $cols = ($cols) ? $cols : $this->columns;
        $this->db->select($cols);
        $query = $this->db->get_where('kkd_school_config', array('school_id' => $school_id));
        return $query->row_array();
    }

    //list是关键字，加 _ 前缀符
    public function _list()
    {
    }
}