<?php

class  Bll_Doctor_Info {
	private $doctorInfoDao;

	public function __construct() {
		$this->doctorInfoDao = new Dao_doctor_Info();
	}
        
        public function get_practice_data($hd_kkid, $d_kkid, $h_kkid) {
                if(empty($hd_kkid)) return array();
                return $this->doctorInfoDao->get_practice_data($hd_kkid, $d_kkid, $h_kkid);
        }


}
