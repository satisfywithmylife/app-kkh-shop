<?php

apf_require_class('APF_Controller');

class Article_ViewController extends APF_Controller
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

		$aid = isset($params['aid']) && !empty($params['aid']) ? $params['aid'] : '';

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_article_info = new Bll_Article_Info();
		$bll_article_image = new Bll_Article_Image();
		$bll_article_belong = new Bll_Article_Belong();

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
				'msg' => '缺少必要参数,aid',
				'data' => [],
			];
			echo json_encode($data);
			return false;
		}

		$article_info = $bll_article_info->get_article_admin($aid);
		
		$data = [
			'status' => 200,
			'msg' => 'success',
			'data' => $article_info,
		];
		echo json_encode($data);	
		return false;
	}
}
