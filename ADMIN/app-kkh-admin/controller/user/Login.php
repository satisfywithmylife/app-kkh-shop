<?php

apf_require_class('APF_Controller');

class User_LoginController extends APF_Controller
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
		
		if (empty($username) || empty($password)) {
			$data = [
				'status' =>400,
				'msg' => '缺少必要参数',
				'data' => [],
			];
			echo json_encode($data);
			return false;
		}
		$bll_admin_user = new Bll_Admin_Info();
		$user_data = [
			'username' => trim($username),
			'password' => trim($password),
		];
	
		$res = $bll_admin_user->login($user_data);
		
		if (!$res) {
			$data = [
				'status' => 400,
				'data' => [],
				'msg' => '用户名或密码不正确',
			];
		} else {
			$data = [
				'status' => 200,
				'msg' => 'successs',
				'data' => array(
					'username' =>$res['username'],
					'access_token' => $res['password'],
					'status' => $res['status'],
					'groupid' => $res['groupid'],
					'user_info' =>[
						'name' => $res['name'],
						'email' => $res['email'],
						'mobile' => $res['mobile'],
						'info' => $res['info'],
					],
				),
			];
		}
		
		echo json_encode($data);	
		return false;
	}
}
