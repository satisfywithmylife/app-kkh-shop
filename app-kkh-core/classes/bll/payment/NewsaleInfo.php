<?php

class  Bll_Payment_NewsaleInfo {
	    private $dao;

	    public function __construct() {
		        $this->dao = new Dao_Payment_NewsaleInfo();
	    }

        public function set_payment_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->paymentGrouponInfoDao->set_payment_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function set_payment_refund_by_id($data) {
                return $this->paymentGrouponInfoDao->set_payment_refund_by_id($data);
        }

        public function set_payment_refund_msg_by_id($data) {
                return $this->paymentGrouponInfoDao->set_payment_refund_msg_by_id($data);
        }   


        public function set_payment_status($charge_id, $status, $time_paid) {
                if(empty($charge_id) || empty($status)) return array();
                return $this->paymentGrouponInfoDao->set_payment_status($charge_id, $status, $time_paid);
        }

        public function create_payment_charge($data) {
                return $this->dao->create_payment_charge($data);
        }
        
        public function get_payment($r_kkid, $u_kkid) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->paymentGrouponInfoDao->get_payment($r_kkid, $u_kkid);
        }

        public function get_payment_charge_list($r_kkid, $limit, $offset)
        {
                if(empty($r_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->dao->get_payment_charge_list($r_kkid, $limit, $offset);
        }

        public function get_payment_charge_list_paid($r_kkid)
        {
                if( empty($r_kkid) ){
                  return array();
                }
                return $this->paymentGrouponInfoDao->get_payment_charge_list_paid($r_kkid);
        }

        public function get_payment_count($loc_code)
        {
                if(empty($loc_code)) {
                    return array();
                }
                return $this->paymentGrouponInfoDao->get_payment_count($loc_code);
        }

        public function get_location($kkid)
        {
                if(empty($kkid)) {
                    return array();
                }
                return $this->paymentGrouponInfoDao->get_location($kkid);
        }

}
