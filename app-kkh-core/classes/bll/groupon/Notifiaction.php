<?php

class  Bll_Groupon_Notifiaction {
	    private $grouponNotifiactionDao;

	    public function __construct() {
		        $this->grouponNotifiactionDao = new Dao_Groupon_Notifiaction();
	    }

        public function create_notifiaction($data) {
                if(empty($data)) return array();
                return $this->grouponNotifiactionDao->create_notifiaction($data);
        }

        public function set_notifiaction($data) {
                if(empty($data)) return array();
                return $this->grouponNotifiactionDao->set_notifiaction($data);
        }

        public function get_notifiaction($id) {
                if(empty($id)) return array();
                return $this->grouponNotifiactionDao->get_notifiaction($id);
        }

        public function get_notifiaction_by_kkid($kkid) {
                if(empty($kkid)) return array();
                return $this->grouponNotifiactionDao->get_notifiaction_by_kkid($kkid);
        }

}
