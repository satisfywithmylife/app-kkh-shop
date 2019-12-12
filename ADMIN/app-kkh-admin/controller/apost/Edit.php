<?php

apf_require_class('APF_Controller');

class Apost_EditController extends APF_Controller
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
        $data['act_url'] = isset($params['act_url']) && !empty($params['act_url']) ? $params['act_url'] : '';
        $data['pos'] = isset($params['pos']) && !empty($params['pos']) ? $params['pos'] : 0;
        $data['imgurl'] = isset($params['imgurl']) && !empty($params['imgurl']) ? $params['imgurl'] : '';
        $data['active'] = isset($params['active']) && !empty($params['active']) ? $params['active'] : 0; //1上线，0下线，-1遗弃(删除)
        $data['description'] = isset($params['description']) ? $params['description'] : '未填写名称';
		$data['id']=$aid;
		$data['pos'] = isset($params['pos']) && !empty($params['pos']) ? $params['pos'] : 0;
		$data['id_product'] = isset($params['id_product']) && !empty($params['id_product']) ? $params['id_product'] : 0;
		$data['type'] = isset($params['type']) && !empty($params['type']) ? $params['type'] : 1; //0-活动页，1-商品
		$data['share_title'] = isset($params['share_title']) && !empty($params['share_title']) ? $params['share_title'] : '';//banner分享文字
		$data['share_img'] = isset($params['share_img']) && !empty($params['share_img']) ? $params['share_img'] : '';//banner分享图片
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
        //首先查看是否存在记录
		$banner_info = $bll_apost_info->get_banner_admin($aid);
		
        if($banner_info){
			if(is_numeric((int)$data['act_url']) && (int)$data['act_url'] != 0){
				$data['id_product'] = $data['act_url'];
				$data['type'] = 1;
				$data['act_url'] = '';
			}else{
				$data['type'] = 0;
			}
            $re = $bll_apost_info->edit($data);
			 
            if($re){
                $data = [
                    'status' =>200,
                    'msg' => 'success',
                    'data' => [],
                ];
            }else{
                $data = [
                    'status' =>400,
                    'msg' => 'edit fail',
                    'data' => [],
                ];
            }
        }else{
            $data = [
                'status' => 400,
                'msg' => 'data nonentity',
                'data' => [],
            ];
        }
		

		echo json_encode($data, JSON_NUMERIC_CHECK);	
		return false;
	}
}
