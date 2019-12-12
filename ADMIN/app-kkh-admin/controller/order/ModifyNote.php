<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/8
 * Time: 16:29
 */
apf_require_class('APF_Controller');

class Order_ModifyNote extends APF_Controller {
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
            'id_order',
            'operator',
            'note',
        );

        $need_param_empty = array(
            'id_order',
            'operator',
        );
        $param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
        $param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
        if($param_check_set === false || $param_check_empty === false) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $id_order = intval($param['id_order']);
        $operator = $param['operator'];
        $note = $param['note'];

        if($id_order <= 0) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $bll_order = new Bll_OrderBackStage_Info();
        $ret = $bll_order->modify_note($id_order, $operator, $note);

        if($ret === true) {
            echo json_encode(self::$success_ret);
        } else {
            self::$error_ret['msg'] = 'server error';
            echo json_encode(self::$error_ret);
        }

        return false;
    }
}
