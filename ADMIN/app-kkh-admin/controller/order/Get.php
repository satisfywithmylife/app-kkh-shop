<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/4
 * Time: 16:21
 */
apf_require_class('APF_Controller');

class Order_GetController extends APF_Controller {
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
            'begin_date',
            'end_date',
            'first_order',
            'order_status',
			'order_source',
			'order_type',
            'page_num',
            'page_size'
        );

        $need_param_empty = array(
            'page_num',
            'page_size'
        );
        $param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
        $param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
        if($param_check_set === false || $param_check_empty === false) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }
		
        $begin_date = $param['begin_date'];
        $end_date = $param['end_date'];
        if(!empty($end_date)) {
            $end_date = date('Y-m-d H:i:s' ,strtotime($end_date) + 86400);
        }
        $first_order = intval($param['first_order']);
        $order_status = intval($param['order_status']);
		$order_source = $param['order_source'];
		$order_type = $param['order_type'];
        $page_num = intval($param['page_num']);
        $page_size = intval($param['page_size']);

		if($order_source == '所有来源') {
			$order_source = '';
		}
		if($order_type == '所有类型') {
			$order_type = '';
		}

        if(!in_array($first_order, array(0, 1, -1))
            || !in_array($order_status, array(-1, 1, 2, 4, 6, 7, 13))
            || $page_num <= 0
            || $page_size <= 0) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $bll_order = new Bll_OrderBackStage_Info();
        $ret = $bll_order->get($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size);
		
        self::$success_ret['data'] = $ret;
		
        echo json_encode(self::$success_ret);
        return false;
    }
}
