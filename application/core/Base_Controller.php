<?php
/**
 * 自定义的基类
 * 主要包括：
 *   公共参数配置
 *   用户验证，token生成，ajax返回值
 */

//闲置参数，暂时无用
define('KKD_HOST', '');

define('KKD_UPLOAD_FILE','gif|jpg|png|doc|docx|ppt|pptx|xls|xlsx|mp3|mp4|rar|zip');

define('DEFAULT_AJAX_RETURN', 'JSON');
define('DEFAULT_JSONP_HANDLER', 'jsonpReturn');
define('VAR_JSONP_HANDLER', 'callback');
define('ENCRYPT_KEY', 'KKD_SYSTEM');
define('DEFAULT_PASSWORD', '000000');

define('REQUEST_METHOD', strtoupper($_SERVER['REQUEST_METHOD']));
define('REQUEST_GET', 'GET');
define('REQUEST_POST', 'POST');
define('REQUEST_PUT', 'PUT');
define('REQUEST_DELETE', 'DELETE');

/*
 * ---------------------
 * 请求响应info配置
*/

define('MESSAGE_SUCCESS', 'success');

//身份信息
define('MESSAGE_ERROR_TOKEN_OVERDUE', '你的TOKEN已过期');
define('MESSAGE_ERROR_WARNING_TOKEN', '非法警告：TOKEN不正确');
define('MESSAGE_ERROR_WARNING_AUTH', '非法警告：用户身份错误');
define('MESSAGE_ERROR_USER_ROLE', '该功能没有权限');

//参数错误
define('MESSAGE_ERROR_PARAMETER', '请求参数不正确');
define('MESSAGE_ERROR_ACCOUNT_UNIQUE', '该账号已存在');
define('MESSAGE_ERROR_ACCOUNT_PASSWORD', '账号或密码不正确');
define('MESSAGE_ERROR_NON_DATA', '数据不存在');
define('MESSAGE_ERROR_REQUEST_TYPE', '请求方式不正确');
define('MESSAGE_ERROR_CHANGE_PASSWORD', '您的初始密码不正确');
define('MESSAGE_ERROR_DATA_WRITE', '数据更新错误');
define('MESSAGE_ERROR_HAVE_DONE_PASS','本条已审核通过');
define('MESSAGE_ERROR_HAVE_DONE_REBUT','本条已被驳回');
define('MESSAGE_ERROR_ENOUGH','本项提交量已达标');
define('MESSAGE_ERROR_SYSTEM_ROLE_D','内置角色不能被删除');
define('MESSAGE_ERROR_SYSTEM_ROLE_P','内置角色不能被修改');
define('MESSAGE_ERROR_HAVE_DATE','该角色下存在用户，不能被删除');
define('MESSAGE_ERROR_SELF_INFO','这个账户是您本人，不能删除');
define('MESSAGE_ERROR_NAME_UNIQUE','角色名已存在');

class Base_Controller extends CI_Controller
{
    protected $HTTP_TOKEN = '';
    protected $HTTP_TOKEN_SIGN = array();
    protected $school_id = 0;

    private $user_auth_group = '';

    protected function valid_kkd_token($token)
    {
        if(!isset($token)) return false;
        if(!$token) return false;
        $sign = @unserialize(base64_decode($token));
        if($sign === false) return false;
        if($sign['uid'] && $sign['sid'] && $sign['type'] && $sign['create_time'] && $sign['expiry_time']){
            if(time() > $sign['expiry_time']) {
                return false;
            }
            $this->school_id = $sign['sid'];
            $this->HTTP_TOKEN_SIGN = $sign;
            return true;
        }
        return false;
    }

    protected function check_user()
    {
        $sign = $this->HTTP_TOKEN_SIGN;
        switch ($sign['type']) {
            case 't':
                //教师
                $this->load->model('teacher_model');
                $where['teacher_id']=$sign['uid'];
                $where['tea.school_id']=$sign['sid'];
                $data = $this->teacher_model->get_teacher($where,'teacher_id,teacher_photo,teacher_name,tea.school_id,teacher_role');
                if ($data) {
                    $u_auth = $this->set_user_auth($sign['uid']);
                    $this->user_auth_group = $u_auth;
                    return $data;
                }
                else return false;
                break;
            case 's' :
                //学生

                break;
            default:
                return false;
                break;
        }
    }

    protected  function valid_user_auth($controller,$method,$action)
    {
        foreach($this->user_auth_group as $row)
        {
            if(strcasecmp($row['auth_controller'],$controller) === 0 && $row['auth_method'] === 'all' && $row['auth_action'] === 'all')
            {
                return true;
            }
            if(strcasecmp($row['auth_controller'],$controller) === 0 && $row['auth_method'] === $method && $row['auth_action'] === 'all')
            {
                return true;
            }
            if(strcasecmp($row['auth_controller'],$controller) === 0 &&
                strcasecmp($row['auth_method'],$method) === 0 &&
                strcasecmp($row['auth_action'],$action) === 0)
            {
                return true;
            }
        }
        return false;
    }

    protected function direct($url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function ajax_return($code='',$info='',$data='',$type=''){
        $result = array(
            'code' => $code,
            'info' => $info,
            'data' => $data
        );
        $this->_ajax_return($result,$type);
    }

    private function _ajax_return($data,$type='') {
        if(empty($type)) $type  =   DEFAULT_AJAX_RETURN;
        switch (strtoupper($type)){
            case 'JSON' :
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                header('Content-Type:application/json; charset=utf-8');
                $handler = isset($_GET[VAR_JSONP_HANDLER]) ? $_GET[VAR_JSONP_HANDLER] :DEFAULT_JSONP_HANDLER;
                exit($handler.'('.json_encode($data).');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
        }
    }

    protected function set_user_session($u_id,$u_name,$u_photo,$u_type,$school_id)
    {
        $_SESSION['user_id'] = $u_id;
        $_SESSION['user_name'] = $u_name;
        $_SESSION['user_photo'] = $u_photo;
        $_SESSION['user_type'] = $u_type;
        $_SESSION['group_model'] = $this->user_auth_group;
        $this->get_normal_user($u_id);
        $_SESSION['school_id']=$school_id;
    }

    protected function get_normal_user($u_id)
    {
        $this->db->select( 'ar.role_name,ar.school_id,ar.role_type' );
        $this->db->from('kkd_teacher_role as tr');
        $this->db->join( 'kkd_role as ar','tr.role_id = ar.role_id');
        $this->db->where('teacher_id',$u_id);
        $this->db->order_by('role_type desc');
        $res = $this->db->get()->result_array();
        $res = array_unique(array_column($res,'role_type'));
        $res_i = '';
        //降序排列，保证循环第一个是教师，最后是1（管理员）,0（审核者）
        foreach($res as $va){
            switch ($va){
                case 2:
                    //普通教师用户
                    $_SESSION['assessment_menu'] = $this->assessment_item_menu();
                    $this->load->model('message_model');
                    $_SESSION['read_count'] = $this->message_model->read_count($u_id);
                    $res_i = 'normal';
                    break;
                case 1:
                    if($res_i == '') $res_i = 'check';
                    break;
                case 0:
                    if($res_i == 'normal') $res_i = 'hybrid';
                    else $res_i = 'check';
                    break;
            }
        }
        $_SESSION['user_role_type']=$res_i;
    }

    protected function check_user_session()
    {
        $re_va = false;
        if(isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && isset($_SESSION['user_photo']) &&
            isset($_SESSION['user_type']) && isset($_SESSION['group_model']) && isset( $_SESSION['assessment_menu']) && isset($_SESSION['school_id']) )
        {
            $re_va = true;
        }

        return $re_va;
    }

    protected function assessment_item_menu()
    {
        $this->load->model('assessment_model');
        $where['is_open'] = 1;
        $where['kkd_assessment_set.school_id'] = $this->school_id;
        return $this->assessment_model->get_name_list($where,'assessment_set_id,assessment_name,assessment_type');
    }

    protected function set_kkd_token($uid,$sid,$type)
    {
        $sign = array(
            'uid' => $uid,
            'sid' => $sid,
            'type' => $type,
            'create_time' => time(),
            'expiry_time' => strtotime('+7 days')
        );

        return base64_encode(serialize($sign));
    }

    protected function set_user_auth($u_id)
    {
        $this->load->model('auth_role_model');
        $u_auth = $this->auth_role_model->get_auth_group($u_id);
        return $u_auth;
    }
}

/*
 * 这个类是提供web服务控制器的基类
 *
 * 主要用于请求header里的token验证
 *
 * */
class API_Conotroller extends Base_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->init();
    }

    private function init()
    {
        if(isset($_SERVER['HTTP_TOKEN'])){
            $this->HTTP_TOKEN = $_SERVER['HTTP_TOKEN'];
        }
        else
        {
            $this->load->helper('cookie');
            $this->HTTP_TOKEN = get_cookie('token');

        }
        if(!isset($this->HTTP_TOKEN) &&empty($this->HTTP_TOKEN)) $this->ajax_return(300,MESSAGE_ERROR_WARNING_TOKEN);

        $router = & load_class('Router', 'core');
        $controller = strtolower($router->fetch_class());
        $method = strtolower($router->fetch_method());

        if($method == 'login' || $method == 'logout') return true;

        $sign = $this->valid_kkd_token($this->HTTP_TOKEN);
        if($sign === false) $this->ajax_return(300,MESSAGE_ERROR_WARNING_TOKEN);
        $user = $this->check_user();
        if($user === false) $this->ajax_return(300,MESSAGE_ERROR_WARNING_AUTH);

        if($controller == 'user')
        {
            //个人中心 user 控制器
            //不需要权限验证，因此加入例外，如果需要在配置可以在这里写过程
            return true;
        }
        //验证当前权限
        $action = strtolower(REQUEST_METHOD);
        $is_valid = $this->valid_user_auth($controller,$method,$action);
        if(!$is_valid)
        {
            $this->ajax_return(300,MESSAGE_ERROR_USER_ROLE);
        }
    }
}
/*
 * 这个类是提供对页面访问（home控制器）的基类
 *
 * 主要用于请求cookie和session分配与验证
 *
 * */
class WEB_Conotroller extends Base_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->init();
    }

    private function init()
    {
        $router = & load_class('Router', 'core');
        $controller = strtolower($router->fetch_class());
        $method = strtolower($router->fetch_method());

        if($method == 'login' || $method == 'logout') return true;

        $this->load->helper('cookie');
        $this->HTTP_TOKEN = get_cookie('token');

        $sign = $this->valid_kkd_token($this->HTTP_TOKEN);
        if($sign === false) $this->direct('/login.html');
        $user = $this->check_user();
        if($user === false) $this->direct('/login.html');
//        if($this->check_user_session()){
//            $this->get_normal_user($user['teacher_id']);
//            return true;
//        }
//        else $this->set_user_session($user['teacher_id'],$user['teacher_name'],$user['teacher_photo'],$this->HTTP_TOKEN_SIGN['type'],$user['school_id']);
        $this->set_user_session($user['teacher_id'],$user['teacher_name'],$user['teacher_photo'],$this->HTTP_TOKEN_SIGN['type'],$user['school_id']);

        if($controller == 'home' || $controller == 'user')
        {
            //home控制器 与 个人中心
            //不需要权限验证，因此加入例外，如果需要在配置可以在这里写过程
            return true;
        }
        //验证当前权限
        $action = strtolower(REQUEST_METHOD);
        $is_valid = $this->valid_user_auth($controller,$method,$action);
        if(!$is_valid)
        {
            $this->direct('/login.html');
        }
    }
}