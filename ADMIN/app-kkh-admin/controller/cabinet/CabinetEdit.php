<?php
/**
 * Created by PhpStorm.
 * User: hanxiaolong
 * Date: 2018/4/16
 * Time: 17:07
 */
apf_require_class('APF_Controller');

class Cabinet_CabinetEditController extends APF_Controller {
    public static $CABINET_STATUS = array(1, 2); //1正常, 2故障

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
            'id_hospital',
			'id_province',
			'id_city',
            'id_cabinet',
            'cabinet_name',
            'cabinet_address',
            'cabinet_status',
            'charge_person'
        );

        $need_param_empty = array(
            'id_hospital',
			'id_province',
			'id_city',
            'id_cabinet',
            'cabinet_name',
            'cabinet_address',
            'cabinet_status',
            'charge_person'
        );
        $param_check_set = Comment_ProductListController::check_param_set($param, $need_param_set);
        $param_check_empty = Comment_ProductListController::check_param_empty($param, $need_param_empty);
        if($param_check_set === false || $param_check_empty === false) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }

        $id_hospital = intval($param['id_hospital']);
		$id_province = intval($param['id_province']);
		$id_city = intval($param['id_city']);
        $id_cabinet = intval($param['id_cabinet']);
        $cabinet_name = $param['cabinet_name'];
        $address = $param['cabinet_address'];
        $cabinet_status = intval($param['cabinet_status']);
        $charge_person = $param['charge_person'];

        if ($id_hospital <= 0 || $id_province <= 0 || $id_city <= 0 || $id_cabinet <= 0) {
            Logger::info(__METHOD__ . ' invalid param, id_hospital invalid, id_hospital = ' . $id_hospital);
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
            return false;
        }
		
        if(!in_array($cabinet_status, Cabinet_CabinetEditController::$CABINET_STATUS)) {
            self::$error_ret['msg'] = 'invalid param';
            echo json_encode(self::$error_ret);
			Logger::info(__METHOD__ . ' param = ' . json_encode($param));
            return false;
        }

        $bll_cabinet = new Bll_Cabinet_Info();
        $ret = $bll_cabinet->cabinet_edit($id_hospital, $id_province, $id_city, $id_cabinet, $cabinet_name, $address, $cabinet_status, $charge_person);
		
        if($ret === true) {
            echo json_encode(self::$success_ret);
        } else {
			self::$error_ret['msg'] = 'server error';
            echo json_encode(self::$error_ret);
        }

        return false;
    }
}
