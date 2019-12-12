<?php

class  Bll_Guest_Info {
	private $guestInfoDao;

	public function __construct() {
		$this->guestInfoDao = new Dao_Guest_Info();
	}


        public function create_guest($data) {
                if(empty($data)) return array();
                return $this->guestInfoDao->create_guest($data);
        }

        public function get_guest($id_guest) {
                if(empty($id_guest)) return array();
                return $this->guestInfoDao->get_guest($id_guest);
        }

        public function get_guest_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->guestInfoDao->get_guest_by_kkid($kkid);
        }
}
