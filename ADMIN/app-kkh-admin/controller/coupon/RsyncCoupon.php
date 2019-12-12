<?php
apf_require_class("APF_Controller");

class Coupon_RsyncCouponController extends APF_Controller
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

        $security = Util_Security::Security($params);
        if (!$security) {
            Util_Json::render(400, null, 'request forbidden', 'Illegal_request');
            return false;
        }
        Logger::info(__FILE__, __CLASS__, __LINE__, 'Rsync Coupon start -------------------');
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));
        Logger::info(__FILE__, __CLASS__, __LINE__, 'Rsync Coupon end -------------------');

        $kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $token = isset($params['user_token']) ? $params['user_token'] : '';
        $mobile = isset($params['mobile']) ? $params['mobile'] : '';
        $action = isset($params['action']) ? $params['action'] : 'rsync';

//      Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

        $msg = "normal request";
        $bll_user = new Bll_User_UserInfoUC();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
            //update
            //$res = self::mv_coupon_code($kkid, $mobile);
            $bll_coupon = new Bll_Coupon_Info();
            $bll_coupon->mv_coupon_code($kkid, $mobile);
            $msg = "update success";
            $msg1 = "Successfully_modified";

        }else{
            $msg = "ACCESS DENIED";
        }

        //Util_Json::render(200, null, $msg, $res);
        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => $msg,
            "action" => $action,
            "res" => $res,
        )));
        return false ;
    }

    private function mv_coupon_code($u_kkid, $mobile)
    {
        if(empty($u_kkid) || empty($mobile)){
            return array();
        }
        $sql = "update t_coupon set u_kkid=:u_kkid where u_kkid=:mobile limit 5;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':u_kkid', $u_kkid, PDO::PARAM_STR);
        $stmt->bindParam(':mobile', $mobile, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
