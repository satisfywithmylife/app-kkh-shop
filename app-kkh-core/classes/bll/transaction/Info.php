<?php

class  Bll_Transaction_Info {
	private $transactionInfoDao;

	public function __construct() {
		$this->transactionInfoDao = new Dao_Transaction_Info();
	}

        public function set_transaction_by_kkid($u_kkid, $t_kkid, $data) {
                if(empty($u_kkid) || empty($t_kkid)) return array();
                return $this->transactionInfoDao->set_transaction_by_kkid($u_kkid, $t_kkid, $data);
        }

        public function add_transaction($u_kkid, $data) {
                if(empty($u_kkid)) return array();
                return $this->transactionInfoDao->add_transaction($u_kkid, $data);
        }

        public function get_transaction($t_kkid) {
                if(empty($t_kkid)) return array();
                return $this->transactionInfoDao->get_transaction($t_kkid);
        }

}
