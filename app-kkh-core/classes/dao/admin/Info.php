<?php
apf_require_class("APF_DB_Factory");

class Dao_Admin_Info {
  
  	private $pdo;

  	public function __construct() {
   		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("admin_master"); // admin master
  	}
	public function get_admin_info_by_uid($uid){
		$sql = "select * from t_admin_user where uid = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array("$uid"));
        $res = $stmt->fetch();
        return $res;		
	}
	
	public function check_user_exsite($username) {
		$sql = "select * from t_admin_user where username = ?;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array("$username"));
		$res = $stmt->fetch();
		return $res;
	}
	
	public function register($data) {
		$data['created_at'] = $data['updated_at'] = time();
		$sql  = "insert into t_admin_user (username, name, password, email, mobile, info, salt, status, created_at, updated_at, groupid)values(:username, :name, :password, :email, :mobile, :info, :salt, :status, :created_at, :updated_at, :groupid);";
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($data, true));
		$stmt = $this->pdo->prepare($sql);
		$res = $stmt->execute($data);
		$hid = $this->pdo->lastInsertId();
		if (!$hid) {
			$hid = 0;
		}

		return $hid;
	}

	public function check_user_role($username, $access_token){
		$row = array();
		$sql = "select * from t_admin_user where username = ? and password = ? and status = 1;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($username, $access_token));
		$row = $stmt->fetch();
		if (empty($row)) {
			return array();
		}
		return $row;
	}
	
	public function login($data) {
		$m = self::check_user_exsite($data['username']);
		
		if (!$m) {
			return array();
		}
		Logger::info(__FILE__, __CLASS__, __LINE__, var_export($m, true));
		$real_password = $m['password'];
		$curr_password = md5(md5($data['password']).$m['salt']);
		Logger::info(__FILE__, __CLASS__, __LINE__, "$real_password, $curr_password");
		unset($m['salt']);

		if($real_password != $curr_password){
			return array();
		}

		return $m;
	}

	public function get_needed_info() {
		$sql = "select * from t_admin_user where username = ?;";
	}
	public function update($data) {
		$sql = "update t_admin_user set ";
		foreach ($data as $k=>$v) {
			if (!empty($v)) {
				$sql .= "$vi" . " = " . ":$v, "; 
			}
		}
		$stmt = $stmt->pdo->prepare($sql);
		$res = $stmt->execute($data);

		return $res;
	}

	public function del($id) {
		$sql = "update t_admin_user status = 0 where uid = ?";
		$stmt = $stmt->pdo->prepare($sql);
		$res = $stmt->execute(array($id));

		return $res;
	}
}
