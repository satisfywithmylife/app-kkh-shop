<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/16
 * Time: 17:07
 */
apf_require_class('APF_Controller');

class Cabinet_StockOutGetController extends APF_Controller {
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
            'date_begin',
            'date_end',
            'order_status',
            'page_size',
            'page_num',
        );

        $need_param_empty = array(
            'page_size',
            'page_num',
        );
        $param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
        $param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
        if($param_check_set === false || $param_check_empty === false) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $date_begin = $param['date_begin'];
        $date_end = $param['date_end'];
        $order_status = intval($param['order_status']);
        $page_size = intval($param['page_size']);
        $page_num = intval($param['page_num']);

        if ($page_size < 1 || $page_num < 1) {
            Logger::info(__METHOD__ . ' invalid param, param = ' . json_encode($param));
            self::$error_ret['msg'] = 'invalid param';
            return false;
        }

        $bll_cabinet = new Bll_Cabinet_Info();
        $ret = $bll_cabinet->stock_out_get($date_begin, $date_end, $order_status, $page_num, $page_size);

        if($ret === false) {
            Logger::info(__METHOD__ . ' server error, param = ' . json_encode($param));
            echo json_encode(self::$error_ret);
        } else {
            self::$success_ret['data'] = $ret;
            echo json_encode(self::$success_ret);
        }

        return false;
    }
}
