<?php

class  Bll_Patient_Info {
	private $patientInfoDao;

	public function __construct() {
		$this->patientInfoDao = new Dao_Patient_Info();
	}

        public function create_patient($data) {
                if(empty($data)) return array();
                return $this->patientInfoDao->create_patient($data);
        }

        public function set_patient($data) {
                if(empty($data)) return array();
                return $this->patientInfoDao->set_patient($data);
        }

        public function set_patient_ease($data) {
                if(empty($data)) return array();
                return $this->patientInfoDao->set_patient_ease($data);
        }

        public function get_patient($id) {
                if(empty($id)) return array();
                return $this->patientInfoDao->get_patient($id);
        }

        public function get_patient_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->patientInfoDao->get_patient_by_kkid($kkid);
        }

        public function get_patient_by_wx_info($wx_openid, $wx_unionid) {
		Logger::info(__FILE__, __CLASS__, __LINE__, 'wx_openid:'.$wx_openid);
                if (empty($wx_openid)) {
                    return array();
                }

                return $this->patientInfoDao->get_patient_by_wx_info($wx_openid, $wx_unionid);
        }

}
