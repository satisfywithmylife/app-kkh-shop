<?php

apf_require_class('APF_Controller');

class Apost_AddController extends APF_Controller
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
		$data['act_url'] = isset($params['act_url']) && !empty($params['act_url']) ? $params['act_url'] : '';
		$data['pos'] = isset($params['pos']) && !empty($params['pos']) ? $params['pos'] : 0;
		$data['imgurl'] = isset($params['imgurl']) && !empty($params['imgurl']) ? $params['imgurl'] : '';
		$data['type'] = isset($params['type']) && !empty($params['type']) ? $params['type'] : 1; //0-活动页，1-商品
		$data['active'] = isset($params['active']) && !empty($params['active']) ? $params['active'] : 0; //1上线，0下线，-1遗弃(删除)
		$data['description'] = isset($params['description']) ? $params['description'] : '未填写名称';
        $data['name'] = isset($params['name']) && !empty($params['name']) ? $params['name'] : 'qpg';//banner名称
		$data['share_title'] = isset($params['share_title']) && !empty($params['share_title']) ? $params['share_title'] : '';//banner分享文字
		$data['share_img'] = isset($params['share_img']) && !empty($params['share_img']) ? $params['share_img'] : '';//banner分享图片
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
		if (empty($data['pos']) || empty($data['type'])) {
			$data = [
				'status' =>400,
				'msg' => '缺少必要参数, 排序值 or 类型',
				'data' => [],
			];
			echo json_encode($data);
			return false;
		}
		#判断act_url是否是一个数字，是，则配置的是商品，否则是活动页
		if(is_numeric((int)$data['act_url']) && (int)$data['act_url'] != 0){
			$data['id_product'] = $data['act_url'];
			$data['type'] = 0;
			$data['act_url'] = '';
		} else{
			$data['type'] = 1;
		}


		$res = $banner_info = $bll_apost_info->add($data);
		
		if($res){
			$data_s = [
				'status' => 200,
				'msg' => 'success',
				'data' => [],
			];
		} else {
			$data_s = [
				'status' => 400,
				'msg' => 'fail ',
				'data' => [], 
			];
		}
		echo json_encode($data_s, JSON_NUMERIC_CHECK);	
		return false;
	}
}
