<?php

apf_require_class('APF_Controller');

class Apost_ListController extends APF_Controller
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

		$res = $banner_info = $bll_apost_info->banner_list();
		
		if($res){
			$data_s = [
				'status' => 200,
				'msg' => 'success',
				'data' => $res,
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
