<?php

class  Bll_Groupon_Payment {
	    private $grouponPaymentDao;

	    public function __construct() {
		    $this->grouponPaymentDao = new Dao_Groupon_Payment();
	    }

        public function set_payment_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->grouponPaymentDao->set_payment_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function set_payment_refund_by_id($data) {
                return $this->grouponPaymentDao->set_payment_refund_by_id($data);
        }

        public function set_payment_status($charge_id, $status, $time_paid) {
                if(empty($charge_id) || empty($status)) return array();
                return $this->grouponPaymentDao->set_payment_status($charge_id, $status, $time_paid);
        }

        public function create_payment_charge($data) {
                return $this->grouponPaymentDao->create_payment_charge($data);
        }
        
        public function get_payment($r_kkid, $u_kkid) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->grouponPaymentDao->get_payment($r_kkid, $u_kkid);
        }

        public function get_payment_charge_list($r_kkid, $limit, $offset)
        {
                if(empty($r_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->grouponPaymentDao->get_payment_charge_list($r_kkid, $limit, $offset);
        }

        public function get_payment_charge_list_by_id_customer_group($r_kkid, $limit, $offset)
        {   
                if(empty($r_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }   
                return $this->grouponPaymentDao->get_payment_charge_list_by_id_customer_group($r_kkid, $limit, $offset);
        }   


        public function get_payment_charge_list_paid($r_kkid)
        {
                if( empty($r_kkid) ){
                  return array();
                }
                return $this->grouponPaymentDao->get_payment_charge_list_paid($r_kkid);
        }

        public function get_payment_charge_list_paid_id($id_customer_group)
        {   
                if( empty($id_customer_group) ){
                  return array();
                }   
                return $this->grouponPaymentDao->get_payment_charge_list_paid_id($id_customer_group);
        }   


}
