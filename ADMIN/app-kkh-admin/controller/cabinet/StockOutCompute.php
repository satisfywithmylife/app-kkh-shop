<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/16
 * Time: 17:07
 */
apf_require_class('APF_Controller');

class Cabinet_StockOutComputeController extends APF_Controller {
    private static $error_ret = array(
        "status" => 500,
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
            'cd_key',
            'id_order',
			'order_type',
			'extra'
        );

        $need_param_empty = array(
            'cd_key',
            'id_order',
			'order_type',
        );
        $param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
        $param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
        if($param_check_set === false || $param_check_empty === false) {
            Logger::info(__METHOD__ . ' invalid param, param = ' . json_encode($param));
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $cd_key = $param['cd_key'];
        $id_order = intval($param['id_order']);
		$order_type = intval($param['order_type']);
		$extra = intval($param['extra']);

        if(empty($cd_key) || $id_order < 1 || !in_array($order_type, array(1, 2))) {
            Logger::info(__METHOD__ . ' invalid param, param = ' . json_encode($param));
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $bll_cabinet = new Bll_Cabinet_Info();
        $ret = $bll_cabinet->stock_out_compute($cd_key, $id_order, $order_type, $extra);

        if($ret === false) {
            Logger::info(__METHOD__ . ' server error');
            self::$error_ret['msg'] = 'server error';
            echo json_encode(self::$error_ret);
        } else {
            self::$success_ret['data'] = $ret;
            echo json_encode(self::$success_ret);
        }

        return false;
    }
}
