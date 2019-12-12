<?php

class  Bll_Department_Info {
	private $departmentInfoDao;

	public function __construct() {
		$this->departmentInfoDao = new Dao_department_Info();
	}

        
        public function get_department($hd_kkid) {
                if(empty($hd_kkid)) return array();
                return $this->departmentInfoDao->get_department($hd_kkid);
        }

}
