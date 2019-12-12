<?php

class  Bll_Coupon_FshareInfo {
	private $couponFshareInfoDao;

	public function __construct() {
		$this->couponFshareInfoDao = new Dao_Coupon_FshareInfo();
	}

        public function create_share_coupon($data) {
                if(empty($data)) return array();
                return $this->couponFshareInfoDao->create_share_coupon($data);
        }

        public function set_share_coupon($data) {
                if(empty($data)) return array();
                return $this->couponFshareInfoDao->set_share_coupon($data);
        }

        public function get_share_coupon($id) {
                if(empty($id)) return array();
                return $this->couponFshareInfoDao->get_share_coupon($id);
        }

        public function check_coupon_exist($sender, $receiver) {
                if(empty($sender) || empty($receiver)) return array();
                return $this->couponFshareInfoDao->check_coupon_exist($sender, $receiver);
        }

        public function check_coupon_exist_adv($sender, $receiver, $ver=1) {
                if(empty($sender) || empty($receiver)) return array();
                return $this->couponFshareInfoDao->check_coupon_exist_adv($sender, $receiver, $ver);
        }

        public function get_share_coupon_total_value($receiver, $ver=1) {
                if(empty($receiver)) return array();
                return $this->couponFshareInfoDao->get_share_coupon_total_value($receiver, $ver);
        }
}
