<?php

apf_require_class('APF_Controller');

class Cornertags_EditController extends APF_Controller
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

		$data['title'] = isset($params['title']) && !empty($params['title']) ? $params['title'] : '';
		$data['id'] = isset($params['id']) && !empty($params['id']) ? $params['id'] : 0;
		$data['img_url'] = isset($params['img_url']) && !empty($params['img_url']) ? $params['img_url'] : '';

		#check admin role
		$bll_admin_user = new Bll_Admin_Info();
		$bll_cornertags_info = new Bll_Cornertags_Info();

		$check = $bll_admin_user->check_user_role($username ,$access_token);		
		if(!$check) {
			echo $this->json_str(400, 'access denied', array());
			return false;
		}
		//$data['created_by'] = $check['uid'];
		$data['updated_by'] = $check['uid']; 
	
		#nesseary data
		if($data['id'] == 0 || empty($data['title']) || empty($data['img_url'])) {
            echo $this->json_str(400, '缺少必要参数，id or title or img_url', array());
			return false;			
		}		

		$res = $bll_cornertags_info->edit($data);
		if ($res) {
			echo $this->json_str(200, 'success', array());
		} else {
			echo $this->json_str(200, 'edit fail', array());
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
