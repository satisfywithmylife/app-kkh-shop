<?php
apf_require_class("APF_DB_Factory");

class Dao_Patient_Info {

	private $pdo;

	public function __construct() {
		$this->pdo = APF_DB_Factory::get_instance()->get_pdo("green_master");
	}

        public function create_patient($data) {
                $sql = "insert into `patient` (`id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `age`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `device_token`, `push_user_id`, `push_channel_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `extra`, `weixin_id`, `wx_openid`, `internal_remark`, `ad_id`, `kkid`, `user_token`) values(:id, :user_type, :sex, :password, :phone_num, :real_name, :header_pic, :register_time, :age, :last_update_time, :pwd_update_time, :pwd_create_time, :device_token, :push_user_id, :push_channel_id, :last_login_time, :app_version, :device_type, :device_id, :os, :os_version, :channel, :is_login, :extra, :weixin_id, :wx_openid, :internal_remark, :ad_id, :kkid, :user_token);";
		Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                $last_id = $this->pdo->lastInsertId();
                return $last_id;
        }

        public function set_patient($data) {
                $sql = "update `patient` set `user_type` = :user_type, `sex` = :sex, `password` = :password, `phone_num` = :phone_num, `real_name` = :real_name, `header_pic` = :header_pic, `register_time` = :register_time, `age` = :age, `last_update_time` = :last_update_time, `pwd_update_time` = :pwd_update_time, `pwd_create_time` = :pwd_create_time, `device_token` = :device_token, `push_user_id` = :push_user_id, `push_channel_id` = :push_channel_id, `last_login_time` = :last_login_time, `app_version` = :app_version, `device_type` = :device_type, `device_id` = :device_id, `os` = :os, `os_version` = :os_version, `channel` = :channel, `is_login` = :is_login, `region_id` = :region_id, `extra` = :extra, `weixin_id` = :weixin_id, `wx_openid` = :wx_openid, `internal_remark` = :internal_remark, `ad_id` = :ad_id, `kkid` = :kkid, `user_token` = :user_token where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function set_patient_ease($data) {
                $sql = "update `patient` set `kkid` = :kkid, `user_token` = :user_token where `id` = :id ;";
                $stmt = $this->pdo->prepare($sql);
                $res = $stmt->execute($data);
                return $res;
        }

        public function get_patient($id) {
                $row = array();
                $sql = "select `id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `age`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `device_token`, `push_user_id`, `push_channel_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `region_id`, `extra`, `weixin_id`, `wx_openid`, `internal_remark`, `ad_id`, `kkid`, `user_token` from `patient` where `id` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$id"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_patient_by_kkid($kkid) {
                $row = array();
                $sql = "select `id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `age`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `device_token`, `push_user_id`, `push_channel_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `region_id`, `extra`, `weixin_id`, `wx_openid`, `internal_remark`, `ad_id`, `kkid`, `user_token` from `patient` where `kkid` = ? ;";
                //Logger::info(__FILE__, __CLASS__, __LINE__, $sql);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array("$kkid"));
                $row = $stmt->fetch();
                if(empty($row)){
                   $row = array();
                }
                return $row;
        }

        public function get_patient_by_wx_info($wx_openid, $wx_unionid) {
                $row = array();
                $sql = "select `id`, `user_type`, `sex`, `password`, `phone_num`, `real_name`, `header_pic`, `register_time`, `age`, `last_update_time`, `pwd_update_time`, `pwd_create_time`, `device_token`, `push_user_id`, `push_channel_id`, `last_login_time`, `app_version`, `device_type`, `device_id`, `os`, `os_version`, `channel`, `is_login`, `region_id`, `extra`, `weixin_id`, `wx_openid`, `internal_remark`, `ad_id`, `kkid`, `user_token` from `patient` where `wx_openid` = ? order by `id` desc limit 1;";
		Logger::info(__FILE__, __CLASS__, __LINE__, $sql); 
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(array($wx_openid));
                $row = $stmt->fetch();
                if(empty($row)){
                    $row = array();
                }
                return $row;
        }

/*
*/

}
