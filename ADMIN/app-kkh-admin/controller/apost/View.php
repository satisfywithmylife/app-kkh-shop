<?php

apf_require_class('APF_Controller');

class Apost_ViewController extends APF_Controller
{

    public function handle_request()
    {   
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

		$username = isset($params['username']) && !empty($params['username']) ? $params['username'] : '';
		$access_token = isset($params['access_token']) && !empty($params['access_token']) ? $params['access_token'] : '';

		$aid = isset($params['id']) && !empty($params['id']) ? $params['id'] : '';

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_apost_info = new Bll_Apost_Info();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			$data = [
				'status' => 400,
				'msg' => 'access denied',
				'data' => array(), 
			];
			echo json_encode($data);
			return false;
		}
	
		#nesseary data
		if (empty(trim($aid))) {
			$data = [
				'status' =>400,
				'msg' => '缺少必要参数,id',
				'data' => [],
			];
			echo json_encode($data);
			return false;
		}

		$banner_info = $bll_apost_info->get_banner_admin($aid);
		
		$data = [
			'status' => 200,
			'msg' => 'success',
			'data' => $banner_info,
		];
		echo json_encode($data, JSON_NUMERIC_CHECK);	
		return false;
	}
}
