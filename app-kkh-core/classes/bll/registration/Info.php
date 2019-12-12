<?php

class  Bll_Registration_Info {
	private $registrationInfoDao;

	public function __construct() {
		$this->registrationInfoDao = new Dao_Registration_Info();
	}

        public function set_registration_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->registrationInfoDao->set_registration_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function cancel_registration_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->registrationInfoDao->cancel_registration_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function set_registration_paystatus_by_kkid($r_kkid, $u_kkid, $status) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->registrationInfoDao->set_registration_paystatus_by_kkid($r_kkid, $u_kkid, $status);
        }

        public function add_registration($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->registrationInfoDao->add_registration($u_kkid, $data);
        }

        public function add_registration_sk($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->registrationInfoDao->add_registration_sk($u_kkid, $data);
        }
        
        public function get_registration($r_kkid, $u_kkid) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->registrationInfoDao->get_registration($r_kkid, $u_kkid);
        }

        public function get_registration_list($u_kkid, $limit, $offset)
        {
                if(empty($u_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->registrationInfoDao->get_registration_list($u_kkid, $limit, $offset);
        }

        public function get_registration_count($loc_code)
        {
                if(empty($loc_code)) {
                    return array();
                }
                return $this->registrationInfoDao->get_registration_count($loc_code);
        }

        public function get_location($kkid)
        {
                if(empty($kkid)) {
                    return array();
                }
                return $this->registrationInfoDao->get_location($kkid);
        }

        public function mail_notifiaction($r_kkid, $res, $ops, $to, $bcc)
        {
            $title = "有一个新的挂号预约 - 患者: " . $res['truename'];

            if($ops == 'new'){
               $title = "有一个新的挂号预约 - 患者: " . $res['truename'] . '【新建】';
            }

            if($ops == 'paid'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【已付款】';
               #$to = $to . ',' . 'dl_ops_alert@kangkanghui.com';
            }

            if($ops == 'cancel'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【已取消】';
            }

            if($ops == 'update'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【已更新】';
            }
            if($ops == 'expired'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【已退款】';
            }
            if($ops == 'expired_cs'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【转入人工服务】';
            }
            if($ops == 'succeed'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【抢号成功】';
            }
            if($ops == 'ticket'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【抢号机会】';
               $practice_points = isset($res['practice_points']) ? $res['practice_points'] : '';
            }
            if($ops == 'refund_failed'){
               $title = "有一个挂号预约 - 患者: " . $res['truename'] . '【退款失败】';
               $refund_failure_code = isset($res['refund_failure_code']) ? $res['refund_failure_code'] : '';
               $refund_failure_msg = isset($res['refund_failure_msg']) ? $res['refund_failure_msg'] : '';
            }
            if(isset($res['r_kkid']) && !empty($res['r_kkid'])){
                $r_kkid = substr($res['r_kkid'],0,8);
            }
            else{
                $r_kkid = substr($r_kkid,0,8);
            }
            $user_name = $res['truename'];
            $mobile_num = $res['mobile_num'];
            $identitycard = isset($res['identitycard']) ? $res['identitycard'] : '';
            $first_visit = $res['first_visit'];
            $checkin_date = $res['checkin_date'];
            $checkin_hour = $res['checkin_hour'];
            $price = $res['price'];
            $disease_type = $res['disease_type'];
            // h_kkid
            // hd_kkid
            // d_kkid

            $bll_hospital = new Bll_Hospital_Info();
            $hospital = $bll_hospital->get_hospital($res['h_kkid']);
            $hospital_name = $hospital['name'];

            $bll_department = new Bll_Department_Info();
            $department = $bll_department->get_department($res['hd_kkid']);
            $department_name = $department['name'];

            $bll_doctor = new Bll_Doctor_Info();
            $doctor = $bll_doctor->get_practice_data($res['hd_kkid'], $res['d_kkid'], $res['h_kkid']);
            $doctor_name = $doctor['name'];
    
            $now_date = date("Y-m-d");
            try {
    

              $mailbody = <<<MAILBODY
苹果妹妹 你好： <br />
<br />
患者： $user_name  , 提交了一次的挂号预约更新。 <br /><br />

订单编号：$r_kkid <br />
医院名称：$hospital_name <br />
科室名称：$department_name <br />
医生名称：$doctor_name <br />
患者姓名：$user_name <br />
患者手机：$mobile_num <br />
身份号码：$identitycard <br />
就诊日期：$checkin_date $checkin_hour <br />
就诊类型：$first_visit <br />
疾病类型：$disease_type <br />

<br />
$refund_failure_code
<br />
$refund_failure_msg
$practice_points
<br />
青苹果团队<br />
$now_date<br />
MAILBODY;

                Util_SmtpMail::send_qq(
                  $to,
                  $title,
                  $mailbody,
                  $bcc
                  );
 //Util_SmtpMail::send_qq($to,$subject,$body,$cc,$reply);
                $ret = array(
                    'status' => true,
                    'msg' => 'ok'
                );
            } catch (Exception $e) {
                $ret = array(
                    'status' => false,
                    'msg' => $e
                );
            }
            return;
        }

        public function mail_pay_notifiaction($r_kkid, $ch, $res, $ops, $to, $bcc)
        {
            $title = "【通知】用户付款";

            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($ch, true));

            if($ops == 'paid'){
               $title = "【通知】用户付款";
            }
            if($ops == 'expired'){
               $title = "【通知】用户退款";
            }

            if(isset($res['r_kkid']) && !empty($res['r_kkid'])){
                $r_kkid = substr($res['r_kkid'],0,8);
            }
            else{
                $r_kkid = substr($r_kkid,0,8);
            }

            $user_name = $res['truename'];
            $mobile_num = $res['mobile_num'];
            $first_visit = $res['first_visit'];
            $checkin_date = $res['checkin_date'];
            $checkin_hour = $res['checkin_hour'];
            $price = $res['price'];
            $disease_type = $res['disease_type'];
            // h_kkid
            // hd_kkid
            // d_kkid
            $bll_user = new  Bll_User_UserInfo();
            $user = $bll_user->get_user_by_kkid($res['u_kkid']);
            $user_mobile_num = $user['mobile_num'];

            $bll_hospital = new Bll_Hospital_Info();
            $hospital = $bll_hospital->get_hospital($res['h_kkid']);
            $hospital_name = $hospital['name'];

            $bll_department = new Bll_Department_Info();
            $department = $bll_department->get_department($res['hd_kkid']);
            $department_name = $department['name'];

            $bll_doctor = new Bll_Doctor_Info();
            $doctor = $bll_doctor->get_practice_data($res['hd_kkid'], $res['d_kkid'], $res['h_kkid']);
            $doctor_name = $doctor['name'];
    
            $now_date = date("Y-m-d");
            $time_paid = isset($ch['time_paid']) && !empty($ch['time_paid']) ? date('Y-m-d H:i:s', $ch['time_paid']) : date('Y-m-d H:i:s');
            $charge_id = isset($ch['id']) && !empty($ch['id']) ?  $ch['id'] : 'charge_id';
            $amount = isset($ch['amount']) && !empty($ch['amount']) ? $ch['amount']/100 : '0';
            $channel = isset($ch['channel']) && !empty($ch['channel']) ? $ch['channel'] : '';

            try {
    

              $mailbody = <<<MAILBODY
$user_name <$user_mobile_num>  支付 $amount 元，购买了 抢号神器 的挂号服务。 收款凭据: $charge_id 。
<br />
<br />
付款渠道：$channel
<br />
<br />
订单编号：$r_kkid <br />
MAILBODY;

            if($ops == 'expired'){
              $mailbody = <<<MAILBODY
已向 $user_name <$user_mobile_num>  退款 $amount 元，购买的 抢号神器 的服务费。 收款凭据: $charge_id 。
<br />
<br />
付款渠道：$channel
<br />
<br />
订单编号：$r_kkid <br />
MAILBODY;
           }


                Util_SmtpMail::send_qq(
                  $to,
                  $title,
                  $mailbody,
                  $bcc
                  );
 //Util_SmtpMail::send_qq($to,$subject,$body,$cc,$reply);
                $ret = array(
                    'status' => true,
                    'msg' => 'ok'
                );
            } catch (Exception $e) {
                $ret = array(
                    'status' => false,
                    'msg' => $e
                );
            }
            return;
        }
}
