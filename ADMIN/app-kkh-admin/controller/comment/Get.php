<?php
//todo  登陆验证和token验证
apf_require_class("APF_Controller");

class Comment_GetController extends APF_Controller {
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
		
		$need_param_set = array(
			'source',
			'product_name',
			'only_display',
			'have_picture',
			'only_negative',
			'page_num',
			'page_size'
		);
		$need_param_empty = array(
			'page_num',
			'page_size'
		);
		$param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
		$param_check_empty = Comment_ProductListController::check_param_set($param, $need_param_empty);
		if($param_check_set === false || $param_check_empty === false) {
			self::$error_ret['msg'] = 'invalid param';
			echo json_encode(self::$error_ret);
			return false;
		}

		$source = intval($param['source']);
		$product_name = $param['product_name'];

		if($product_name === '' && $source === 1) { //外部评论只获取单个商品的评论, 商品名字不能为空
			self::$error_ret['msg'] = 'invalid param';
			Logger::info(__METHOD__ . ' invalid param, when source is 1, product_name cannot be empty');
			return false;
		}

		$only_display = intval($param['only_display']);
		$have_picture = intval($param['have_picture']);
		$only_negative = intval($param['only_negative']);
		$page_num = intval($param['page_num']);
		$page_size = intval($param['page_size']);

        $bll_comment = new Bll_Comment_Info();
        self::$success_ret['data'] = $bll_comment->get($source, $product_name, $only_display, $have_picture, $only_negative, $page_num, $page_size);
		
        echo json_encode(self::$success_ret);
        return false;
    }
}
