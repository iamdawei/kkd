<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 程序控制器默认入口
 * 登录，登出，分配Token
 */

class Home extends WEB_Conotroller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"http://cdn.hcharts.cn/highcharts/highcharts.js\"></script>";
        $this->load->view('header');
        $this->load->model('school_model');
        $datas = $this->school_model->get($this->school_id,'file_number');
        $file_number = $datas['file_number'];
        $user_role_type = $_SESSION['user_role_type'];
        if($user_role_type == 'normal'){
            $this->_home_teacher($file_number);
        }
        else if($user_role_type == 'hybrid')
        {
            $page_user_role_type = $this->input->get('all');
            if($page_user_role_type !== null) $this->_home_all($file_number);
            else $this->_home_teacher($file_number);
        }else $this->_home_all($file_number);
        $this->load->view('footer',$data);
    }

    private function _home_all($file_number)
    {
        //教师统计
        $count_querty = 'select count(*) as teacher_count from kkd_teacher WHERE school_id = '.$this->school_id;
        $count_data = $this->db->query($count_querty)->row_array();
        $main['count_result'] = $count_data['teacher_count'];

        //三项标准统计

        $count_querty = "SELECT sum(assessment_type = 0) as t0,sum(assessment_type = 1) as t1,sum(assessment_type = 2) as t2 FROM kkd_assessment_item
where item_status = 0 and school_id = $this->school_id and file_number = $file_number";
        $count_data = $this->db->query($count_querty)->row_array();
        $main['count_type'] = $count_data;
        //TOP 10 统计
        $count_querty = "select  SUM(item_number) as sum_type, min(ai.teacher_id) as teacher_id,min(ai.teacher_name) as teacher_name from kkd_assessment_item as ai
  where ai.file_number = $file_number and ai.school_id = $this->school_id and ai.item_status = 0 and assessment_type = 0 group by ai.teacher_id order by sum_type DESC LIMIT 10";
        $main['count_top10_0'] = $this->db->query($count_querty)->result_array();
        $count_querty = "select  SUM(item_number) as sum_type, min(ai.teacher_id) as teacher_id,min(ai.teacher_name) as teacher_name from kkd_assessment_item as ai
  where ai.file_number = $file_number and ai.school_id = $this->school_id and ai.item_status = 0 and assessment_type = 1 group by ai.teacher_id order by sum_type DESC LIMIT 10";
        $main['count_top10_1'] = $this->db->query($count_querty)->result_array();
        $count_querty = "select  SUM(item_number) as sum_type, min(ai.teacher_id) as teacher_id,min(ai.teacher_name) as teacher_name from kkd_assessment_item as ai
  where ai.file_number = $file_number and ai.school_id = $this->school_id and ai.item_status = 0 and assessment_type = 2 group by ai.teacher_id order by sum_type DESC LIMIT 10";
        $main['count_top10_2'] = $this->db->query($count_querty)->result_array();

        //三项标准图表统计
        //专业
        $count_querty = "SELECT AVG(item_number)  as avg_number FROM(
 SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 0 group by teacher_id ) AS TEMP";
        $avg_number = $this->db->query($count_querty)->row_array();
        $avg_number = round($avg_number['avg_number'], 2);
        $main['count_chart0_avg_number'] = $avg_number;
        $count_querty = "SELECT sum(IF(item_number >= $avg_number,1,0)) as type_count,sum(IF(item_number < $avg_number,1,0)) as type_count1 FROM (
SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 0 group by teacher_id ) AS TEMP";
        $main['count_chart0'] = $this->db->query($count_querty)->row_array();
        //素养
        $count_querty = "SELECT AVG(item_number)  as avg_number FROM(
 SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 1 group by teacher_id ) AS TEMP";
        $avg_number = $this->db->query($count_querty)->row_array();
        $avg_number = round($avg_number['avg_number'], 2);
        $main['count_chart1_avg_number'] = $avg_number;
        $count_querty = "SELECT sum(IF(item_number >= $avg_number,1,0)) as type_count,sum(IF(item_number < $avg_number,1,0)) as type_count1 FROM (
SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 1 group by teacher_id ) AS TEMP";
        $main['count_chart1'] = $this->db->query($count_querty)->row_array();
        //学术
        $count_querty = "SELECT AVG(item_number)  as avg_number FROM(
 SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 2 group by teacher_id ) AS TEMP";
        $avg_number = $this->db->query($count_querty)->row_array();
        $avg_number = round($avg_number['avg_number'], 2);
        $main['count_chart2_avg_number'] = $avg_number;
        $count_querty = "SELECT sum(IF(item_number >= $avg_number,1,0)) as type_count,sum(IF(item_number < $avg_number,1,0)) as type_count1 FROM (
SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 2 group by teacher_id ) AS TEMP";
        $main['count_chart2'] = $this->db->query($count_querty)->row_array();

        $this->load->view('home',$main);
    }

    private function _home_teacher($file_number)
    {
        $count_querty = "SELECT AVG(item_number)  as avg_number FROM(
 SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 0 group by teacher_id ) AS TEMP";
        $avg_number = $this->db->query($count_querty)->row_array();
        $avg_number = round($avg_number['avg_number'], 2);
        $main['count_chart0_avg_number'] = $avg_number;
        $count_querty = "SELECT AVG(item_number)  as avg_number FROM(
 SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 1 group by teacher_id ) AS TEMP";
        $avg_number = $this->db->query($count_querty)->row_array();
        $avg_number = round($avg_number['avg_number'], 2);
        $main['count_chart1_avg_number'] = $avg_number;
        $count_querty = "SELECT AVG(item_number)  as avg_number FROM(
 SELECT SUM(item_number) AS item_number FROM kkd_assessment_item where file_number = $file_number and school_id = $this->school_id and item_status = 0 and assessment_type = 2 group by teacher_id ) AS TEMP";
        $avg_number = $this->db->query($count_querty)->row_array();
        $avg_number = round($avg_number['avg_number'], 2);
        $main['count_chart2_avg_number'] = $avg_number;

        $count_querty = "select SUM(item_number) as sum_type,assessment_name,min(assessment_type) as assessment_type from kkd_assessment_item
 where file_number = $file_number and school_id = $this->school_id and item_status = 0 and teacher_id = ".$_SESSION['user_id']." group by assessment_name";
        $main['count_chart_all'] = json_encode($this->db->query($count_querty)->result_array());
        $this->load->view('home_teacher',$main);
    }

    public function login()
    {
        if(REQUEST_METHOD !== REQUEST_POST) $this->ajax_return(400,MESSAGE_ERROR_REQUEST_TYPE);
        $account = $this->input->post('username');
        $password = $this->input->post('password');
        $record = $this->input->post('record');
        $type = $this->input->post('type');

        if (empty($account) || empty($password)) {
            $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
        }
        $password = md5($password . ENCRYPT_KEY);
        switch ($type) {
            case 't':
                //教师
                $this->_teacher_login($account,$password,$record);
                break;
            case 's' :
                //学生
                $this->_student_login($account,$password);
                break;
            default:
                $this->ajax_return(400,MESSAGE_ERROR_PARAMETER);
                break;
        }
    }

    protected function _teacher_login($account,$password,$record)
    {
        $this->load->model('teacher_model');
        $where['teacher_account'] = $account;
        $where['teacher_password'] = $password;
        $data = $this->teacher_model->get_teacher($where, 'teacher_id,teacher_name,teacher_photo,tea.school_id');
        if ($data) {
            $sign = $this->set_kkd_token($data['teacher_id'],$data['school_id'], 't');
            $time = ($record)?(7*86400):0;
            $this->load->helper('cookie');
            set_cookie('token',$sign,$time);

            $this->ajax_return(200,MESSAGE_SUCCESS,$sign);
        }else
            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_PASSWORD);
    }

    protected function _student_login($account,$password)
    {
        $this->load->model('student_model');
        $data = $this->student_model->match_student_info($account, $password);
        if ($data) {
            $sign = $this->set_kkd_token($data['student_id'],0, 's');

            $this->ajax_return(200,MESSAGE_SUCCESS,$sign);
        }else
            $this->ajax_return(400,MESSAGE_ERROR_ACCOUNT_PASSWORD);
    }

    public function logout()
    {
        session_start();
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_photo']);
        unset($_SESSION['user_type']);
        unset($_SESSION['group_model']);
        unset($_SESSION['school_id']);
        session_destroy();
        $this->load->helper('cookie');
        set_cookie('token',0,-1);
        $this->direct('/login.html');
    }

    public function teacher()
    {
        $this->load->model('school_model');
        //读取学校配置文件
        $school_id = $this->school_id;
        $va = $this->school_model->get($school_id);

        $this->load->model('auth_role_model');
        //读取角色列表
        $roles = $this->auth_role_model->get_role_list($role_type = null,$school_id);

        $main['KKD_ROLES'] = json_encode($roles);
        $main['KKD_SCHOOL_CONFIG'] = json_encode($va);
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/icheck/skins/square/blue.css?v=1.0.2\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>
                <script type=\"text/javascript\" src=\"/js/birthday.js\"></script>
                <script type=\"text/javascript\" src=\"/js/icheck/icheck.js?v=1.0.2\"></script>";
        $this->load->view('header',$data);
        $this->load->view('teacher',$main);
        $this->load->view('footer',$data);
    }

    public function profile()
    {
        $data['FOOTER_JAVASCRIPT'] = "<script type=\"text/javascript\" src=\"/js/ajaxfileupload.js\"></script>";
        $this->load->view('header');
        $this->load->view('profile');
        $this->load->view('footer',$data);
    }

    public function assessment()
    {
        $this->load->model('auth_role_model');
        //读取角色列表,过滤普通教师；
        $school_id =$this->school_id;
        $role_type = 2;
        $roles = $this->auth_role_model->get_role_list($role_type,$school_id);
        $main['KKD_ROLES'] = json_encode($roles);
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/icheck/skins/square/blue.css?v=1.0.2\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>
                <script type=\"text/javascript\" src=\"/js/birthday.js\"></script>
                <script type=\"text/javascript\" src=\"/js/icheck/icheck.js?v=1.0.2\"></script>
                <script type=\"text/javascript\" src=\"/js/jquery.spinner/jquery.spinner.js\"></script>";
        $this->load->view('header',$data);
        $this->load->view('assessment',$main);
        $this->load->view('footer',$data);
    }

    public function pend()
    {
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/icheck/skins/square/blue.css?v=1.0.2\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>
                <script type=\"text/javascript\" src=\"/js/icheck/icheck.js?v=1.0.2\"></script>";
        $this->load->view('header',$data);
        $this->load->view('pend');
        $this->load->view('footer',$data);
    }

    public function source()
    {
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>";
        $this->load->view('header',$data);
        $this->load->view('source');
        $this->load->view('footer',$data);
    }

    public function role()
    {
        $this->load->view('header');
        $this->load->view('role');
        $this->load->view('footer');
    }

    //以下公开入口属于角色为普通教师身份的用户
    public function apply()
    {
        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>";
        $this->load->view('header',$data);
        $this->load->view('apply');
        $this->load->view('footer',$data);
    }

    public function item()
    {
        $item_id = $this->input->get('edit');
        $main['ass_item_files']=0;
        $main['item_title']='';
        $main['item_content']='';
        $main['save_method'] = 'post';
        $main['save_path'] ='/assessment/item';
        if($item_id){
            $this->load->model('assessment_item_model');
            $ass_item = $this->assessment_item_model->get_item($item_id,'assessment_type,assessment_set_id,item_title,item_content');
            $ass_item_files = $this->assessment_item_model->get_item_file($item_id);
            if(!$ass_item) $this->direct('/Home/apply');
            $main['ass_item_files'] = json_encode($ass_item_files);
            $main['item_title'] = $ass_item['item_title'];
            $main['item_content'] = $ass_item['item_content'];
            $main['save_method'] = 'put';
            $main['save_path'] ='/assessment/item/'.$item_id;
        }
        else{
            $sid = $this->input->get('sid');
            $asstype = $this->input->get('type');
            if(!isset($sid) || !isset($asstype))  $this->direct('/Home/apply');
            $ass_item['assessment_set_id']=$sid;
            $ass_item['assessment_type']=$asstype;
        }

        //获取当前item所属set类型下的列表
        $this->load->model('assessment_model');
        $where['is_open'] = 1;
        $where['assessment_type'] = $ass_item['assessment_type'];
        $where['kkd_assessment_set.school_id'] = $this->school_id;
        $ass_model = $this->assessment_model->get_name_list($where);
        $ass_array = $this->assessment_model->get_info($ass_item['assessment_set_id'],'have_title,have_content,have_zip,assessment_descript,max_number');

        $main['KKD_ASS_MODEL'] = json_encode($ass_model);
        $main['DEFAULT_ITEM'] = $ass_item['assessment_set_id'];
        $item_type = ['专业标准','素养标准','学术标准'];
        $main['item_type']=$item_type[$where['assessment_type']];
        $main['assessment_descript'] = $ass_array['assessment_descript'];
        $main['max_number'] = $ass_array['max_number'];
        $main['have_title'] = $ass_array['have_title'];
        $main['have_content'] = $ass_array['have_content'];
        $main['have_zip'] = $ass_array['have_zip'];


        $data['HEADER_CSS'] = "<link href=\"//cdn.bootcss.com/bootstrap/3.1.0/css/bootstrap.min.css\" rel=\"stylesheet\">
<link rel=\"stylesheet\" href=\"/js/bootstrap.summernote/dist/summernote.0.8.2.css\">";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"//cdn.bootcss.com/bootstrap/3.1.0/js/bootstrap.min.js\"></script>
<script type=\"text/javascript\" src=\"/js/bootstrap.summernote/dist/summernote.min.0.8.2.js\"></script>
<script type=\"text/javascript\" src=\"/js/bootstrap.summernote/lang/summernote-zh-CN.js\"></script>
<script type=\"text/javascript\" src=\"/js/ajaxfileupload.js\"></script>";
        $this->load->view('header',$data);
        $this->load->view('item',$main);
        $this->load->view('footer',$data);
    }

    public function message()
    {
        $this->load->view('header');
        $this->load->view('message');
        $this->load->view('footer');
    }

    public function rank()
    {
        $this->load->model('school_model');
        $school_id = $this->school_id;
        $va = $this->school_model->get($school_id);
        $main['KKD_SCHOOL_CONFIG'] = json_encode($va);

        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>";
        $this->load->view('header',$data);
        $this->load->view('rank',$main);
        $this->load->view('footer',$data);
    }

    public function rank_info()
    {
        $main['teacher_id'] = $this->uri->segment(3, 0);
        if(!$main['teacher_id']) $main['teacher_id'] = $_SESSION['user_id'];
        $main['teacher_name'] = $this->input->get('user');
        if($main['teacher_id'] == $_SESSION['user_id']) $main['teacher_name'] = '我';

        $data['HEADER_CSS'] = "<link href=\"/js/select/css/cs-select.css\" rel=\"stylesheet\" type=\"text/css\" />
                 <link href=\"/js/select/css/cs-skin-border.css\" rel=\"stylesheet\" type=\"text/css\" />";
        $data['FOOTER_JAVASCRIPT'] = "<script src=\"/js/select/js/classie.js\" type=\"text/javascript\"></script>
                <script src=\"/js/select/js/selectFx.js\" type=\"text/javascript\"></script>";
        $this->load->view('header',$data);
        $this->load->view('rank_info',$main);
        $this->load->view('footer',$data);
    }

    public function save_excel()
    {
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $objPHPExcel = new PHPExcel();
        //设置文档属性
        $objPHPExcel->getProperties()->setCreator("史家教育集团")->setLastModifiedBy("Modified User")->setTitle("教师排行榜")->setSubject("教师排行榜");

        $objPHPExcel->setActiveSheetIndex(0);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $objActSheet->getRowDimension(1)->setRowHeight(30);

        $objActSheet->setTitle('教师排行榜');

        $objActSheet->setCellValue('A1','教师排行榜');
        $objActSheet->mergeCells('A1:G1');
        $objActSheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle('A1:G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objActSheet->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle('A2:G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objStyleA1 = $objActSheet->getStyle('A1');
        $objFontA1 = $objStyleA1->getFont();
        $objFontA1->setName('宋体');
        $objFontA1->setSize(16);
        $objFontA1->setBold(true);

        $objActSheet->setCellValue('A2','排名');
        $objActSheet->setCellValue('B2','教师');
        $objActSheet->setCellValue('C2','学科');
        $objActSheet->setCellValue('D2','年级');
        $objActSheet->setCellValue('E2','专业');
        $objActSheet->setCellValue('F2','素养');
        $objActSheet->setCellValue('G2','学术');

        $this->load->model('school_model');
        $school_datas = $this->school_model->get($this->school_id,'file_number');
        $this->load->model('rank_model');
        $where['file_number'] = $school_datas['file_number'];
        $where['school_id'] = $this->school_id;
        $datas = $this->rank_model->rank_list_all($where);
        $cell_rank = 1;
        foreach($datas as $val){
            $cell_i = $cell_rank + 2;
            $objActSheet
                ->setCellValue('A'.$cell_i, $cell_rank)->setCellValue('B'.$cell_i, $val['teacher_name'])->setCellValue('C'.$cell_i,$val['teacher_subject'])->setCellValue('D'.$cell_i, $val['grade_number'])
                ->setCellValue('E'.$cell_i, $val['sum_type0'])->setCellValue('F'.$cell_i, $val['sum_type1'])->setCellValue('G'.$cell_i, $val['sum_type2']);
            $cell_rank++;
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="教师排行榜.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    
    public function download()
    {
        $this->load->view('download');
    }

}