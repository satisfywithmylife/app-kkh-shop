<?php

class  Bll_Order_Info {
	private $orderInfoDao;

	public function __construct() {
		$this->orderInfoDao = new Dao_Order_Info();
	}

		public function get_order_detail_list($id_order){
				if(!$id_order) return array();
				return $this->orderInfoDao->get_order_detail_list($id_order);
		}

        public function create_order($data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->create_order($data);
        }
		
		public function set_order_status_by_id_order($id_order, $state){
				if(!$id_order || !$state) return array();
				return $this->orderInfoDao->set_order_status_by_id_order($id_order, $state);
		}
	
		public function set_order($data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->set_order($data);
        }

        public function get_order($id_order) {
                if(empty($id_order)) return array();
                return $this->orderInfoDao->get_order($id_order);
        }

        public function get_order_by_customer($o_kkid, $id_customer) {
                if(empty($id_customer) || empty($o_kkid)) return array();
                return $this->orderInfoDao->get_order_by_customer($o_kkid, $id_customer);
        }

		public function get_expired_product_list($id_customer, $o_kkid, $type) {
				if(!$id_customer || !$o_kkid) return array();
				return $this->orderInfoDao->get_expired_product_list($id_customer, $o_kkid, $type); 
		}

		public function set_pick_status($data){
				if(!$data) return array();
				return $this->orderInfoDao->set_pick_status($data);
		}

		public function set_payment_way($o_kkid, $channel){
				if(!$o_kkid || !$channel) return array();
				return $this->orderInfoDao->set_payment_way($o_kkid, $channel);
		}
		
        public function get_order_by_customer_list($id_customer, $current_state, $page_size, $page_start) {
                if(empty($id_customer)) return array();
                return $this->orderInfoDao->get_order_by_customer_list($id_customer, $current_state, $page_size, $page_start);
        }

        public function get_order_by_customer_list_admin($current_state, $page_size, $page_start) {
                if(empty($current_state)) return array();
                return $this->orderInfoDao->get_order_by_customer_list_admin($current_state, $page_size, $page_start);
        }

        public function get_order_product($id_order, $id_product) {
                if(empty($id_order) || empty($id_product)) return array();
                return $this->orderInfoDao->get_order_product($id_order, $id_product);
        }

		public function get_order_info_by_order_no($order_no) {
				if (empty($order_no)) return false;
				return $this->orderInfoDao->get_order_info_by_order_no($order_no);
		}

        public function get_gorder_info_by_order_no($order_no) {
                if (empty($order_no)) return false;
                return $this->orderInfoDao->get_gorder_info_by_order_no($order_no);
        }   

		public function change_gorder_state_by_id_customer_group($id_customer_group) {
                if (empty($id_customer_group)) return false;
                return $this->orderInfoDao->change_gorder_state_by_id_customer_group($id_customer_group);
        }   

        public function change_gorder_state_by_pid($pid) {
                if (!$pid) return false;
                return $this->orderInfoDao->change_gorder_state_by_pid($pid);
        }

		public function change_order_state_by_o_kkid($o_kkid) {
				if (empty($o_kkid)) return false;
				return $this->orderInfoDao->change_order_state_by_o_kkid($o_kkid);
		}

		public function change_order_state_by_pid($pid) {
				if (!$pid) return false;
				return $this->orderInfoDao->change_order_state_by_pid($pid);
		}

        public function add_order_product($data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->add_order_product($data);
        }

        public function set_order_product($id_order, $id_product, $data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->set_order_product($id_order, $id_product, $data);
        }

        public function del_order_product($id_order, $id_product) {
                if(empty($id_order) || empty($id_product)) return array();
                return $this->orderInfoDao->del_order_product($id_order, $id_product);
        }

        public function create_order_detail($data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->create_order_detail($data);
        }

        public function create_order_history($data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->create_order_history($data);
        }

        public function get_order_detail($id_order_detail) {
                if(empty($id_order_detail)) return array();
                return $this->orderInfoDao->get_order_detail($id_order_detail);
        }

        public function create_order_address($data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->create_order_address($data);
        }

        public function set_order_address($id_customer, $data) {
                if(empty($data)) return array();
                return $this->orderInfoDao->set_order_address($id_customer, $data);
        }

        public function get_order_address($id_address) {
                if(empty($id_address)) return array();
                return $this->orderInfoDao->get_order_address($id_address);
        }

        public function get_order_address_by_customer_list($id_customer) {
                if(empty($id_customer)) return array();
                return $this->orderInfoDao->get_order_address_by_customer_list($id_customer);
        }
        public function get_product_attribute_list($id_product, $id_product_attribute) {
                if(empty($id_product) || empty($id_product_attribute)) return array();
                if($id_product_attribute == 0) return array();
                return $this->orderInfoDao->get_product_attribute_list($id_product, $id_product_attribute);
        }
        public function set_order_paystatus_by_kkid($o_kkid, $id_customer, $current_state) {
                if(empty($o_kkid) || empty($id_customer)) return array();
                return $this->orderInfoDao->set_order_paystatus_by_kkid($o_kkid, $id_customer, $current_state);
        }
        public function add_coupon_to_order($o_kkid, $id_customer, $coupon) {
                if(empty($o_kkid) || empty($id_customer) || empty($coupon)) return array();
                return $this->orderInfoDao->add_coupon_to_order($o_kkid, $id_customer, $coupon);
        }

        public function mail_pay_notifiaction($r_kkid, $ch, $res, $ops, $to, $bcc)
        {
            $title = "【通知】用户付款";

            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($ch, true));

            if($ops == 'paid'){
               $title = "【通知】用户付款 - 新电商";
            }

            if(isset($res['kkid']) && !empty($res['kkid'])){
                //$r_kkid = substr($res['kkid'],0,8);
                $r_kkid = $res['reference'];
            }
            else{
                //$r_kkid = substr($r_kkid,0,8);
                $r_kkid = $res['reference'];
            }
            // order_product_list
            $product_list = array();
            $product_names = "";
            foreach($res['order_product_list'] as $k=>$p){
                if(empty($product_names)){
                    $product_names = $p['product_name'];
                    $product_list[$p["product_id"]]["product_name"] = $p['product_name'];
                    $product_list[$p["product_id"]]["product_quantity"] = $p['product_quantity'];
                    $product_list[$p["product_id"]]["product_price"] = $p['product_price'];
                }
                else{
                    $product_names .= " ; ".$p['product_name'];
                    $product_list[$p["product_id"]]["product_name"] = $p['product_name'];
                    $product_list[$p["product_id"]]["product_quantity"] = $p['product_quantity'];
                    $product_list[$p["product_id"]]["product_price"] = $p['product_price'];
                }
            }
            $coupon_value = $res["order"]["c_value"];

            $user_name = "";
            $mobile_num = "";
            $u_kkid = "";
            if(isset($res['user_info']) && !empty($res['user_info'])){
                $user_info = $res['user_info'];
                $user_name = $user_info['name'];
                $mobile_num = $user_info['mobile_num'];
                $u_kkid = $user_info['kkid'];
                $u_kkid = substr($u_kkid,0,8);
            }
            if(isset($res['address_delivery']) && !empty($res['address_delivery'])){
                $rec_name = $res['address_delivery']['firstname'];
                $rec_address = $res['address_delivery']['address1'];
                $rec_address_2 = $res['address_delivery']['address2'];
                $rec_code = $res['address_delivery']['postcode'];
                $rec_phone = $res['address_delivery']['phone_mobile'];
            }
            if(empty($user_name)){
                if(isset($res['address_delivery']) && !empty($res['address_delivery'])){
                    if(isset($res['address_delivery']['firstname']) && !empty($res['address_delivery']['firstname'])){ 
                        $user_name = $res['address_delivery']['firstname'];
                    }
                }
            }
			
			$doctor_str = '';
			if($res['doctor_info']){
				$doctor_info = $res['doctor_info'];
				$doctor_str = '<h1><b>推荐医生</b></h1><br>姓名(doctor_id:'.$doctor_info['doctor_id'].')：'.$doctor_info['real_name'].'<br />医院(hospital_id:'.$doctor_info['hospital_id'].')：'.$doctor_info['hospital'].'<br />科室：'.$doctor_info['department'];
			}
            $now_date = date("Y-m-d H:i:s");
            $time_paid = isset($ch['time_paid']) && !empty($ch['time_paid']) ? date('Y-m-d H:i:s', $ch['time_paid']) : date('Y-m-d H:i:s');
            $charge_id = isset($ch['id']) && !empty($ch['id']) ?  $ch['id'] : 'charge_id';
            $amount = isset($ch['amount']) && !empty($ch['amount']) ? $ch['amount']/100 : '0';
            $channel = isset($ch['channel']) && !empty($ch['channel']) ? $ch['channel'] : '';

            try {
    

            $html_th ='
<table border=1>
<tr>
    <th>商品id</th>
    <th>商品名称</th>
    <th>商品价格</th>
    <th>数量</th>
    <th>优惠券</th>
</tr>';
            foreach($product_list as $key => $value){
                $html_td = '<tr>' . 
                '<td>' . $key . '</td>' .
                '<td>' . $value['product_name'] . '</td>' .
                '<td>' . number_format($value['product_price'],2) . '</td>' .
                '<td>' . $value['product_quantity'] . '</td>' .
                '<td>' . $coupon_value . '</td>' .
                '</tr>';
            }
            $html_body = $html_th . $html_td . '</table>';

              $mailbody = <<<MAILBODY
用户: $user_name $u_kkid 
<br />
支付金额: $amount 元
<br />
$html_body
<h1><b>收件人信息：</b></h1>
收件人: $rec_name &nbsp;
电话：$rec_phone &nbsp;
地址：$rec_address  $rec_address_2
<br />
$doctor_str
<br />
<br />
<br />
订单id：$r_kkid
<br />
收款凭据：$charge_id
<br />
MAILBODY;

/*
[GP53039TS1515195656]在1月6日7时41分, 用户钱佳茗 PAT_909225支付30.00元, 购买了210 个 青苹果 (礼物 #53039).<ch_W9SGOK5mDabDmDCK8CPSGmDO>
*/



//                Util_SmtpMail::send_qq(
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
