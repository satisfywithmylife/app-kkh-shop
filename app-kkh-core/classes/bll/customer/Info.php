<?php

class  Bll_Customer_Info {
	private $customerInfoDao;

	public function __construct() {
		$this->customerInfoDao = new Dao_Customer_Info();
	}


        public function create_customer($data) {
                if(empty($data)) return array();
                return $this->customerInfoDao->create_customer($data);
        }

        public function set_customer($data) {
                if(empty($data)) return array();
                return $this->customerInfoDao->set_customer($data);
        }

        public function get_customer($id_customer) {
                if(empty($id_customer)) return array();
                return $this->customerInfoDao->get_customer($id_customer);
        }

        public function get_customer_by_u_kkid($u_kkid) {
                if(empty($u_kkid)) return array();
                return $this->customerInfoDao->get_customer_by_u_kkid($u_kkid);
        }

        public function get_customer_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->customerInfoDao->get_customer_by_kkid($kkid);
        }
        public function get_customer_by_id_address($id_address) {
                if(empty($id_address)) return array();
                $customer = array();
                $address = $this->customerInfoDao->get_customer_by_id_address($id_address);
                if(isset($address['id_customer']) && !empty($address['id_customer'])){
                   $customer = $this->customerInfoDao->get_customer($address['id_customer']);
                   $customer['id_customer'] = $address['id_customer'];
                }
                return $customer;
        }
}
