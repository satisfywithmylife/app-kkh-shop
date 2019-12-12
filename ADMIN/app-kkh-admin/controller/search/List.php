<?php

apf_require_class('APF_Controller');

class Search_ListController extends APF_Controller
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

		//$data['id'] = isset($params['id']) ? $params['id'] : 0;

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_search_info = new Bll_Search_Info();
		$bll_admin_info = new Bll_Admin_Info();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			echo $this->json_str(400, 'access denied', array());
			return false;
		}
	
		#nesseary data
		/*if (empty($data['id'])) {
			echo $this->json_str(400, '缺少必要参数，id', array());
			return false;
		}*/
		
		$keyword_list = [];
		$keyword_list = $bll_search_info->get_list_admin();
		$keyword_list = $bll_admin_info->change_time_formate_list($keyword_list);

		$data = [
			'keyword_list' => $keyword_list,
		];
		
		echo $this->json_str(200, 'success', $data);	
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
