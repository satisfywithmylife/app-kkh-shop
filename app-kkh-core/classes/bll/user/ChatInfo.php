<?php

class  Bll_User_ChatInfo {
	private $userInfoDao;

	public function __construct() {
		$this->userInfoDao = new Dao_User_ChatInfo();
	}

        public function create_user($data) {
                if(empty($data)) return array();
                return $this->userInfoDao->create_user($data);
        }

        public function set_user($data) {
                if(empty($data)) return array();
                return $this->userInfoDao->set_user($data);
        }

        public function get_user($id) {
                if(empty($id)) return array();
                return $this->userInfoDao->get_user($id);
        }

        public function get_user_by_username($username) {
                if(empty($username)) return array();
                return $this->userInfoDao->get_user_by_username($username);
        }

}
