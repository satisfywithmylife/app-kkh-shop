<?php

class  Bll_Hospital_KkhInfo {
	private $hospitalInfoDao;

	public function __construct() {
		$this->hospitalInfoDao = new Dao_Hospital_KkhInfo();
	}

        public function create_hospital($data) {
                if(empty($data)) return array();
                return $this->hospitalInfoDao->create_hospital($data);
        }

        public function set_hospital($data) {
                if(empty($data)) return array();
                return $this->hospitalInfoDao->set_hospital($data);
        }

        public function get_hospital($id) {
                if(empty($id)) return array();
                return $this->hospitalInfoDao->get_hospital($id);
        }

        public function get_hospital_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->hospitalInfoDao->get_hospital_by_kkid($kkid);
        }

}
