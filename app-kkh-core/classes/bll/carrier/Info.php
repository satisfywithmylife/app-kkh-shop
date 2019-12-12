<?php

class  Bll_carrier_Info {
	private $carrierInfoDao;

	public function __construct() {
		$this->carrierInfoDao = new Dao_Carrier_Info();
	}


        public function get_carrier_fee($name) {
                if(empty($name)) return array();
                return $this->carrierInfoDao->get_carrier_fee($name);
        }

}
