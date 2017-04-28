<?php
/**
 * 排行榜方法模型；
 * 暂时提供get方法；有需求再增加；
 */

class Rank_model extends CI_Model
{
    protected $columns_person = 'assessment_item_id,assessment_name,item_number,item_title,commit_datetime,auditor_name,,ai.file_number';


    public function __construct()
    {
        parent::__construct();
    }

   //教师个人详细列表；
    public function get_item_list($where = array(), $limit = 10, & $total = null)
    {
        $school_id = isset($where['school_id']) ? intval($where['school_id']) : 0;
        $assessment_type = isset($where['assessment_type']) ? $where['assessment_type'] : null;
        $teacher_id = isset($where['teacher_id']) ? $where['teacher_id'] : null;
        $file_number = isset($where['file_number']) ? $where['file_number'] : null;


        $page = intval($where['page']);
        $limit = intval($limit);
        $start = ($page - 1) * $limit;

        if (!is_null($total)) {
            $this->db->from('kkd_assessment_item as ai');

            switch ($file_number){
                case null:
                    $this->db->join('kkd_school_config', 'ai.school_id = kkd_school_config.school_id');
                    $this->db->where('ai.file_number = kkd_school_config.file_number');
                    break;
                case 'all':
                    break;
                default :
                    $this->db->where('file_number', $file_number);
            }

            if (!is_null($assessment_type)) {
                $this->db->where('assessment_type', $assessment_type);
            }

            $this->db->where('ai.school_id', $school_id);
            $this->db->where('teacher_id',$teacher_id);
            $total = $this->db->count_all_results();
        }

        $this->db->select($this->columns_person);
        $this->db->from('kkd_assessment_item as ai');

        switch ($file_number){
            case null:
                $this->db->join('kkd_school_config', 'ai.school_id = kkd_school_config.school_id');
                $this->db->where('ai.file_number = kkd_school_config.file_number');
                break;
            case 'all':
                break;
            default :
                $this->db->where('file_number', $file_number);
        }


        if (!is_null($assessment_type)) {
            $this->db->where('assessment_type', $assessment_type);
        }

        $this->db->where('item_status', 0);
        $this->db->where('ai.school_id', $school_id);
        $this->db->where('teacher_id',$teacher_id);
        $this->db->order_by('commit_datetime','DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }


//准入条件：属于该系统角色的school_id下，属于当前进行的file_number号下,且考核状态item_status为0

//  1、该用户属于100000权限的角色组成员（也就是普通教师=被考核者）

//  2、以教师表为主表left join，避免教师没设置班级而导致统计丢失，如果查询教师名或学科，应该在此层级进行where查询

//  3、temp_grade临时表为了保证不进行重复求和，避免老师任教多个班级时的多次求和问题，如果要查询某个班级的所有老师，应该在temp_grade的来源数据中进行where查询
    public function rank_list($where = array(), $limit = 10, & $total = null)
    {
        //过滤接受的参数
        $assessment_type = (isset($where['assessment_type']) && !empty($where['assessment_type'])) ? $where['assessment_type'] : 0;
        $teacher_subject = (isset($where['teacher_subject']) && !empty($where['teacher_subject'])) ? $where['teacher_subject'] : null;
        $grade_number = intval(($where['grade_number']));
        $keywords = (isset($where['keywords']) && !empty($where['keywords']))? $where['keywords'] :'';
        $teacher_join = 'left';
        //系统取的参数可以不过滤
        $school_id = $where['school_id'];
        $file_number = $where['file_number'];

        $page = intval($where['page']);
        $start = ($page - 1) * $limit;

        $total = $this->rank_count($school_id,$file_number,$teacher_subject,$grade_number,$keywords);
        if($total === 0) return false;

        if (! is_null($teacher_subject))
            $teacher_subject = " and tea.teacher_subject='$teacher_subject'";
        else $teacher_subject ='';

        if ($grade_number)
        {
            $teacher_join='';
            $grade_number = " where tc.grade_number = '$grade_number'";
        }
        else
            $grade_number = '';


        if (! empty($keywords))
            $keywords = " and tea.teacher_name like '%$keywords%'";
        else $keywords ='';

        switch ($assessment_type) {
            case 1 :
                $assessment_type = 'sum_type1';
                break;
            case 2 :
                $assessment_type = 'sum_type2';
                break;
            default:
                $assessment_type = 'sum_type0';
                break;
        }

        $query_str = "select tea.teacher_id,tea.teacher_name,tea.teacher_subject,IFNULL(sun_t.grade_number,0) grade_number,IFNULL(sun_t.sum_type0,0) sum_type0,IFNULL(sun_t.sum_type1,0) sum_type1,IFNULL(sun_t.sum_type2,0) sum_type2 from kkd_teacher as tea
$teacher_join JOIN (
  select
  SUM(IF(assessment_type = 0,item_number,0))AS sum_type0,
  SUM(IF(assessment_type = 1,item_number,0))AS sum_type1,
  SUM(IF(assessment_type = 2,item_number,0))AS sum_type2,
  min(ai.teacher_id) as teacher_id,
  min(temp_grade.grade_number) as grade_number
  from kkd_assessment_item as ai
  JOIN (select min(tc.grade_number) as grade_number,MAX(teacher_id) as teacher_id from kkd_teacher_class tc $grade_number GROUP BY teacher_id) as temp_grade on .temp_grade.teacher_id = ai.teacher_id
  where ai.file_number = '100001' and ai.school_id = 1 and ai.item_status = 0
  group by ai.teacher_id
) as sun_t on tea.teacher_id = sun_t.teacher_id
JOIN kkd_teacher_role as tr on tea.teacher_id = tr.teacher_id
where tr.role_id = 100000 $teacher_subject $keywords
order by $assessment_type DESC limit $start,$limit";

        $query = $this->db->query($query_str);
        return $query->result_array();
    }

    public function rank_count($school_id,$file_number,$teacher_subject,$grade_number,$keywords)
    {
        $teacher_join = 'left';
        if (! is_null($teacher_subject))
            $teacher_subject = " and tea.teacher_subject='$teacher_subject'";
        else $teacher_subject ='';

        if ($grade_number)
        {
            $teacher_join='';
            $grade_number = " where tc.grade_number = '$grade_number'";
        }
        else
            $grade_number = '';

        if (! empty($keywords))
            $keywords = " and tea.teacher_name like '%$keywords%'";
        else $keywords ='';

        $query_str = "select count(*) as count_number from kkd_teacher as tea
$teacher_join JOIN (
  select
  min(ai.teacher_id) as teacher_id
  from kkd_assessment_item as ai
  JOIN (
    select min(tc.grade_number) as grade_number,MAX(teacher_id) as teacher_id from kkd_teacher_class tc $grade_number GROUP BY teacher_id) as temp_grade on .temp_grade.teacher_id = ai.teacher_id
  where ai.file_number = '$file_number' and ai.school_id = $school_id and ai.item_status = 0
  group by ai.teacher_id
) as sun_t on tea.teacher_id = sun_t.teacher_id
JOIN kkd_teacher_role as tr on tea.teacher_id = tr.teacher_id
where tr.role_id = 100000 $teacher_subject $keywords";

        $query = $this->db->query($query_str);
        $row = $query->row();

        if (isset($row))
        {
            return $row->count_number;
        }
        else return 0;
    }
}
