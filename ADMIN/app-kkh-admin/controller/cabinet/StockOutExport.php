<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/16
 * Time: 17:07
 */
apf_require_class('APF_Controller');

class Cabinet_StockOutExportController extends APF_Controller {
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
            'order_status'
        );

        $need_param_empty = array(
            'order_status'
        );
        $param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
        $param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
        if($param_check_set === false || $param_check_empty === false) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $order_status = intval($param['order_status']);

        $invalid_order_status = array(-1, 1, 2, 4, 6, 7, 13);
        if (!in_array($order_status, $invalid_order_status)) {
            Logger::info(__METHOD__ . ' invalid param, param = ' . json_encode($param));
            self::$error_ret['msg'] = 'invalid param';
            return false;
        }

        $bll_cabinet = new Bll_Cabinet_Info();
        $ret = $bll_cabinet->stock_out_export($order_status);

        if($ret === false) {
            Logger::info(__METHOD__ . ' server error, param = ' . json_encode($param));
            echo json_encode(self::$error_ret);
        } else {
            echo json_encode(self::$success_ret);
        }

        return false;
    }
}
