<?php
apf_require_class("APF_Controller");

class Comment_ProductListController extends APF_Controller {
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
		
		$bll_comment = new Bll_Comment_Info();
		self::$success_ret['data'] = $bll_comment->productList();
		
		echo json_encode(self::$success_ret);
		return false;
	}
	
	/**
     * 参数检查 - 是否存在
     * @param array $param
     * @param array $need
     * @return bool - true: 检测通过    false: 检测不通过
     */
    public static function check_param_set(array $param, array $need) {
        foreach($need as $v) {
            if(!isset($param[$v])) {
                Logger::info(__METHOD__ . ' invalid param, ' . $v . ' is not set, param = ' . json_encode($param) . ', need = ' . json_encode($need));
                return false;
            }
        }

        return true;
    }

    /**
     * 参数检查 - 是否非空
     * @param array $param
     * @param array $need
     * @return bool - true: 检测通过    false:检测不通过
     */
    public static function check_param_empty(array $param, array $need) {
        foreach($need as $v) {
            if(empty($param[$v])) {
                Logger::info(__METHOD__ . ' invalid param, ' . $v . ' is empty, param = ' . json_encode($param) . ', need = ' . json_encode($need));
                return false;
            }
        }

        return true;
    }

	
}
