<?php

apf_require_class('APF_Controller');

class User_RegisterController extends APF_Controller
{
	public function __construct() {
		
	}

    public function handle_request()
    {   
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

		$username = isset($params['username']) && !empty($params['username']) ? $params['username'] : '';
		$password = isset($params['password']) && !empty($params['password']) ? $params['password'] : '';
		
		$salt = mt_rand(1, 10000); 
		
		if (empty(trim($username)) || empty(trim($password))) {
			$data = [
				'status' =>400,
				'msg' => '缺少必要参数 username or password',
				'data' => [],
			];
			echo json_encode($data);
			return false;
		}
		
		if (strlen($username) < 8 || strlen($password) < 8) {
			$data = [
				'status' => 400,
				'data' => array(),
				'msg' => '用户名或者密码长度不能低于8位',
			];
			echo json_encode($data);
			return false;
		}

		$bll_admin_user = new Bll_Admin_Info();
		
		$is_exsite = $bll_admin_user->check_user_exsite($username);		
		if ($is_exsite){
			$data = [
				'status' => 400,
				'msg' => '该用户已存在，请勿重复注册！',
				'data' => array(),
			];
			echo json_encode($data);
			return false;
		}
		
		$password = md5(md5($password).$salt); //双md5+盐 加密，生成access_token
		$user_data = [
			'username' => $username,
			'name' => '', //true name
			'password' => $password,
			'email' => '', 
			'mobile' => '',
			'info' => '',  //user info
			'salt' => $salt,
			'status' => 1, //0-access denied ,1-access allowed
			'groupid' => 1, //1-客服，2-经理，5-super admin
		];
	
		$res = $bll_admin_user->register($user_data);

		if (!$res) {
			$data = [
				'status' => 400,
				'data' => [],
				'msg' => 'register fail, please try again',
			];
		} else {
			$data = [
				'status' => 200,
				'msg' => 'successs',
				'data' => array(),
			];
		}
		
		echo json_encode($data);	
		return false;
	}
}
