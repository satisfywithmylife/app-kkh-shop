<?php
//todo  登陆验证和token验证
apf_require_class("APF_Controller");

class Comment_ExternalInfoController extends APF_Controller {
    private static $error_ret = array(
        "status" => 400,
        "data" => array(),
        "msg" => ""
    );  

    private static $success_ret = array(
        "status" => 200,
        "data" => array(),
        "msg" => ""
    );  

    public function handle_request() {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");
		
		$req = APF::get_instance()->get_request();
		$param = $req->get_parameters();
		if(empty($param) || !isset($param['keyword']) || empty($param['page_num']) || empty($param['page_size'])) {
			self::$error_ret['msg'] = 'invalid param';
			echo json_encode(self::$error_ret);
			return false;
		}
		
		$keyword = $param['keyword'];
		if(empty($keyword)) {
			$keyword = '';
		}
		$page_num = intval($param['page_num']);
		$page_size = intval($param['page_size']);

        $bll_comment = new Bll_Comment_Info();
        self::$success_ret['data'] = $bll_comment->externalInfo($keyword, $page_num, $page_size);

        echo json_encode(self::$success_ret);
        return false;
    }
}

