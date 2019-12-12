<?php

class  Bll_Department_KkhInfo {
	private $departmentInfoDao;

	public function __construct() {
		$this->departmentInfoDao = new Dao_Department_KkhInfo();
	}

        public function create_department($data) {
                if(empty($data)) return array();
                return $this->departmentInfoDao->create_department($data);
        }

        public function set_department($data) {
                if(empty($data)) return array();
                return $this->departmentInfoDao->set_department($data);
        }

        public function get_department($id) {
                if(empty($id)) return array();
                return $this->departmentInfoDao->get_department($id);
        }

        public function get_department_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->departmentInfoDao->get_department_by_kkid($kkid);
        }
}
