<?php
apf_require_class("APF_Controller");

require CORE_PATH . 'classes/pingpp/init.php';

class Registration_EditController extends APF_Controller
{
    private $pingpp_api_key;
    private $pingpp_api_id;

    public function __construct() {
/**
PINGPP_API_KEY = 'sk_live_jFVdZheje3nuOnIFUbEtNb02'
PINGPP_APP_ID = 'app_P8yzn5fHuTeLSiXz'

$ch = \Pingpp\Charge::create(
    array(
        'order_no'  => '123456789',
        'app'       => array('id' => 'APP_ID'),
        'channel'   => 'alipay', //wx_pub
        'amount'    => 100,
        'client_ip' => '127.0.0.1',
        'currency'  => 'cny',
        'subject'   => 'Your Subject',
        'body'      => 'Your Body',
        'extra'     => $extra
    )
);


*/
        $this->pingpp_api_key = 'sk_live_jFVdZheje3nuOnIFUbEtNb02';
        $this->pingpp_api_id = 'app_P8yzn5fHuTeLSiXz';
        \Pingpp\Pingpp::setApiKey($this->pingpp_apikey);
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
        $r_kkid = isset($params['r_kkid']) ? $params['r_kkid'] : '';

        $u_kkid = "";  // user kkid , 会员ID char(32) 
        $truename = "";  // 患者真实姓名 varchar(60) 
        $identitycard = "";  // 身份证号码 varchar(80) 
        $mobile_num = "";  // 患者手机号 mobile num varchar(60) 
        $h_kkid = "";  // hospital uuid, 所属医院 char(32) 
        $hd_kkid = "";  // department uuid, 所属科室 char(32) 
        $d_kkid = "";  // doctor uuid, 所属医生 char(32) 
        $first_visit = "";  // 是否初诊。1为初诊，2为复诊 tinyint(1) 
        $checkin_date = "";  // 挂号日期 date 
        $checkin_hour = "";  // 挂号时间 tinyint(1) 
        $disease_type = "";  // 疾病类型 int(11) 
        $outpatient_type = "";  // 门诊类型 tinyint(1) 
        $price = "";  // 挂号手续费 RMB int(11) 

        $u_kkid = isset($params['kkid']) ? $params['kkid'] : '';
        $truename = isset($params['truename']) ? $params['truename'] : '';
        $identitycard = isset($params['identitycard']) ? $params['identitycard'] : '';
        $mobile_num = isset($params['mobile_num']) ? $params['mobile_num'] : '';
        $h_kkid = isset($params['h_kkid']) ? $params['h_kkid'] : '';
        $hd_kkid = isset($params['hd_kkid']) ? $params['hd_kkid'] : '';
        $d_kkid = isset($params['d_kkid']) ? $params['d_kkid'] : '';
        $first_visit = isset($params['first_visit']) ? $params['first_visit'] : '';
        $checkin_date = isset($params['checkin_date']) ? $params['checkin_date'] : '';
        $checkin_hour = isset($params['checkin_hour']) ? $params['checkin_hour'] : '';
        $disease_type = isset($params['disease_type']) ? $params['disease_type'] : '';
        $outpatient_type = isset($params['outpatient_type']) ? $params['outpatient_type'] : '';
        $price = 20;

        /* params */
        $payment_method = "";  // 支付方式，1:到付 2:在线支付 tinyint(1) 
        $service_charge = "";  // 平台服务费 RMB int(11) 
        $payment_channel = "";  // 1: alipay 2: wxpay tinyint(1) 
        $payment_order_sid = "";  // 收款流水号 varchar(200) 
        $payment_status = "";  // 0: 待付款 1: 已付款 2: 已退款 tinyint(1) 
        $payment_modify = "";  // 支付操作时间 int(11) 
        $status = "1";  // 是否有效。1为有效，0为无效 tinyint(1) 
        $created = time();  // Timestamp for when record was created. int(11) 
        $client_ip = Util_NetWorkAddress::get_client_ip();

        $res = array(
            'u_kkid' => $u_kkid,
            'truename' => $truename,
            'identitycard' => $identitycard,  //身份证
            'mobile_num' => $mobile_num,
            'h_kkid' => $h_kkid, // 医院
            'hd_kkid' => $hd_kkid, // 科室
            'd_kkid' => $d_kkid, // 医生
            'first_visit' => $first_visit,
            'checkin_date' => $checkin_date,
            'checkin_hour' => $checkin_hour,
            'disease_type' => $disease_type,
            'outpatient_type' => $outpatient_type,
            'price' => $price,
            'payment_method' => 0,
            'service_charge' => 0,
            'payment_channel' => 0,
            'payment_order_sid' => '',
            'payment_status' => 0,
            'payment_modify' => $created,
            'status' => 1,
            'created' => $created,
            'client_ip' => $client_ip,
        );

//      Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

        $msg = "normal request";
        $bll_user = new Bll_User_UserInfo();
        if($bll_user->verify_user_access_token($kkid, $token)){ //验证登录
               //update
            if(true){

               $bll_registration = new Bll_Registration_Info();
               $bll_registration->set_registration_by_kkid($r_kkid, $kkid, $res);

               $registration = $bll_registration->get_registration($r_kkid, $kkid);
               $res = $registration;

               #$res['wx_pay'] = '';
               #$res['alipay'] = '';
               if(!empty($r_kkid)){
                   $bll_registration->mail_notifiaction($r_kkid, $registration, 'update', CS_MAIL_GROUP, TECH_MAIL_GROUP);
               }

            }
            $msg = "update success";
            $msg1 = "Successfully_modified";
        }else{
            $msg = "ACCESS DENIED";
        }

        Util_Json::render(200, null, $msg, $res);
        return ;
    }


/*
tonycai@app01-010:~/bin$ ./mysqltl4php.pl -tt_registration
####################################################################
Variables List
####################################################################
$rid = "";  // Primary Key: Unique registration ID. bigint(20) unsigned 
$kkid = "";  // UNIQUE Key: uuid char(32) 
$u_kkid = "";  // user kkid , 会员ID char(32) 
$truename = "";  // 患者真实姓名 varchar(60) 
$identitycard = "";  // 身份证号码 varchar(80) 
$mobile_num = "";  // 患者手机号 mobile num varchar(60) 
$h_kkid = "";  // hospital uuid, 所属医院 char(32) 
$hd_kkid = "";  // department uuid, 所属科室 char(32) 
$d_kkid = "";  // doctor uuid, 所属医生 char(32) 
$first_visit = "";  // 是否初诊。1为初诊，2为复诊 tinyint(1) 
$checkin_date = "";  // 挂号日期 date 
$checkin_hour = "";  // varchar(20) 
$disease_type = "";  // varchar(50) 
$outpatient_type = "";  // varchar(50) 
$price = "";  // 挂号费 RMB int(11) 
$payment_method = "";  // 支付方式，1:到付 2:在线支付 tinyint(1) 
$service_charge = "";  // 平台服务费 RMB int(11) 
$payment_channel = "";  // 1: alipay 2: wxpay tinyint(1) 
$payment_order_sid = "";  // 收款流水号 varchar(200) 
$payment_status = "";  // 0: 待付款 1: 已付款 2: 已退款 tinyint(1) 
$payment_modify = "";  // 支付操作时间 int(11) 
$status = "";  // 是否有效。1为有效，0为无效 tinyint(1) 
$created = "";  // Timestamp for when record was created. int(11) 
$update_date = "";  // timestamp 
$client_ip = "";  // varchar(20) 

$rid = isset($params['rid']) ? $params['rid'] : '';
$kkid = isset($params['kkid']) ? $params['kkid'] : '';
$u_kkid = isset($params['u_kkid']) ? $params['u_kkid'] : '';
$truename = isset($params['truename']) ? $params['truename'] : '';
$identitycard = isset($params['identitycard']) ? $params['identitycard'] : '';
$mobile_num = isset($params['mobile_num']) ? $params['mobile_num'] : '';
$h_kkid = isset($params['h_kkid']) ? $params['h_kkid'] : '';
$hd_kkid = isset($params['hd_kkid']) ? $params['hd_kkid'] : '';
$d_kkid = isset($params['d_kkid']) ? $params['d_kkid'] : '';
$first_visit = isset($params['first_visit']) ? $params['first_visit'] : '';
$checkin_date = isset($params['checkin_date']) ? $params['checkin_date'] : '';
$checkin_hour = isset($params['checkin_hour']) ? $params['checkin_hour'] : '';
$disease_type = isset($params['disease_type']) ? $params['disease_type'] : '';
$outpatient_type = isset($params['outpatient_type']) ? $params['outpatient_type'] : '';
$price = isset($params['price']) ? $params['price'] : '';
$payment_method = isset($params['payment_method']) ? $params['payment_method'] : '';
$service_charge = isset($params['service_charge']) ? $params['service_charge'] : '';
$payment_channel = isset($params['payment_channel']) ? $params['payment_channel'] : '';
$payment_order_sid = isset($params['payment_order_sid']) ? $params['payment_order_sid'] : '';
$payment_status = isset($params['payment_status']) ? $params['payment_status'] : '';
$payment_modify = isset($params['payment_modify']) ? $params['payment_modify'] : '';
$status = isset($params['status']) ? $params['status'] : '';
$created = isset($params['created']) ? $params['created'] : '';
$update_date = isset($params['update_date']) ? $params['update_date'] : '';
$client_ip = isset($params['client_ip']) ? $params['client_ip'] : '';

####################################################################
Array Statement
####################################################################
$res = array(
    'rid' => $rid,
    'kkid' => $kkid,
    'u_kkid' => $u_kkid,
    'truename' => $truename,
    'identitycard' => $identitycard,
    'mobile_num' => $mobile_num,
    'h_kkid' => $h_kkid,
    'hd_kkid' => $hd_kkid,
    'd_kkid' => $d_kkid,
    'first_visit' => $first_visit,
    'checkin_date' => $checkin_date,
    'checkin_hour' => $checkin_hour,
    'disease_type' => $disease_type,
    'outpatient_type' => $outpatient_type,
    'price' => $price,
    'payment_method' => $payment_method,
    'service_charge' => $service_charge,
    'payment_channel' => $payment_channel,
    'payment_order_sid' => $payment_order_sid,
    'payment_status' => $payment_status,
    'payment_modify' => $payment_modify,
    'status' => $status,
    'created' => $created,
    'update_date' => $update_date,
    'client_ip' => $client_ip
);
####################################################################
Insert Statement
####################################################################
insert into `t_registration` (`rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip`) values(:rid, :kkid, :u_kkid, :truename, :identitycard, :mobile_num, :h_kkid, :hd_kkid, :d_kkid, :first_visit, :checkin_date, :checkin_hour, :disease_type, :outpatient_type, :price, :payment_method, :service_charge, :payment_channel, :payment_order_sid, :payment_status, :payment_modify, :status, :created, :update_date, :client_ip);
####################################################################
Update Statement
####################################################################
update `t_registration` set `rid` = :rid, `kkid` = :kkid, `u_kkid` = :u_kkid, `truename` = :truename, `identitycard` = :identitycard, `mobile_num` = :mobile_num, `h_kkid` = :h_kkid, `hd_kkid` = :hd_kkid, `d_kkid` = :d_kkid, `first_visit` = :first_visit, `checkin_date` = :checkin_date, `checkin_hour` = :checkin_hour, `disease_type` = :disease_type, `outpatient_type` = :outpatient_type, `price` = :price, `payment_method` = :payment_method, `service_charge` = :service_charge, `payment_channel` = :payment_channel, `payment_order_sid` = :payment_order_sid, `payment_status` = :payment_status, `payment_modify` = :payment_modify, `status` = :status, `created` = :created, `update_date` = :update_date, `client_ip` = :client_ip where `rid` = :rid ;
####################################################################
Select Statement
####################################################################
select `rid`, `kkid`, `u_kkid`, `truename`, `identitycard`, `mobile_num`, `h_kkid`, `hd_kkid`, `d_kkid`, `first_visit`, `checkin_date`, `checkin_hour`, `disease_type`, `outpatient_type`, `price`, `payment_method`, `service_charge`, `payment_channel`, `payment_order_sid`, `payment_status`, `payment_modify`, `status`, `created`, `update_date`, `client_ip` from `t_registration` where `rid` = ? ;
####################################################################
PHP PDO Statement
####################################################################

  $stmt = $this->pdo->prepare($sql);
  $stmt->execute($res);
  
  $last_id = $this->pdo->lastInsertId();
  $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

Help Document:
https://secure.php.net/manual/zh/class.pdostatement.php
*/

}
