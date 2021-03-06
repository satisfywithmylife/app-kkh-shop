<?php
apf_require_class("APF_Controller");

class Coupon_ExpireController extends APF_Controller
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
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $token = isset($params['user_token']) ? $params['user_token'] : '';

        $order_total_price = isset($params['order_total_price']) ? $params['order_total_price'] : '0';
        $action = isset($params['action']) ? $params['action'] : '';


        $page_num = 0;
        $page_size = 100;


        if (isset($params['page']) && is_numeric($params['page'])) {
           $page_num = intval($params['page']);
        }
        $page_num = $page_num <= 0 ? 1 : $page_num;
        $page_start = ($page_num - 1) * $page_size;
        $total = 0;

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

        $coupon_list = array();
        $total = 0;
        $bll_user = new Bll_User_UserInfoUC();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
               //view
            if($action == "list"){ 
                # Bll_Registration_Info
                $bll_coupon = new Bll_Coupon_Info();
                $coupon_list = $bll_coupon->get_coupon_list($kkid, $page_size, $page_start, 1);
                $total = $bll_coupon->get_coupon_count($kkid, 1);
            }
            $msg = "success";
            $msg1 = "Successfully";
        }else{
            $msg = "ACCESS DENIED";
        }

       echo json_encode(Util_Beauty::wanna(array(
           'code' => 1,
           'codeMsg' => $msg,
           "page_num" => $page_num,
           "page_size" => $page_size,
           "total" => $total,
           "action" => $action,
           "coupon_list" => $coupon_list,
       )));
       

       return false;
    }

}
