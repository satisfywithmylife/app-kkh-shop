<?php

apf_require_class('APF_Controller');

class Article_SearchController extends APF_Controller
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

		$kwd = isset($params['keyword']) && !empty($params['keyword']) ? $params['keyword'] : '';
		$page = isset($params['page']) && !empty($params['page']) ? $params['page'] : 1;

		if($page <= 0) {
			$page = 1;
		}
		$data['page_size'] = 20;
		$data['offset'] = ($page - 1)*$data['page_size'];
		
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_article_info = new Bll_Article_Info();
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
		
		$article_list = $bll_article_info->get_article_by_keyword_admin($kwd, $data);
		$total = $bll_article_info->get_article_by_keyword_admin_count($kwd, array());
		
		$data = [
			'status' => 200,
			'msg' => 'success',
			'data' => [
				'article_list' => $article_list,
				'keyword' => $kwd,
				'current_page' => $page,
				'page_size' => $data['page_size'],
				'total' => $total,
			],
		];
		echo json_encode($data);	
		return false;
	}
}
