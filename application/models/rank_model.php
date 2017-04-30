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

    public function get_item_list($data = array(), $limit = 10, & $total = 0,$page=1)
    {
        $where['assessment_type'] = isset($data['assessment_type']) ? $data['assessment_type'] : 0;
        $where['teacher_id'] = isset($data['teacher_id']) ? $data['teacher_id'] : 0;
        $where['ai.school_id'] = $data['school_id'];
        $where['file_number'] =  $data['file_number'];
        $where['item_status'] =  0;
        $start = ($page - 1) * $limit;
        $this->db->from('kkd_assessment_item as ai');
        $this->db->where($where);
        $total = $this->db->count_all_results();
        $this->db->select($this->columns_person);
        $this->db->from('kkd_assessment_item as ai');
        $this->db->where($where);
        $this->db->order_by('commit_datetime','DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }


//准入条件：属于该系统角色的school_id下，属于当前进行的file_number号下,且考核状态item_status为0，用户属于100000（也就是普通教师/被考核者）
//2017-4-8 22:29 修改之后的版本：
//  1、先将teacher（当查询班级时以此为主表）与temp_grade班级信息进行join，这个步骤将会得到所有的用户（如果查询班级，则会忽略未设置班级信息的用户），
//  2、根据条件统计主要数据源（三项考核得分），并将数据源进行join，这个步骤会得到教师与其标准（默认取专业标准）的统计数据
//  3、关联用户与权限表，此步骤将筛选出参与考核的用户提交的数据（避免垃圾数据影响）
    public function rank_list($where = array(), $limit = 10, & $total = 0,$page)
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

        $start = ($page - 1) * $limit;

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

        $total = $this->rank_count($school_id,$file_number,$teacher_subject,$grade_number,$keywords,$teacher_join);
        if($total === 0) return false;

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

        $query_str="select tea.teacher_id,tea.teacher_name,tea.teacher_subject,IFNULL(temp_grade.grade_number,0) grade_number,IFNULL(sun_t.sum_type0,0) sum_type0,IFNULL(sun_t.sum_type1,0) sum_type1,IFNULL(sun_t.sum_type2,0) sum_type2
from kkd_teacher as tea
$teacher_join JOIN (select min(tc.grade_number) as grade_number,MAX(teacher_id) as teacher_id from kkd_teacher_class tc $grade_number  GROUP BY teacher_id) as temp_grade on tea.teacher_id = temp_grade.teacher_id
left JOIN (
  select
  SUM(IF(assessment_type = 0,item_number,0))AS sum_type0,
  SUM(IF(assessment_type = 1,item_number,0))AS sum_type1,
  SUM(IF(assessment_type = 2,item_number,0))AS sum_type2,
  min(ai.teacher_id) as teacher_id
  from kkd_assessment_item as ai
  where ai.file_number = '$file_number' and ai.school_id = $school_id and ai.item_status = 0
  group by ai.teacher_id
) as sun_t on tea.teacher_id = sun_t.teacher_id
JOIN kkd_teacher_role as tr on tea.teacher_id = tr.teacher_id
where tr.role_id = 100000 $teacher_subject $keywords
order by $assessment_type DESC limit $start,$limit";

        $query = $this->db->query($query_str);
        return $query->result_array();
    }

    public function rank_count($school_id,$file_number,$teacher_subject,$grade_number,$keywords,$teacher_join)
    {
        $query_str = "select count(*) as count_number from kkd_teacher as tea
$teacher_join JOIN (select min(tc.grade_number) as grade_number,MAX(teacher_id) as teacher_id from kkd_teacher_class tc $grade_number  GROUP BY teacher_id) as temp_grade on tea.teacher_id = temp_grade.teacher_id
left JOIN (
  select
  min(ai.teacher_id) as teacher_id
  from kkd_assessment_item as ai
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
