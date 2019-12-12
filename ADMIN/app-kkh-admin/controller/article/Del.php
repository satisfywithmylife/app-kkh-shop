<?php

apf_require_class('APF_Controller');

class Article_DelController extends APF_Controller
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

		$data['aid'] = isset($params['aid']) && !empty($params['aid']) ? $params['aid'] : 0;
		$data['active'] = isset($params['active']) && !empty($params['active']) ? $params['active'] : 0;
		if($data['aid'] == 0) {
            $data = [ 
                'status' => 400,
                'msg' => 'need aid，aid can not be empty',
                'data' => array(), 
            ];  
            echo json_encode($data);
            return false;			
		}		

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_article_info = new Bll_Article_Info();

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
		$data['updated_by'] = $check['uid']; 
	
		$res = $bll_article_info->del_article($data);
		
		if (!$res) {
            $data = [ 
                'status' => 400,
                'msg' => '更新状态失败',
                'data' => array(), 
            ];  
		}else {
		    //删除缓存
		    Util_Redis::del_cache("product:headline_article_list");
			$data = [
				'status' => 200,
				'msg' => 'success',
				'data' => array(
					'aid' => $data['aid'],
				),
			];
		}
		echo json_encode($data);	
		return false;
	}
}
