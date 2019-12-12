<?php
class bll_User_UserTemp {

	private $dao;

	public function __construct() {
		$this->dao = New Dao_User_UserTemp; 
	}

	public function get_temp_user_by_min_openid($min_openid){
		if(!$min_openid) return array();
		return $this->dao->get_temp_user_by_min_openid($min_openid);
	}

	public function create_temp_user($data){
		if(!$data) return array();
		return $this->dao->create_temp_user($data);
	}

	public function get_temp_user_by_uid($uid){
		if(!$uid) return array();
		return $this->dao->get_temp_user_by_uid($uid);
	}

	public function verify_user_access_token($kkid, $token){
		if(!$kkid || !$token) return array();
		return $this->dao->verify_user_access_token($kkid, $token);
	}
}
