<?php
apf_require_class("APF_Controller");

class Coupon_BaseInfoController extends APF_Controller
{
    private $pdo;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("coupon_master");
    }

    public function handle_request()
    {

        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

        $kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $token = isset($params['user_token']) ? $params['user_token'] : '';
        $c_kkid = isset($params['c_kkid']) ? $params['c_kkid'] : '';
        $action = isset($params['action']) ? $params['action'] : 'view';

        $security = Util_Security::Security($params);

        if (!$security) {
            echo json_encode(Util_Beauty::wanna(array(
                'code' => 0,
                'codeMsg' => 'Illegal_request',
                'status' => 'fail',
                'msg' => "request forbidden",
                "userMsg" => 'Illegal_request',
            )));

            return false;
        }


        $coupon = array();

        $bll_user = new Bll_User_UserInfoUC();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
            //view
            # Bll_Coupon_Info
            $bll_coupon = new Bll_Coupon_Info();
            $coupon = $bll_coupon->get_coupon_by_kkid($c_kkid, $kkid); // user's coupon

            if($action == "view"){
            }

            if(empty($coupon)){
               $coupon = array('error' => 'no coupon');
            }


            $msg = "success";
            $msg1 = "Successfully";

        }else{
            $msg = "ACCESS DENIED";
        }

        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => $msg,
            "coupon" => $coupon,
        )));

        return false;
    }

}
