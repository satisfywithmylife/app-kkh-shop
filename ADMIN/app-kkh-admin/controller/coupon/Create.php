<?php
apf_require_class("APF_Controller");

class Coupon_CreateController extends APF_Controller
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
        $action = isset($params['action']) ? $params['action'] : 'create';
        $num = isset($params['num']) ? $params['num'] : '1';

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
            $base_info = $bll_user->get_user_by_kkid($kkid);
            $mobile_num = $base_info['mobile_num'];
            $wxopenid = $base_info['wx_openid'];
            //view
            # Bll_Coupon_Info
            $bll_coupon_f = new Bll_Coupon_FshareInfo();
            /* */
            $coupon_value = "20";  // 优惠券面额 int(11) 
            $status = "1";  // tinyint(1) 
            $created = time();  // Timestamp for when record was created. int(11) 
            $update_date = "";  // timestamp 
            //$bll_coupon = new Bll_Coupon_Info();
            //$coupon = $bll_coupon->get_coupon_by_kkid($c_kkid, $kkid); // user's coupon
            $total = $num;
            $sender = "F12AA3EF984B11E7B2AF00163E0EB924";
            $receiver = $kkid;

               for ($x=1; $x<=$total; $x++) {
                    $coupon_code = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 7);
                    $share_coupon = array(
                        'id' => 0,
                        'kkid' => '',
                        'sender' => $sender,
                        'receiver' => $receiver,
                        'mobile_num' => $mobile_num,
                        'wxopenid' => $wxopenid,
                        'coupon_code' => $coupon_code,
                        'coupon_value' => $coupon_value,
                        'status' => $status,
                        'active_ver' => 3,
                        'created' => $created,
                        'update_date' => $update_date
                    );
                    $last_id = $bll_coupon_f->create_share_coupon($share_coupon); // user's share_coupon
                    $share_coupon = $bll_coupon_f->get_share_coupon($last_id);
                    $share_coupon_list[] = $share_coupon;
                }
                $share_coupon_total_value = $bll_coupon_f->get_share_coupon_total_value($receiver, 3);
                /* */
                //get_coupon_by_kkid
                $coupon_data = array(
                    'id' => 0,
                    'kkid' => '',
                    'u_kkid' => '',
                    'o_kkid' => '',
                    'coupon_code' => '',
                    'coupon_value' => 20,
                    'last_used' => '2018-03-31',//date('y-m-d', time() + 86400*90),
                    'expiry_date' => '2018-03-31',//date('y-m-d', time() + 86400*90),
                    'submitted_by' => '',
                    'success' => 0,
                    'fail' => 0,
                    'status' => 1,
                    'create_date' => time(),
                    'update_date' => '',
                    'locked' => 0,
                    'channel' => 'kkh-api',
                    'coupon_type' => 1,
                    'category' => 1,
                    'min_use_price' => 0,
                );
        
                $bll_coupon = new Bll_Coupon_Info();
                $coupon_list = array();
                foreach($share_coupon_list as $k=>$j){
                    $coupon_data['u_kkid'] = $j['receiver'];
                    $coupon_data['o_kkid'] = $j['sender'];
                    $coupon_data['coupon_code'] = $j['coupon_code'];
                    $last_id = $bll_coupon->create_coupon($coupon_data);
                    $coupon  = $bll_coupon->get_coupon($last_id);
                    $coupon_list[] = $coupon;
                }
                /* */



            $msg = "success";
            $msg1 = "Successfully";

        }else{
            $msg = "ACCESS DENIED";
        }

        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => $msg,
            "share_coupon_total_value" => $share_coupon_total_value,
            "share_coupon_list" => $share_coupon_list,
            "coupon_list" => $coupon_list,
            "params" => $params,
        )));

        return false;
    }

}
