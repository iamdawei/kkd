<?php
/**
 * 自定义的基类
 * 主要包括：
 *   公共参数配置
 *   用户验证，token生成，ajax返回值
 */
define('KKD_HOST', 'http://kkd.localhost:8081');
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

//请求响应code与info配置
//code：成功 200，重定向 300，错误 400

//200
define('MESSAGE_SUCCESS', 'success');

//300，跳转到登录页
define('MESSAGE_ERROR_TOKEN_OVERDUE', '你的TOKEN已过期');
define('MESSAGE_ERROR_WARNING_TOKEN', '非法警告：TOKEN不正确');
define('MESSAGE_ERROR_WARNING_SESSION', '非法警告：用户身份错误');
define('MESSAGE_ERROR_USER_ROLE', '该功能没有权限');

//400
define('MESSAGE_ERROR_PARAMETER', '请求参数不正确');
define('MESSAGE_ERROR_ACCOUNT_UNIQUE', '该账号已存在');
define('MESSAGE_ERROR_ACCOUNT_PASSWORD', '账号或密码不正确');
define('MESSAGE_ERROR_NON_DATA', '数据不存在');
define('MESSAGE_ERROR_REQUEST_TYPE', '请求方式不正确');
define('MESSAGE_ERROR_CHANGE_PASSWORD', '您的初始密码不正确');

define('MESSAGE_ERROR_DATA_WRITE', '数据更新错误');

class Base_Controller extends CI_Controller
{
    protected $HTTP_TOKEN = '';
    protected $HTTP_TOKEN_SIGN = array();
    protected $user_auth_group = '';
    //参数用于决定是否重新负责session，ajax情况下不需要进行sessiong设置，直接进入验权
    //如果接口分离后，请注意本参数关联的函数
    public static $is_ajax = 1;
    protected $school_id = 0;

    function __construct()
    {
        parent::__construct();
        $router = & load_class('Router', 'core');

        $controller = strtolower($router->fetch_class());
        $method = strtolower($router->fetch_method());

        if($method == 'login' || $method == 'logout') return true;

        //如果设置HTTP_TOKEN，表示带TOKEN的AJAX请求
        //非AJAX请求时，通过cookie进行身份验证
        if(isset($_SERVER['HTTP_TOKEN']))
            $this->HTTP_TOKEN = $_SERVER['HTTP_TOKEN'];
        else{
            $this->load->helper('cookie');
            $this->HTTP_TOKEN = get_cookie('token');
        }
        if(empty($this->HTTP_TOKEN)){$this->direct('/login.html');}
        $sign = $this->valid_kkd_token($this->HTTP_TOKEN);
        $this->check_user($sign);

        if($controller == 'home' || $controller == 'user')
        {
            //home控制器 与 个人中心
            //不需要权限验证，因此加入例外，如果需要在配置可以在这里写过程
            return true;
        }

        //获取角色权限

        //验证当前权限
        $action = strtolower($_SERVER['REQUEST_METHOD']);
        $is_valid = $this->valid_user_auth($controller,$method,$action);
        if(!$is_valid)
        {
            $this->ajax_return(300,MESSAGE_ERROR_USER_ROLE);
        }
    }

    protected function ajax_return($code='',$info='',$data=''){
        $result = array(
            'code' => $code,
            'info' => $info,
            'data' => $data
        );
        $this->_ajax_return($result);
    }

    protected function _ajax_return($data,$type='') {
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
                $handler  =   isset($_GET[VAR_JSONP_HANDLER]) ? $_GET[VAR_JSONP_HANDLER] :DEFAULT_JSONP_HANDLER;
                exit($handler.'('.json_encode($data).');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
        }
    }

    protected function direct($url)
    {
        header('Location: ' . $url);
		exit;
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

    protected function valid_kkd_token($token)
    {
        //验证token是否正确有效
		if(!$token) $this->ajax_return(300,MESSAGE_ERROR_WARNING_SESSION);
        $sign = unserialize(base64_decode($token));
        if(!$sign['sid']) $this->ajax_return(300,MESSAGE_ERROR_WARNING_TOKEN);
        $this->school_id = $sign['sid'];
        $this->HTTP_TOKEN_SIGN = $sign;
        if(time() > $sign['expiry_time']) {
            $this->ajax_return(300,MESSAGE_ERROR_TOKEN_OVERDUE);
        }
        return $sign;
    }

    protected function check_user($sign)
    {
        if(!isset($_SESSION['user_id']))
        {
            switch ($sign['type']) {
                case 't':
                    //教师
                    $this->load->model('teacher_model');
                    $where['teacher_id']=$sign['uid'];
                    $data = $this->teacher_model->get_teacher($where,'teacher_id,teacher_photo,teacher_name,school_id,teacher_role');
                    if ($data) {
                        $u_auth = $this->set_user_auth($sign['uid']);
                        $this->user_auth_group = $u_auth;
                        if((self::$is_ajax) === 0) $this->set_user_session($data['teacher_id'],$data['teacher_name'],$data['teacher_photo'],$sign['type'],$data['school_id'],$data['teacher_role']);
                    }
                    break;
                case 's' :
                    //学生

                    break;
                default:
                    $this->ajax_return(300,MESSAGE_ERROR_WARNING_TOKEN);
                    break;
            }
            //执行set_user_session($u_id,$u_name,$u_photo,$u_type,$school_id);
            return true;
        }
    }

    protected function set_user_session($u_id,$u_name,$u_photo,$u_type,$school_id,$u_role)
    {
        session_start();
        $_SESSION['user_id'] = $u_id;
        $_SESSION['user_name'] = $u_name;
        $_SESSION['user_photo'] = $u_photo;
        $_SESSION['user_type'] = $u_type;
        $_SESSION['group_model'] = $this->user_auth_group;
        if(strpos($u_role,'100000') !== false){
            $_SESSION['assessment_menu'] = $this->assessment_item_menu();
            $this->load->model('message_model');
            $datas['read_count'] = $this->message_model->read_count($u_id);
            $this->load->vars($datas);
        }
        $_SESSION['school_id']=$school_id;

        session_write_close();
    }

    protected function set_user_auth($u_id)
    {
        $this->load->model('auth_role_model');
        $u_auth = $this->auth_role_model->get_auth_group($u_id);
        return $u_auth;
    }

    protected  function valid_user_auth($controller,$method,$action)
    {
        foreach($this->user_auth_group as $row)
        {
            if(strcasecmp($row->auth_controller,$controller) === 0 && $row->auth_method === 'all' && $row->auth_action === 'all')
            {
                return true;
            }
            if(strcasecmp($row->auth_controller,$controller) === 0 && $row->auth_method === $method && $row->auth_action === 'all')
            {
                return true;
            }
            if(strcasecmp($row->auth_controller,$controller) === 0 &&
                strcasecmp($row->auth_method,$method) === 0 &&
                strcasecmp($row->auth_action,$action) === 0)
            {
                return true;
            }
        }
        return false;
    }

    protected function assessment_item_menu()
    {
        $this->load->model('assessment_model');
        $where['is_open'] = 1;
        $where['kkd_assessment_set.school_id'] = $this->school_id;
        return $this->assessment_model->get_name_list($where,'assessment_set_id,assessment_name,assessment_type');
    }
}