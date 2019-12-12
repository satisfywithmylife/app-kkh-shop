<?php

class  Bll_Hospital_Info {
	private $hospitalInfoDao;

	public function __construct() {
		$this->hospitalInfoDao = new Dao_Hospital_Info();
	}

        public function set_hospital_by_kkid($u_kkid, $h_kkid, $data) {
                if(empty($u_kkid) || empty($h_kkid)) return array();
                $sk_kkid =  $this->hospitalInfoDao->get_hospital_sk_by_hkkid($h_kkid);
                Logger::info(__FILE__, __CLASS__, __LINE__, "sk_kkid: $sk_kkid");
                Logger::info(__FILE__, __CLASS__, __LINE__, "kkid: $h_kkid");
                if(empty($sk_kkid)){
                    //backup v1 data
                    $backup = $this->hospitalInfoDao->get_hospital_by_kkid($h_kkid); 
                    if(!empty($backup)){
                       Logger::info(__FILE__, __CLASS__, __LINE__, var_export($backup, true));
                       $this->hospitalInfoDao->add_hospital_sk('sysops', $backup);
                    }
                }
                return $this->hospitalInfoDao->set_hospital_by_kkid($u_kkid, $h_kkid, $data);
        }

        public function add_hospital($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->hospitalInfoDao->add_hospital($u_kkid, $data);
        }

        public function add_hospital_sk($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->hospitalInfoDao->add_hospital_sk($u_kkid, $data);
        }
        
        public function get_hospital($h_kkid, $u_kkid = '') {
                if(empty($h_kkid)) return array();
                return $this->hospitalInfoDao->get_hospital($h_kkid, $u_kkid);
        }

        public function get_hospital_list($loc_code, $limit, $offset)
        {
                if(empty($loc_code) || !is_numeric($limit) || !is_numeric($offset)){
                  return array();
                }
                return $this->hospitalInfoDao->get_hospital_list($loc_code, $limit, $offset);
        }

        public function get_hospital_count($loc_code)
        {
                if(empty($loc_code)) {
                    return array();
                }
                return $this->hospitalInfoDao->get_hospital_count($loc_code);
        }

        public function get_location($kkid)
        {
                if(empty($kkid)) {
                    return array();
                }
                return $this->hospitalInfoDao->get_location($kkid);
        }

}
