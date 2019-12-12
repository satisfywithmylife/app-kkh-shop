<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/4
 * Time: 16:21
 */
apf_require_class('APF_Controller');

class Order_ExportController extends APF_Controller {
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
            'begin_date',
            'end_date',
            'first_order',
            'order_status',
			'order_source',
			'order_type',
        );

        $need_param_empty = array(
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
        $page_num = -1;
        $page_size = -1;
		//$export_path = dirname(__FILE__) . '/订单列表.xlsx';

		if ($order_source == '所有来源') {
			$order_source = '';
		}
		if ($order_type == '所有类型') {
			$order_type = '';
		}

        if(!in_array($first_order, array(0, 1, -1))
            || !in_array($order_status, array(-1, 1, 2, 4, 6, 7, 13))) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $bll_order = new Bll_OrderBackStage_Info();
        $ret = $bll_order->export($begin_date, $end_date, $first_order, $order_status, $order_source, $order_type, $page_num, $page_size);

		if($ret === true) {
			echo json_encode(self::$success_ret);
		} else {
		    self::$error_ret['msg'] = 'content is empty, nothing to export';
			echo json_encode(self::$error_ret);
		}

        return false;
    }
}
