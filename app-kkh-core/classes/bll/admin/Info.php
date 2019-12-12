<?php

class  Bll_Admin_Info {

	private $adminInfoDao;

	public function __construct() {
		$this->adminInfoDao = new Dao_Admin_Info();
	}
	
	public function get_admin_info_by_uid($uid) {
		if (!$uid) return array();
		return $this->adminInfoDao->get_admin_info_by_uid($uid);
	}
	
	public function check_user_exsite($username) {
		if(empty($username)) return array();
		return $this->adminInfoDao->check_user_exsite($username);
	}
	
	public function register($data) {
		if (empty($data)) return false;
		return  $this->adminInfoDao->register($data);
	}

	public function login($data) {
		if (empty($data['username']) || empty($data['password'])) return array();
		return $this->adminInfoDao->login($data);	
	}
	
	public function check_user_role($username, $access_token){
		if (empty($username) || empty($access_token)) return array();
		return $this->adminInfoDao->check_user_role($username, $access_token);
	}

	public function change_time_formate_list($data){
		if(!$data) return array();
		foreach($data as $k=>$v){
			if(!$v){
				continue;
			}
			$v = self::change_time_formate($v);
			$data[$k] = $v;
		}
		return $data;
	}

	public function change_time_formate($data){
		$s = array('created_at', 'updated_at');
		if(!$data) return array();
		foreach($data as $k=>$v){
			if(!in_array($k, $s, true)){
				continue;
			}
			$v = date('Y-m-d H:i:s', $v);
			$data[$k] = $v;
		}

		return $data;
	}
}
