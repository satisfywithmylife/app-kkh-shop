<?php

apf_require_class('APF_Controller');

class Search_AddController extends APF_Controller
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

		$data['keyword'] = isset($params['keyword']) && !empty($params['keyword']) ? $params['keyword'] : '';
		$data['pos'] = isset($params['pos']) && !empty($params['pos']) ? $params['pos'] : 0;
		$id = isset($params['id']) && !empty($params['id']) ? $params['id'] : 0;
		$data['active'] = 1;  //0-offline,1-online;
		$data['type'] = isset($params['type']) && !empty($params['type']) ? $params['type'] : 2; //1-product, 2-category

		if($data['type'] == 1){
			$data['id_product'] = $id;
			$data['id_category'] = 0;
		} else {
			$data['id_product'] = 0;
			$data['id_category'] = $id;
		}
		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_search_info = new Bll_Search_Info();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			echo $this->json_str(400, 'access denied', array());
			return false;
		}
		$data['created_by'] = $check['uid'];
		$data['updated_by'] = $check['uid']; 
		#nesseary data
		if (mb_strlen($data['keyword'], 'UTF-8')>30 || empty($data['keyword']) || empty(trim($data['pos'])) || $id == 0) {
			echo $this->json_str(400, '缺少必要参数，keyword or pos or id = 0, or keyword length more than 10', array());
			return false;
		}
		
		$sid = $bll_search_info->add($data);
		if ($sid) {
			echo $this->json_str(200, 'success', array());	
		} else {
			echo $this->json_str(400, 'add fail', array());
		}
		return false;
	}

	#json
	public function json_str($code, $msg, $data){
		$j_data = [
			'status' => $code,
			'msg' => $msg,
			'data' => $data,
		];
		$res = json_encode($j_data, JSON_NUMERIC_CHECK);
		return $res;
	}
	
}
