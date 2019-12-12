<?php
apf_require_class("APF_Controller");

require CORE_PATH . 'classes/pingpp/init.php';

class Registration_BaseInfoController extends APF_Controller
{
    private $pdo;
    private $pingpp_api_key;
    private $pingpp_api_id;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
        $this->pingpp_api_key = PINGPP_API_KEY;
        $this->pingpp_api_id = PINGPP_API_ID;
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
        $r_kkid = isset($params['r_kkid']) ? $params['r_kkid'] : '';

        $action = isset($params['action']) ? $params['action'] : 'view'; //pay
        $payment_channel = isset($params['payment_channel']) ? $params['payment_channel'] : 'wx_pub'; //alipay
        $open_id = isset($params['open_id']) ? $params['open_id'] : '';
        // success_url
        // cancel_url


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


        $registration = array();
        $total = 0;
        $bll_user = new Bll_User_UserInfo();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
               //view
                # Bll_Registration_Info
            $bll_registration = new Bll_Registration_Info();
            $registration = $bll_registration->get_registration($r_kkid, $kkid);
            $bll_payment = new Bll_Payment_Info();
            $charge_list = array();
            if($action == "view"){
              if(!empty($registration) && $registration['payment_status'] == 0){
                  $charge_list = $bll_payment->get_payment_charge_list($registration['kkid'], 10, 0);
                  \Pingpp\Pingpp::setApiKey($this->pingpp_api_key);
                  foreach($charge_list as $k=>$j){
                    if(isset($j['charge_id']) && !empty($j['charge_id'])){
                       Logger::info(__FILE__, __CLASS__, __LINE__, "check payment status: " . $j['charge_id']);
                       $ch = \Pingpp\Charge::retrieve($j['charge_id']);
                       $is_paid = 0;
                       if($ch['paid']){
                           $is_paid = 1;
                           $bll_payment->set_payment_status($j['charge_id'], 1, $ch['time_paid']);
                           $bll_registration->set_registration_paystatus_by_kkid($registration['kkid'], $kkid, 1);
                           $registration = $bll_registration->get_registration($r_kkid, $kkid);
                           // send notify mail
                           $bll_registration->mail_notifiaction($r_kkid, $registration, 'paid', CS_MAIL_GROUP);
                           $bll_registration->mail_pay_notifiaction($r_kkid, $ch, $registration, 'paid', PAY_MAIL_GROUP, TECH_MAIL_GROUP);

                           // update t_payment_charge time_paid , payment_status
                           // update t_registration payment_status
              
                       }
                       
                       Logger::info(__FILE__, __CLASS__, __LINE__, "check payment paid1: " . $ch['time_paid']);
                       Logger::info(__FILE__, __CLASS__, __LINE__, "check payment paid2: " . $ch['paid']);
                       //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($ch, true));
                    }
                  }
              }
            }
            if($action == "pay"){
               Logger::info(__FILE__, __CLASS__, __LINE__, "action: " . $action);
               $client_ip = Util_NetWorkAddress::get_client_ip();
               $ch = array();
               if(in_array($payment_channel , array("wx_wap", "wx_pub"))){
                   $ch = self::create_wxpay_order($payment_channel, $r_kkid, 20, $client_ip, '挂号预约服务费', 'h5 request', $open_id);
                   $registration['wx_pay'] = $ch;
               }
               if(in_array($payment_channel, array("alipay_wap","alipay"))){
                   $ch = self::create_alipay_order($payment_channel, $r_kkid, 20, $client_ip, '挂号预约服务费', 'h5 request', $open_id);
                   $registration['alipay'] = $ch;
               }
            
            $charge_id = isset($ch['id']) ? $ch['id'] : '';
            $charge_created = isset($ch['created']) ? $ch['created'] : '';
            $channel = isset($ch['channel']) ? $ch['channel'] : '';
            $order_no = isset($ch['order_no']) ? $ch['order_no'] : '';
            $client_ip_pp = isset($ch['client_ip']) ? $ch['client_ip'] : '';
            $amount = isset($ch['amount']) ? $ch['amount'] : '';
            $currency = isset($ch['currency']) ? $ch['currency'] : '';
            $subject = isset($ch['subject']) ? $ch['subject'] : '';
            $body = isset($ch['body']) ? $ch['body'] : '';
            $time_paid = isset($ch['time_paid']) && !empty($ch['time_paid']) ? $ch['time_paid'] : '';
            $time_expire = isset($ch['time_expire']) ? $ch['time_expire'] : '';
            $payment_status = 0;
            $status = 1;
            $created = time();
            $client_ip = Util_NetWorkAddress::get_client_ip();
            
            $charge_data = array(
                'r_kkid' => $r_kkid,
                'charge_id' => $charge_id,
                'charge_created' => $charge_created,
                'channel' => $channel,
                'order_no' => $order_no,
                'client_ip_pp' => $client_ip_pp,
                'amount' => $amount,
                'currency' => $currency,
                'subject' => $subject,
                'body' => $body,
                'time_paid' => $time_paid,
                'time_expire' => $time_expire,
                'payment_status' => $payment_status,
                'status' => $status,
                'created' => $created,
                'client_ip' => $client_ip
            );
               Logger::info(__FILE__, __CLASS__, __LINE__, var_export($charge_data, true));
               //$bll_payment = new Bll_Payment_Info();
               $bll_payment->create_payment_charge($charge_data);
               Logger::info(__FILE__, __CLASS__, __LINE__, "charge_id: " . $ch['id']);

            }


            $msg = "update success";
            $msg1 = "Successfully_modified";
        }else{
            $msg = "ACCESS DENIED";
        }

        echo json_encode(Util_Beauty::wanna(array(
            'code' => 1,
            'codeMsg' => $msg,
            "registration" => $registration,
            "charge_list" => $charge_list,
        )));

        return false;
    }

    private function create_wxpay_order($payment_channel, $r_kkid, $price, $client_ip, $subject, $body, $open_id){
       $r_kkid = substr($r_kkid,0,20) . '0' . time();
       Logger::info(__FILE__, __CLASS__, __LINE__, "channel: " .$payment_channel);
       Logger::info(__FILE__, __CLASS__, __LINE__, "open_id: " .$open_id);

       $extra = array('open_id'=> $open_id);
       $res1 = array(
               'order_no'  => $r_kkid,
               'app'       => array('id' => $this->pingpp_api_id ),
               'channel'   => $payment_channel, //wx_pub , alipay
               'amount'    => $price*100,
               'client_ip' => $client_ip,
               'currency'  => 'cny',
               'subject'   => $subject,
               'body'      => $body,
               'extra'     => $extra,
           );
       Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res1, true));

       \Pingpp\Pingpp::setApiKey($this->pingpp_api_key);
/*
       if($payment_channel == 'wx_wap'){
           $extra = array('result_url'=>'http://touch.registration.kangkanghui.com/paySuccess');
       }
*/

       $ch = \Pingpp\Charge::create(
           array(
               'order_no'  => $r_kkid,
               'app'       => array('id' => $this->pingpp_api_id ),
               'channel'   => $payment_channel, //wx_pub , alipay
               'amount'    => $price*100,
               'client_ip' => $client_ip,
               'currency'  => 'cny',
               'subject'   => $subject,
               'body'      => $body,
               'extra'     => $extra,
           )
       );
       

       return $ch;
    }

    private function create_alipay_order($payment_channel, $r_kkid, $price, $client_ip, $subject, $body, $open_id){
       //$r_kkid = "a" . $r_kkid;

       \Pingpp\Pingpp::setApiKey($this->pingpp_api_key);
       $extra = array();
       if($payment_channel == 'alipay_wap'){
           $extra = array('success_url'=>'http://touch.registration.kangkanghui.com/paySuccess', 'cancel_url'=>'http://touch.registration.kangkanghui.com/payFail');
       }
       
       
       $ch = \Pingpp\Charge::create(
           array(
               'order_no'  => $r_kkid,
               'app'       => array('id' => $this->pingpp_api_id ),
               'channel'   => $payment_channel, //wx_pub , alipay_wap
               'amount'    => $price*100,
               'client_ip' => $client_ip,
               'currency'  => 'cny',
               'subject'   => $subject,
               'body'      => $body,
               'extra'     => $extra,
           )
       );

       return $ch;
    }
/*
                    [id] => ch_00OKy94qTibLDGGW5GH8SarT
                    [object] => charge
                    [created] => 1505489328
                    [livemode] => 1
                    [paid] => 
                    [refunded] => 
                    [reversed] => 
                    [app] => app_P8yzn5fHuTeLSiXz
                    [channel] => alipay_wap
                    [order_no] => D96ABD88937A11E79E8E00163E0EB924
                    [client_ip] => 120.55.36.93
                    [amount] => 2000
                    [amount_settle] => 1988
                    [currency] => cny
                    [subject] => registration
                    [body] => test request
                    [time_paid] => 
                    [time_expire] => 1505575728
                    [time_settle] => 
                    [transaction_no] => 
*/

}
