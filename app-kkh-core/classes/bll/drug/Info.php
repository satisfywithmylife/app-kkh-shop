<?php

class  Bll_Drug_Info {
	private $drugInfoDao;

	public function __construct() {
		$this->drugInfoDao = new Dao_drug_Info();
	}

        public function set_drug_by_kkid($u_kkid, $d_kkid, $data) {
                if(empty($u_kkid) || empty($d_kkid)) return array();
                return $this->drugInfoDao->set_drug_by_kkid($u_kkid, $d_kkid, $data);
        }

        public function add_drug($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->drugInfoDao->add_drug($u_kkid, $data);
        }

        public function get_drug($d_kkid, $u_kkid = '') {
                if(empty($d_kkid)) return array();
                return $this->drugInfoDao->get_drug($d_kkid , $u_kkid);
        }

        public function get_drug_list($limit, $offset, $u_kkid = '')
        {
                return $this->drugInfoDao->get_drug_list($limit, $offset, $u_kkid);
        }
 
        public function get_drug_count()
        {
                return $this->drugInfoDao->get_drug_count();
        }


}
