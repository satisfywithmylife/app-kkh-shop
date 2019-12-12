<?php
apf_require_class("APF_Controller");

require CORE_PATH . 'classes/pingpp/init.php';

class Coupon_EditController extends APF_Controller
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
        Logger::info(__FILE__, __CLASS__, __LINE__, var_export($params, true));

        $kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $token = isset($params['user_token']) ? $params['user_token'] : '';
        $c_kkid = isset($params['c_kkid']) ? $params['c_kkid'] : '';
        $action = isset($params['action']) ? $params['action'] : 'disable'; // enable
        $locked = isset($params['locked']) ? $params['locked'] : '0'; // enable


        $id = "";  // bigint(21) 
        $kkid = "";  // char(32) 
        $u_kkid = "";  // char(32) 
        $coupon_code = "";  // varchar(50) 
        $coupon_value = "";  // 优惠券面额 int(11) 
        $last_used = "";  // date 
        $expiry_date = "";  // date 
        $submitted_by = "";  // char(32) 
        $success = "";  // tinyint(1) 
        $fail = "";  // tinyint(1) 
        $status = "";  // 0：无效 1：有效 tinyint(1) 
        $create_date = "";  // int(11) 
        $update_date = "";  // timestamp 
        //$locked = "";  // tinyint(1) 
        $channel = "";  // 优惠券发行渠道 varchar(60) 
        $coupon_type = "";  // 代金券类型1：普通，2：可变金额 tinyint(4) 
        $category = "";  // 关联t_coupon_category 类型表 int(11) 
        $min_use_price = "";  // 最小使用限额 int(11) 
        
        $id = isset($params['id']) ? $params['id'] : '';
        $kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $u_kkid = isset($params['u_kkid']) ? $params['u_kkid'] : '';
        $coupon_code = isset($params['coupon_code']) ? $params['coupon_code'] : '';
        $coupon_value = isset($params['coupon_value']) ? $params['coupon_value'] : '';
        $last_used = isset($params['last_used']) ? $params['last_used'] : '';
        $expiry_date = isset($params['expiry_date']) ? $params['expiry_date'] : '';
        $submitted_by = isset($params['submitted_by']) ? $params['submitted_by'] : '';
        $success = isset($params['success']) ? $params['success'] : '';
        $fail = isset($params['fail']) ? $params['fail'] : '';
        $status = isset($params['status']) ? $params['status'] : '';
        $create_date = isset($params['create_date']) ? $params['create_date'] : '';
        $update_date = isset($params['update_date']) ? $params['update_date'] : '';
        $locked = isset($params['locked']) ? $params['locked'] : '';
        $channel = isset($params['channel']) ? $params['channel'] : '';
        $coupon_type = isset($params['coupon_type']) ? $params['coupon_type'] : '';
        $category = isset($params['category']) ? $params['category'] : '';
        $min_use_price = isset($params['min_use_price']) ? $params['min_use_price'] : '';


        $status = 1;  // 是否有效。1为有效，0为无效 tinyint(1) 
        $created = time();  // Timestamp for when record was created. int(11) 
        $client_ip = Util_NetWorkAddress::get_client_ip();
        if($locked == 1){
          $status = 2;
        }
        else{
          $status = 0;
        }
        $res = array(
            'last_used' => date('y-m-d', $created),
            'submitted_by' => $kkid,
            'success' => $success,
            'fail' => $fail,
            'status' => $status,
            'locked' => $locked,
            'channel' => $channel,
        );


//      Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

        $msg = "normal request";
        $bll_user = new Bll_User_UserInfoUC();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
               //update
            if(true){

               $bll_coupon = new Bll_Coupon_Info();
               $bll_coupon->set_coupon_by_kkid($c_kkid, $kkid, $res);

               $coupon = $bll_coupon->get_coupon_by_kkid($c_kkid, $kkid);
               $res = $coupon;
               // add 2017-11-04 nignt
               if($res['status'] == 0){
                   // bouns 200 apple
                   // get user_pat_id
                   // o_kkid  分享优惠券的人
                   Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));
               }


            }
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
            "coupon" => $coupon,
        )));
        return false ;
    }

/*
mysql> select * from t_coupon where status=0 and o_kkid!='' order by id desc limit 1\G;
*************************** 1. row ***************************
           id: 1872
         kkid: 785799FAC15211E7BBD000163E0EB924
       u_kkid: 94AB102CC15211E7BBD000163E0EB924
       o_kkid: F12AA3EF984B11E7B2AF00163E0EB924
  coupon_code: bdhyqca
 coupon_value: 20
    last_used: 2017-11-04
  expiry_date: 2018-02-02
 submitted_by: 94AB102CC15211E7BBD000163E0EB924
      success: 1
         fail: 0
       status: 0
  create_date: 1509794560
  update_date: 2017-11-04 19:25:45
       locked: 0
      channel: kkh-api
  coupon_type: 1
     category: 1
min_use_price: 0
1 row in set (0.00 sec)

ERROR: 
No query specified

mysql> select * from  patient where kkid='F12AA3EF984B11E7B2AF00163E0EB924'\G;
*************************** 1. row ***************************
              id: 957028
       user_type: PAT
             sex: M
        password: 6756
       phone_num: 18616851610
       real_name: 自在客Tony
      header_pic: headerpic/PAT/957028/737f1980aca611e785eb00163e02165c
   register_time: 2017-06-23 01:03:15
             age: 1985
last_update_time: 2017-11-03 07:04:08
 pwd_update_time: NULL
 pwd_create_time: NULL
    device_token: NULL
    push_user_id: NULL
 push_channel_id: NULL
 last_login_time: 2017-10-30 07:17:57
     app_version: 6.7.2
     device_type: iphone 6plus
       device_id: 8B51625B-E917-447A-95F6-6EBEA60352AE
              os: ios
      os_version: 11.0.3
         channel: i00
        is_login: 1
       region_id: 15
           extra: 
       weixin_id: onFyguHWZekxRl3mxCYwRdgDI-Jw
       wx_openid: o_6qLuJDlObaj6tKV4bsiHSu4KHc
 internal_remark: 
           ad_id: AE819CAE6A314D7182741DE734B5E6E0
            kkid: F12AA3EF984B11E7B2AF00163E0EB924
      user_token: OZtB5Qkf0x_5woHFugX_xOrhMoUW60ZfTBSgvT_fEgw
1 row in set (0.00 sec)

ERROR: 
No query specified

*/


}
