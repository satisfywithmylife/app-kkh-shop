<?php
apf_require_class("APF_DB_Factory");

class Dao_User_UserTemp {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("usercenter_master");
	}

	public function get_temp_user_by_min_openid($min_openid){
		$sql = "select * from t_temp_users where min_openid = ? order by uid desc limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($min_openid));
		return $stmt->fetch();
	}

	public function create_temp_user($data){
		$sql = "insert into t_temp_users (kkid ,user_token, min_openid, created_at, updated_at)values(:kkid ,:user_token, :min_openid, :created_at, :updated_at);";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$uid = $stmt->lastinsertid();
		
		$user = self::get_temp_user_by_uid($uid);
		return $user;
	}

	public function get_temp_user_by_uid($uid){
		if(!$uid) return array();
		$sql = "select * from t_temp_users where uid = ? limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute(array($uid));
		return $res;
	}

	public function verify_user_access_token($kkid, $token){
		if(!$kkid || !$token) return array();
		$data = [
			'kkid' => $kkid,
			'user_token' => $token,
		];
		$sql = "select * from t_temp_users where kkid = :kkid and user_token = :user_token limit 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($data);
		$res = $stmt->fetch();
		if($res){
			return true;
		}else{
			return false;
		}
	}
}
