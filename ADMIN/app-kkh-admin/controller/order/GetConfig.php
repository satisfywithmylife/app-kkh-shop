<?php
/**
 */
apf_require_class('APF_Controller');

class Order_GetConfigController extends APF_Controller {

    public function handle_request() {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $param = $req->get_parameters();

        $bll_order_config = new Bll_Order_Config();
		$type_list = $bll_order_config->get_order_config();
		echo Util_Json::json_str(200, 'success', $type_list);
		return false;
    }
}
