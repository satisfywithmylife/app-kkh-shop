<?php

class  Bll_Coupon_Info {
	private $couponInfoDao;

	public function __construct() {
		$this->couponInfoDao = new Dao_coupon_Info();
	}

        public function set_coupon_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->couponInfoDao->set_coupon_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function cancel_coupon_by_kkid($r_kkid, $u_kkid, $data) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->couponInfoDao->cancel_coupon_by_kkid($r_kkid, $u_kkid, $data);
        }

        public function set_coupon_paystatus_by_kkid($r_kkid, $u_kkid, $status) {
                if(empty($r_kkid) || empty($u_kkid)) return array();
                return $this->couponInfoDao->set_coupon_paystatus_by_kkid($r_kkid, $u_kkid, $status);
        }

        public function add_coupon($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->couponInfoDao->add_coupon($u_kkid, $data);
        }

        public function add_coupon_sk($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->couponInfoDao->add_coupon_sk($u_kkid, $data);
        }
        
        public function get_coupon($id) {
                if(empty($id)) return array();
                return $this->couponInfoDao->get_coupon($id);
        }

        public function create_coupon($id) {
                if(empty($id)) return array();
                return $this->couponInfoDao->create_coupon($id);
        }

        public function get_coupon_list($u_kkid, $limit, $offset, $av = 0)
        {
                if(empty($u_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->couponInfoDao->get_coupon_list($u_kkid, $limit, $offset, $av);
        }

        public function get_coupon_share_bouns_list()
        {
                return $this->couponInfoDao->get_coupon_share_bouns_list();
        }

        public function set_coupon_share_bouns($kkid)
        {
                return $this->couponInfoDao->set_coupon_share_bouns($kkid);
        }

        public function get_coupon_list_filter_price($u_kkid, $limit, $offset, $order_total_price)
        {
                if(empty($u_kkid) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->couponInfoDao->get_coupon_list_filter_price($u_kkid, $limit, $offset, $order_total_price);
        }

        public function get_coupon_list_filter_price_count($u_kkid, $order_total_price)
        {
                if(empty($u_kkid)){
                  return array();
                }
                return $this->couponInfoDao->get_coupon_list_filter_price_count($u_kkid, $order_total_price);
        }

        public function get_coupon_count($u_kkid, $av = 0)
        {
                return $this->couponInfoDao->get_coupon_count($u_kkid, $av);
        }

        public function get_coupon_by_kkid($c_kkid, $kkid)
        {
                if(empty($c_kkid)) {
                    return array();
                }
                return $this->couponInfoDao->get_coupon_by_kkid($c_kkid, $kkid);
        }

        public function mv_coupon_code($u_kkid, $mobile)
        {
                if(empty($u_kkid) || empty($mobile)) {
                    return array();
                }
                return $this->couponInfoDao->mv_coupon_code($u_kkid, $mobile);
        }

		public function get_coupon_value_by_kkid($c_kkid)
		{
				if(empty($c_kkid)) {
					return 0;
				}
				return $this->couponInfoDao->get_coupon_value_by_kkid($c_kkid);
		}

}
