<?php
apf_require_class("APF_Controller");
require CORE_PATH . 'classes/pingpp/init.php';

class Registration_ListController extends APF_Controller
{

    private $pdo;
    private $pingpp_api_key;
    private $pingpp_api_id;

    public function __construct() {
        $this->pdo = APF_DB_Factory::get_instance()->get_pdo("master");
        $this->pingpp_api_key = 'sk_live_jFVdZheje3nuOnIFUbEtNb02';
        $this->pingpp_api_id = 'app_P8yzn5fHuTeLSiXz';
    }


    public function handle_request()
    {

        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:SIG,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        header("Content-type: application/json; charset=utf-8");

        $req = APF::get_instance()->get_request();
        $params = $req->get_parameters();

        $default_kkid = "6F86727E527411E79E6C68F728954D54"; // 422,1,2  / 朝阳

        $kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $token = isset($params['user_token']) ? $params['user_token'] : '';

        $action = "list";

        $page_num = 0;
        $page_size = 20;


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


        $registration_list = array();
        $total = 0;
        $bll_user = new Bll_User_UserInfo();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
               //view
            if($action == "list"){ 
                # Bll_Registration_Info
                $bll_registration = new Bll_Registration_Info();
                $registration_list = $bll_registration->get_registration_list($kkid, $page_size, $page_start);
                foreach($registration_list as $k=>$j){
/**/
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
                               // update t_payment_charge time_paid , payment_status
                               // update t_registration payment_status
                               // send notify mail
                               $bll_registration->mail_notifiaction($r_kkid, $registration, 'paid', CS_MAIL_GROUP);
                               $bll_registration->mail_pay_notifiaction($r_kkid, $ch, $registration, 'paid', PAY_MAIL_GROUP, TECH_MAIL_GROUP);
                           }
    
                           Logger::info(__FILE__, __CLASS__, __LINE__, "check payment paid2: " . $ch['paid']);
                           //Logger::info(__FILE__, __CLASS__, __LINE__, var_export($ch, true));
                        }
                      }
                  }

/**/
                }
                $registration_list = $bll_registration->get_registration_list($kkid, $page_size, $page_start);
                $total = $bll_registration->get_registration_count($u_kkid);
            }
            $msg = "update success";
            $msg1 = "Successfully_modified";
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
            "registration_list" => $registration_list,
        )));

        return false;
    }

}
