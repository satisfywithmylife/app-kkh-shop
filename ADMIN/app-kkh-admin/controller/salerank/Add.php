<?php

apf_require_class('APF_Controller');

class Salerank_AddController extends APF_Controller
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

		$data['id_product'] = isset($params['id_product']) && !empty($params['id_product']) ? $params['id_product'] : 0;
		$data['pos'] = isset($params['pos']) && !empty($params['pos']) ? $params['pos'] : 0;
		//$data['link'] = isset($params['link']) && !empty($params['link']) ? $params['link'] : '';
		$data['active'] = 1;  //0-offline,1-online;

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_salerank_info = new Bll_Salerank_Info();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			echo $this->json_str(400, 'access denied', array());
			return false;
		}
		$data['created_by'] = $check['uid'];
		$data['updated_by'] = $check['uid']; 
	
		#nesseary data
		if (empty(trim($data['pos'])) ||empty($data['id_product'])) {
			echo $this->json_str(400, '缺少必要参数，id_product or pos', array());
			return false;
		}
		
		#防止多人同时添加造成重复的问题
		$r_data['id_product'] = $data['id_product']; 
		$repeat = $bll_salerank_info->check_repeat($r_data);
		if($repeat){
			echo json_str(400, '该商品已存在排行榜中', array());
			return false;
		}

		$id = $bll_salerank_info->add($data);
		if ($id) {
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
