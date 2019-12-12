<?php

class  Bll_Newsale_Info {
	    private $dao;

	    public function __construct() {
		        $this->dao = new Dao_Newsale_Info();
	    }

		public function order_create($data){
				if(!$data) return array();
				return $this->dao->order_create($data);
		}
		
		public function get_order($id_order){
				if(!$id_order) return array();
				return $this->dao->get_order($id_order);
		}

        public function get_order_by_customer_list_admin($current_state, $page_size, $page_start) {
                if(empty($current_state)) return array();
                return $this->dao->get_order_by_customer_list_admin($current_state, $page_size, $page_start);
        }

        public function get_payment_charge_list($o_kkid, $page_size, $page_start) {
                if(empty($o_kkid)) return array();
                return $this->dao->get_payment_charge_list($o_kkid, $page_size, $page_start);
        }

		public function set_order_paystatus_by_kkid($kkid, $c_kkid, $state){
				if(!$kkid ||!$c_kkid) return false;
				return $this->dao->set_order_paystatus_by_kkid($kkid, $c_kkid, $state);
		}

		public function check_status($cd_key){
				if(!$cd_key) return array();
				return $this->dao->check_status($cd_key);
		}

		public function set_payment_status($charge_id, $state, $time_paid){
				return $this->dao->set_payment_status($charge_id, $state, $time_paid);
		}

		public function mail_pay_notifiaction($r_kkid, $ch, $res, $ops, $to, $bcc){
            $title = "【通知】售货机发售";

            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($ch, true));
            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

            if($ops == 'paid'){
               $title = "【通知】售货机发售 - 新零售";
            }

            $r_kkid = substr($r_kkid,0,8);
			$cd_key = $res['cd_key'];
            $user_name = "";
            if(isset($res['user_info']) && !empty($res['user_info'])){
                $user_info = $res['user_info'];
                $user_name = $user_info['cabinet_name'];
            }
            $bll_product = new Bll_Product_Info();
            $product_info = $bll_product->get_product($res['p_kkid']);
            $product_name = $product_info['name'];

            $now_date = date("Y-m-d");
            $time_paid = isset($ch['time_paid']) && !empty($ch['time_paid']) ? date('Y-m-d H:i:s', $ch['time_paid']) : date('Y-m-d H:i:s');
            $charge_id = isset($ch['id']) && !empty($ch['id']) ?  $ch['id'] : 'charge_id';
            $amount = isset($ch['amount']) && !empty($ch['amount']) ? $ch['amount']/100 : '0';
            $channel = isset($ch['channel']) && !empty($ch['channel']) ? $ch['channel'] : '';

            try {


              $mailbody = <<<MAILBODY
设备名：$user_name
<br />
设备唯一识别码: $cd_key
<br />
支付金额：$amount 元
<br />
商品名称：$product_name
<br />
付款渠道：$channel
<br />
收款凭据: $charge_id
<br />
订单单号：$r_kkid <br />
MAILBODY;



                Util_SmtpMail::send_encrypt(
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
