<?php

apf_require_class('APF_Controller');

class Apost_DelController extends APF_Controller
{

    public function handle_request()
    {   
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();
        $s_re = Util_AdminSecurity::Security($params);
        if(!$s_re){
            $data = [
                'status' => 400,
                'msg' => 'access denied',
                'data' => array(),
            ];
            echo json_encode($data);
            return false;
        }

        $bll_apost_info = new Bll_Apost_Info();
        $aid = isset($params['id']) && !empty($params['id']) ? $params['id'] : '';
	
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
        $data['id'] = $aid;
		$banner_info = $bll_apost_info->del($data);
		if($banner_info){
		  $data = ['status'=>200,'msg'=>'success','data'=>[]];
		}else{
			$data = [
				'status' => 400,
				'msg' => 'fail',
				'data' => [],
			];
		}
		echo json_encode($data, JSON_NUMERIC_CHECK);	
		return false;
	}
}
