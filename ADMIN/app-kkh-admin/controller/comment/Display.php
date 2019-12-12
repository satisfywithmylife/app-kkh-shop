<?php
apf_require_class("APF_Controller");

class Comment_DisplayController extends APF_Controller {
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
        $param = $req->get_parameters(); //todo 小程序评论所有参数都要rawurldecode, 后台评论是否需要

		$need_param_set = array(
			'id_comment',
			'display',
			'operator'
		);

		$need_param_empty = array(
			'id_comment',
			'operator'
		);
		$param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
		$param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
		if($param_check_set === false || $param_check_empty === false) {
			self::$error_ret['msg'] = 'invalid param';
			echo json_encode(self::$error_ret);
			return false;
		}

		$id_comment = intval($param['id_comment']);
		$display = $param['display'] === "true" ? 1 : 0;
		$operator = rawurldecode($param['operator']); //展示/隐藏评论的操作人

        $bll_comment = new Bll_Comment_Info();
        $ret = $bll_comment->display($id_comment, $display, $operator);
		
		if($ret !== true) {
			self::$error_ret['msg'] = 'server error';
			echo json_encode(self::$error_ret);
			return false;
		}

		self::$success_ret['data']['id_comment'] = $id_comment;
        echo json_encode(self::$success_ret);
        return false;
    }
}
