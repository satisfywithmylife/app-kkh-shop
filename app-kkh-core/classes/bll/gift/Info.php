<?php

class  Bll_Gift_Info {
	private $giftInfoDao;

	public function __construct() {
		$this->giftInfoDao = new Dao_Gift_Info();
	}

        public function create_gift($data) {
                if(empty($data)) return array();
                return $this->giftInfoDao->create_gift($data);
        }

        public function set_gift($data) {
                if(empty($data)) return array();
                return $this->giftInfoDao->set_gift($data);
        }

        public function get_gift($id) {
                if(empty($id)) return array();
                return $this->giftInfoDao->get_gift($id);
        }

        public function get_gift_by_userid($user_id) {
                if(empty($user_id)) return array();
                return $this->giftInfoDao->get_gift_by_userid($user_id);
        }
/*
*/
     /* user_gift_entry */
/*
*/
        public function create_gift_entry($data) {
                if(empty($data)) return array();
                return $this->giftInfoDao->create_gift_entry($data);
        }

        public function set_gift_entry($data) {
                if(empty($data)) return array();
                return $this->giftInfoDao->set_gift_entry($data);
        }

        public function get_gift_entry($id) {
                if(empty($id)) return array();
                return $this->giftInfoDao->get_gift_entry($id);
        }

        public function get_gift_entry_by_userid($user_id) {
                if(empty($user_id)) return array();
                return $this->giftInfoDao->get_gift_entry_by_userid($user_id);
        }

}
