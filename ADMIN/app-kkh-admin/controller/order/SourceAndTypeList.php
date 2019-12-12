<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/8
 * Time: 16:29
 */
apf_require_class('APF_Controller');

class Order_SourceAndTypeListController extends APF_Controller {
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

        $bll_order = new Bll_OrderBackStage_Info();
        $ret = $bll_order->source_and_type_list();
		
		self::$success_ret['data'] = $ret;
		echo json_encode(self::$success_ret);

        return false;
    }
}
