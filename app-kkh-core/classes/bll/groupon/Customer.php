<?php

class  Bll_Groupon_Customer {
	    private $grouponCustomerDao;

	    public function __construct() {
		        $this->grouponCustomerDao = new Dao_Groupon_Customer();
	    }

        public function create_customer($data) {
                if(empty($data)) return array();
                return $this->grouponCustomerDao->create_customer($data);
        }

        public function set_customer($data) {
                if(empty($data)) return array();
                return $this->grouponCustomerDao->set_customer($data);
        }

        public function get_customer($id) {
                if(empty($id)) return array();
                return $this->grouponCustomerDao->get_customer($id);
        }

        public function get_customer_by_kkid($kkid) {
                if(empty($kkid)) return array();
                $order = $this->grouponCustomerDao->get_customer_by_kkid($kkid);
                if(isset($order['g_kkid']) && !empty($order['g_kkid'])){
                       $bll_groupon = new Bll_Groupon_Info();
                       $order['groupon_info'] = $bll_groupon->get_groupon_by_kkid($order['g_kkid']);
                       unset($order['groupon_info']['admin_user']);
                }
                return $order;
        }

        public function get_groupon_customer_list($m_kkid, $current_state, $limit, $offset) {
                if(!is_numeric($limit) || !is_numeric($offset)) return array();
                if(empty($m_kkid) || empty($current_state)) return array();
                $order_list = $this->grouponCustomerDao->get_groupon_customer_list($m_kkid, $current_state, $limit, $offset);
                $bll_groupon = new Bll_Groupon_Info();
                $bll_user = new Bll_User_UserInfoUC();
                foreach($order_list as $k=>$j){
                    /*  */
                    if(isset($j['g_kkid']) && !empty($j['g_kkid']) && isset($j['m_kkid']) && empty($j['m_kkid'])){
                       $j['groupon_info'] = $bll_groupon->get_groupon_by_kkid($j['g_kkid']);
                       unset($j['groupon_info']['admin_user']);
                    }
                    if(isset($j['c_kkid']) && !empty($j['c_kkid'])){
                       $base_info = $bll_user->get_user_by_kkid($j['c_kkid']);
                       if(isset($base_info['picture']) && strlen($base_info['picture']) == 32){
                           $base_info['picture_url'] = IMG_CDN_USER . strtolower($base_info['picture']) . "/" . "headpic.jpg";
                       }
                       if(isset($base_info['wechat_photo_url']) && !empty($base_info['wechat_photo_url'])){
                           if( $base_info['wechat_photo_url'] != "undefined" ){
                               $base_info['picture_url'] = $base_info['wechat_photo_url'];
                           }
                       }
                       $j['customer'] = array(
                                                 'kkid' => $base_info['kkid'],
                                                 'name' => $base_info['name'],
                                                 'picture_url' => $base_info['picture_url'],
                                                 'user_photo' => $base_info['user_photo'],
                                                 'wechat_photo_url' => $base_info['wechat_photo_url'],
                                              );
                    }
                    /*  */
                    $order_list[$k] = $j;
                }

                return $order_list;
        }

        public function get_groupon_my_list($u_kkid, $current_state, $limit, $offset) {
                if(!is_numeric($limit) || !is_numeric($offset)) return array();
                if(empty($u_kkid) || empty($current_state)) return array();
                $order_list = $this->grouponCustomerDao->get_groupon_my_list($u_kkid, $current_state, $limit, $offset);
                $bll_groupon = new Bll_Groupon_Info();
                foreach($order_list as $k=>$j){
                    /*  */
                    if(isset($j['g_kkid']) && !empty($j['g_kkid'])){
                       $j['groupon_info'] = $bll_groupon->get_groupon_by_kkid($j['g_kkid']);
                       unset($j['groupon_info']['admin_user']);
                    }
                    /*  */
                    $order_list[$k] = $j;
                }

                return $order_list;
        }

        public function get_groupon_customer_count($m_kkid, $current_state) {
                if(empty($m_kkid) || empty($current_state)) return array();
                return $this->grouponCustomerDao->get_groupon_customer_count($m_kkid, $current_state);
        }

        public function get_groupon_my_count($u_kkid, $current_state) {
                if(empty($u_kkid) || empty($current_state)) return array();
                return $this->grouponCustomerDao->get_groupon_my_count($u_kkid, $current_state);
        }

        public function set_order_paystatus_by_kkid($o_kkid, $c_kkid, $current_state) {
                if(empty($o_kkid) || empty($c_kkid)) return array();
                return $this->grouponCustomerDao->set_order_paystatus_by_kkid($o_kkid, $c_kkid, $current_state);
        }   

        public function mail_pay_notifiaction($r_kkid, $ch, $res, $ops, $to, $bcc)
        {   
            $title = "【通知】用户付款";

            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($ch, true));
            Logger::info(__FILE__, __CLASS__, __LINE__, var_export($res, true));

            if($ops == 'paid'){
               $title = "【通知】用户付款 - 团购";
            }   

            $r_kkid = substr($r_kkid,0,8);

            $user_name = ""; 
            $mobile_num = ""; 
            if(isset($res['user_info']) && !empty($res['user_info'])){
                $user_info = $res['user_info'];
                $user_name = $user_info['name'];
                $mobile_num = $user_info['mobile_num'];
            }
            $bll_groupon = new Bll_Groupon_Info();
            $groupon_info = $bll_groupon->get_groupon_by_kkid($res['g_kkid']);
            $product_info = $groupon_info['product_info'];
            $product_name = $product_info['name'];
    
            $now_date = date("Y-m-d");
            $time_paid = isset($ch['time_paid']) && !empty($ch['time_paid']) ? date('Y-m-d H:i:s', $ch['time_paid']) : date('Y-m-d H:i:s');
            $charge_id = isset($ch['id']) && !empty($ch['id']) ?  $ch['id'] : 'charge_id';
            $amount = isset($ch['amount']) && !empty($ch['amount']) ? $ch['amount']/100 : '0';
            $channel = isset($ch['channel']) && !empty($ch['channel']) ? $ch['channel'] : ''; 

            try {
    

              $mailbody = <<<MAILBODY
用户：$user_name
<br />
支付金额：$amount 元
<br />
商品名称：$product_name
<br />
手机号：$mobile_num
<br />
付款渠道：$channel
<br />
收款凭据: $charge_id 
<br />
团购单号：$r_kkid <br />
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

        public function get_order_by_customer_list_admin($current_state, $page_size, $page_start) {
                if(empty($current_state)) return array();
                return $this->grouponCustomerDao->get_order_by_customer_list_admin($current_state, $page_size, $page_start);
        }


	//根据gkkid获取用户信息
	public function get_customer_by_gkkid($g_kkid)
	{
	    if(empty($g_kkid)) return array();
		return $this->grouponCustomerDao->get_customer_by_gkkid($g_kkid);
	}
    //获取拼团人数 $gkkid = 拼团id
	public function get_limit_time_customer_count($gkkid)
	{
	    if(!$gkkid) return array();
		return $this->grouponCustomerDao->get_limit_time_customer_count($gkkid);
	}


}
